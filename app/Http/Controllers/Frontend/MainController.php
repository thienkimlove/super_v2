<?php

namespace App\Http\Controllers\Frontend;


use App\Click;
use App\Http\Controllers\Controller;
use App\Network;
use App\NetworkClick;
use App\Offer;
use App\User;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Log;
use Countries;

class MainController extends Controller
{

    public function offer_api(Request $request)
    {
        if ($request->input('net') == 'gowide') {

            $client = new Client([
                'base_uri' => 'https://affiliate.api.gowide.com',
                'headers' => ['Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjViNmFiOGU2YzM1OTY5NmVlMjVmNzY5NCIsImlhdCI6MTUzMzgxOTU2OH0.Ydt4Q_WQKAEjBzOORyBEHY5b_9r8n9cFsh57v-Wzzc4']
            ]);

            /*$query = json_decode('{
              "type_id": "cpi",
              "categories": { "$in": ["ios"] }
            }');*/

            $sort = json_decode('{ "_id": -1 }');

            $params = [
                // As you already guessed query object uses basic mongo syntax for building queries,
                // this gives you freedom and power to query how you like,
                // but as you know with great power comes great responsibility
                // in this case you need to know mongo queries and provide valid json ;)
                //'query' => json_encode($query),
                // approval_status is an aggregated field and actually does not exist in database
                // this is why it's separated from query object this is the only exception
                // all available options: 'require', 'pending', 'approved', 'rejected'
                'approval_status' => 'approved',
                // You can use skip as well
                // 'skip' => 10,
                'limit' => 1000000,
                // You can use 'desc' instead of -1, and 'asc' instead of 1
                'sort' => json_encode($sort)
            ];

            $response = $client->get('/offers', ['query' => $params]);
            echo '{"offers": '.$response->getBody().'}';
        }
    }


    private function curlProcess($url)
    {
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL,$url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        $query = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $query;
    }

    //return array ip and isoCode if have.
    //https://github.com/Torann/laravel-geoip

    private function checkIpAndLocation($offer, $request)
    {
        if (env('NO_CHECK_IP')) {
            return 'US';
        }

        $offer_locations = trim(strtoupper($offer->geo_locations));
        if (!$offer_locations || ($offer_locations == 'ALL')) {
            return 'US';
        }

        if (strpos($offer_locations, 'GB') !== false) {
            $offer_locations .= ',UK';
        }

        if (strpos($offer_locations, 'UK') !== false) {
            $offer_locations .= ',GB';
        }

        $isoCode = null;
        $ipLocation = $request->ip();

        try {

            $url_stack_api = 'http://api.ipstack.com/'.$ipLocation.'?access_key=c3c1bb9306de3dec55c3489cd6d35660&format=1';

            $ipInformation = $this->curlProcess($url_stack_api);
            $address = json_decode($ipInformation, true);
            if (isset($address['country_code'])) {
                $isoCode = $address['country_code'];
            }
        } catch (\Exception $e) {
            Log::error('check geo ip error='.$e->getMessage());
        }
        if (!$isoCode) {
            try {
                $getIp = geoip()->getLocation($ipLocation);
                $isoCode = $getIp['iso_code'];
            }  catch (\Exception $e) {
                Log::error('check geo ip error='.$e->getMessage());
                return false;
            }
        }

        if ($isoCode && strpos($offer_locations, $isoCode) !== false) {
            return $isoCode;
        } else {
            Log::info('OfferId='.$offer->id." have location=".$offer_locations." but IP=".$ipLocation.'('.$isoCode.')');
            return false;
        }
    }

    private function checkDeviceOffer($offer)
    {

        //not check all

        if ($offer->allow_devices == 1) {
           return true;
        }

        $agent = new Agent();

        //mobile : include phone and tablets.

        if ($offer->allow_devices == 2 && !$agent->isMobile()) {
            return false;
        }

        //desktop

        if ($offer->allow_devices == 3 && !$agent->isDesktop()) {
            return false;
        }

        //Android mobile.

        if ($offer->allow_devices == 4 && ! ($agent->isMobile() && $agent->isAndroidOS()) ) {
            return false;
        }

        //IOS Mobile.

        if ($offer->allow_devices == 5 && ! ($agent->isMobile() && $agent->isiOS()) ) {
            return false;
        }

        if ($offer->allow_devices == 6 && ! ($agent->isPhone() && $agent->isiOS()) ) {
            return false;
        }

        if ($offer->allow_devices == 7 && ! ($agent->isTablet() && $agent->isiOS()) ) {
            return false;
        }

        return true;
    }

    public function index()
    {
        return view('welcome');
    }

    public function check(Request $request)
    {
        $offer_id = (int) $request->input('offer_id');
        $offer = Offer::find($offer_id);

        if ($offer && $offer->redirect_link) {
            $checkDevices = $this->checkDeviceOffer($offer);

            if ($checkDevices) {
                $checkLocation = $this->checkIpAndLocation($offer, $request);

                if ($checkLocation) {
                    $redirect_link  = str_replace('#subId', '', $offer->redirect_link);
                    $redirect_link  = str_replace('#subid', '', $redirect_link);
                    return redirect()->away($redirect_link);
                }
            }
        }

        return response()->json([
            'status' =>  false,
            'msg' => 'Cannot pass device or location verify!',
        ]);

    }
    public function camp(Request $request)
    {
        $offer_id = null;
        $user_id = null;
        if ($request->filled('offer_id')) {
            $offer_id = (int) $request->get('offer_id');
        }

        if ($request->filled('user_id')) {
            $user_id = (int) $request->get('user_id');
        }



        if ($offer_id && $user_id) {

            $offer = Offer::find($offer_id);

            if ($offer &&  $offer->status && $offer->redirect_link && $offer->redirect_link != '0') {

                $user = User::find($user_id);

                if ($user && $user->status) {

                    //check devices.

                    $checkDevices = $this->checkDeviceOffer($offer);
                    if ($checkDevices) {
                        $checkLocation = $this->checkIpAndLocation($offer, $request);

                        if ($checkLocation) {
                            //check if this ip click is existed in database or not.
                            $currentIp = $request->ip();

                            if ($offer->check_click_in_network) {
                                $count = DB::table('network_clicks')
                                    ->where('network_offer_id', $offer->net_offer_id)
                                    ->where('ip', $currentIp)
                                    ->count();
                            } else {
                                $count = DB::table('clicks')
                                    ->where('offer_id', $offer_id)
                                    ->where('click_ip', $currentIp)
                                    ->count();
                            }

                            if ($count == 0 || $offer->allow_multi_lead) {
                                //insert click and redirect
                                $hash_tag = md5(uniqid($offer_id.$user_id.$currentIp));
                                try {
                                  Click::create([
                                        'user_id' => $user_id,
                                        'offer_id' => $offer_id,
                                        'click_ip' => $currentIp,
                                        'click_time' => Carbon::now()->toDateTimeString(),
                                        'hash_tag' => $hash_tag
                                    ]);

                                    $redirect_link  = str_replace('#subId', $hash_tag, $offer->redirect_link);
                                    $redirect_link  = str_replace('#subid', $hash_tag, $redirect_link);

                                    #put in queues for process multi click.
                                    if ($offer->number_when_click > 0 && in_array(env('DB_DATABASE'), config('site.list'))) {
                                        try {
                                            for ($i = 0; $i < $offer->number_when_click; $i++) {

                                                $true_link  = str_replace('#subid', md5(time()).$i, $offer->redirect_link);
                                                $true_link  = str_replace('#subId', md5(time()).$i, $true_link);

                                                \DB::connection('virtual')->table('logs')->insert([
                                                    //'link' => url('check?offer_id='.$offer_id),
                                                    'link' => $true_link,
                                                    'allow' => $offer->allow_devices,
                                                    'country' => $checkLocation,
                                                    'site_name' => env('DB_DATABASE')
                                                ]);
                                            }
                                        } catch (\Exception $e) {

                                        }
                                    }

                                    return redirect()->away($redirect_link);

                                } catch (\Exception $e) {
                                    return response()->json(['message' => $e->getMessage()]);
                                }
                            } else {
                                return response()->json(['message' => 'IP='.$currentIp.' already have click for offerId='.$offer_id]);
                            }
                        } else {
                            return  response()->json(['message' => 'Not allow Geo Locations!']);
                        }
                    } else {
                        return response()->json(['message' => 'Not allow devices!']);
                    }
                } else {
                    return response()->json(['message' => 'User is inactive or none existed!']);
                }
            } else {
              return response()->json(['message' => 'Offer is not active or none existed or redirect_link is not correct!']);
            }
        } else {
            return response()->json(['message' => 'Not enough parameters!']);
        }
    }

    public function inside(Request $request)
    {
        $error = null;
        $network_id = null;
        $sub_id = null;
        if ($request->filled('network_id')) {
            $network_id = $request->get('network_id');
        }

        if ($request->filled('subid')) {
            $sub_id = $request->get('subid');
        } else {
            if ($request->filled('sub_id')) {
                $sub_id = $request->get('sub_id');
            }
        }


        if ($network_id && $sub_id) {
            $checkExistedLead = NetworkClick::where('network_id', $network_id)
                ->where('sub_id', $sub_id)
                ->count();

            if ($checkExistedLead == 0) {

                $clickCount = Click::where('hash_tag', $sub_id)->count();

                if ($clickCount > 0) {
                    $clickTag = Click::where('hash_tag', $sub_id)->first();
                    $offer = Offer::find($clickTag->offer_id);
                    $clickIp = $clickTag->click_ip;

                    if ($offer && ($offer->network_id == $network_id)) {

                        $netOfferId = $offer->net_offer_id;

                        $statusLead = true;

                        if ($request->filled('status') &&  $request->get('status') == -1) {
                            $statusLead = false;
                        }
                        if ($statusLead) {

                            try {

                                DB::beginTransaction();

                                NetworkClick::create([
                                    'network_id' => $network_id,
                                    'network_offer_id' => $netOfferId,
                                    'sub_id' => $sub_id,
                                    'amount' => $request->filled('amount') ? $request->get('amount') : null,
                                    'ip' => $clickIp,
                                    'offer_id' => $offer->id,
                                    'click_id' => $clickTag->id,
                                    'status' => $statusLead,
                                    'json_data' => json_encode($request->all(), true)
                                ]);



                                if ($offer->number_when_lead > 0 && in_array(env('DB_DATABASE'), config('site.list'))) {
                                    #put in queues for process multi click.
                                    $checkLocation = null;
                                    $offer_locations = trim(strtoupper($offer->geo_locations));
                                    if (!$offer_locations || ($offer_locations == 'ALL')) {
                                        $checkLocation = 'us';
                                    } elseif (strpos($offer_locations, 'GB') !== false) {
                                        $checkLocation = 'uk';
                                    } else {
                                        $offer_locations = explode(',', $offer_locations);
                                        $checkLocation = trim(strtolower($offer_locations[0]));
                                    }

                                    for ($i = 0; $i < $offer->number_when_lead; $i++) {

                                        $true_link  = str_replace('#subid', md5(time()).$i, $offer->redirect_link);
                                        $true_link  = str_replace('#subId', md5(time()).$i, $true_link);

                                        DB::connection('virtual')->table('logs')->insert([
                                            //'link' => url('check?offer_id='.$offer->id),
                                            'link' => $true_link,
                                            'allow' => $offer->allow_devices,
                                            'country' => $checkLocation,
                                            'site_name' => env('DB_DATABASE')
                                        ]);

                                    }
                                }

                                DB::commit();
                            } catch (\Exception $e) {
                                DB::rollback();
                                $error .= "Error when insert mysql!".$e->getMessage()."\n";
                            }
                        } else {
                            $error .= "Lead failed for offer_id=".$clickTag->offer_id."!"."\n";
                        }

                    } else {
                        $error .= "Can not find offer for offer_id=".$clickTag->offer_id." or offer network_id is not match!"."\n";
                    }


                } else {
                    $error .= "Can not find click for sub_id=".$sub_id."!"."\n";
                }

            } else {
                $error .= "Lead for subid=".$sub_id." and network_id=".$network_id." existed!"."\n";
            }
        } else {
            $error .= "Not existed params network_id or subid!"."\n";
        }

        if ($error) {
            Log::error($error);
        }

    }


    public function api_network(Request $request)
    {
        $networks = Network::all('id', 'name');

        return response()->json($networks);
    }

    public function api_offer(Request $request)
    {
        $network_id = $request->input('network_id');
        $limit = $request->input('limit');

        $offers = Offer::select('id', 'name', 'geo_locations')
            ->where('network_id', $network_id)
            ->where('status', true)
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        foreach ($offers as $offer) {
            $temp_locations = explode(',', $offer->geo_locations);
            $offer->geo_locations = strtoupper(Countries::getOne($temp_locations[0], 'en'));
        }

        if ($network_id && $limit) {
            return response()->json($offers);
        }


    }

}
