<?php

namespace App\Http\Controllers\Frontend;


use App\Click;
use App\Http\Controllers\Controller;
use App\NetworkClick;
use App\Offer;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use App\MediaOffer;

class MainController extends Controller
{

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
            $ipInformation = file_get_contents('http://freegeoip.net/json/'.$ipLocation);
            $address = json_decode($ipInformation, true);
            $isoCode = $address['country_code'];
        } catch (\Exception $e) {
            \Log::error('check geo ip error='.$e->getMessage());
            try {
                $getIp = \GeoIP::getLocation($ipLocation);
                $isoCode = $getIp['isoCode'];
            } catch (\Exception $e) {
                \Log::error('check geo ip error='.$e->getMessage());
                return false;
            }
        }

        if (strpos($offer_locations, $isoCode) !== false) {
            return $isoCode;
        } else {
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
        $offer_id = (int) $request->input('offer_id');
        $user_id = (int) $request->input('user_id');

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
                                    if ($offer->number_when_click > 0) {
                                        try {
                                            for ($i = 0; $i < $offer->number_when_click; $i++) {

                                                \DB::connection('virtual')->table('logs')->insert([
                                                    'link' => url('check?offer_id='.$offer_id),
                                                    'allow' => $offer->allow_devices,
                                                    'country' => $checkLocation,
                                                ]);
                                            }
                                        } catch (\Exception $e) {

                                        }
                                    }

                                    return redirect()->away($redirect_link);

                                } catch (\Exception $e) {
                                    return response()->json(['message' => 'Error happened when update database!']);
                                }
                            } else {
                                return response()->json(['message' => 'This ip already have click for this offer!']);
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
        $network_id = $request->input('network_id');
        $sub_id = $request->input('subid');

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

                    if ($offer) {

                        $netOfferId = $offer->net_offer_id;

                        $statusLead = true;

                        if ($request->has('status') &&  $request->input('status') == -1) {
                            $statusLead = false;
                        }
                        if ($statusLead) {
                            DB::beginTransaction();
                            try {

                                NetworkClick::create([
                                    'network_id' => $network_id,
                                    'network_offer_id' => $netOfferId,
                                    'sub_id' => $sub_id,
                                    'amount' => $request->input('amount'),
                                    'ip' => $clickIp,
                                    'offer_id' => $offer->id,
                                    'click_id' => $clickTag->id,
                                    'status' => $statusLead,
                                    'json_data' => json_encode($request->all(), true)
                                ]);

                                if ($offer->number_when_lead > 0) {
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
                                        DB::connection('virtual')->table('logs')->insert([
                                            'link' => url('check?offer_id='.$offer->id),
                                            'allow' => $offer->allow_devices,
                                            'country' => $checkLocation,
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
                        $error .= "Can not find offer for offer_id=".$clickTag->offer_id."!"."\n";
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
            \Log::error($error);
        }

    }

}
