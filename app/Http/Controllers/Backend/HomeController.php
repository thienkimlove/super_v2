<?php

namespace App\Http\Controllers\Backend;

use App\Click;
use App\Group;
use App\Network;
use App\Offer;
use App\Site;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class HomeController extends AdminController
{

    protected function generateDashboard($userId = null)
    {

        $todayStart = Carbon::now()->startOfDay();
        $todayEnd = Carbon::now()->endOfDay();

        $yesterdayStart = Carbon::now()->yesterday()->startOfDay();
        $yesterdayEnd = Carbon::now()->yesterday()->endOfDay();

        $startWeek = Carbon::now()->startOfWeek();
        $endWeek = Carbon::now()->endOfWeek();

        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();


        $initQuery = DB::table('network_clicks')
            ->join('clicks', 'network_clicks.click_id', '=', 'clicks.id')
            ->join('offers', 'network_clicks.offer_id', '=', 'offers.id')
            ->join('users', 'clicks.user_id', '=', 'users.id');

        if ($userId) {
            $initQuery = $initQuery->where('users.id', $userId);
        }

        //recent lead.


        $userRecent = clone $initQuery;

        $userRecent = $userRecent
            ->select('offers.name', 'network_clicks.ip', 'network_clicks.created_at', 'users.username')
            ->orderBy('network_clicks.id', 'desc')
            ->limit(10)
            ->get();


        //money
        $moneyQuery = clone $initQuery;
        $moneyQuery = $moneyQuery->select(DB::raw("SUM(offers.click_rate) as total"));


        $todayMoneyQuery = clone $moneyQuery;
        $monthMoneyQuery = clone $moneyQuery;
        $totalMoneyQuery = clone $moneyQuery;

        $todayMoneyQuery = $todayMoneyQuery->whereBetween('network_clicks.created_at', [$todayStart, $todayEnd])->get();
        $monthMoneyQuery = $monthMoneyQuery->whereBetween('network_clicks.created_at', [$startMonth, $endMonth])->get();
        $totalMoneyQuery = $totalMoneyQuery->get();

        $content = [
            'today' => ($todayMoneyQuery->count() > 0) ? $todayMoneyQuery->first()->total : 0,
            'month' => ($monthMoneyQuery->count() > 0) ? $monthMoneyQuery->first()->total : 0,
            'total' => ($totalMoneyQuery->count() > 0) ? $totalMoneyQuery->first()->total : 0,
        ];

        $userQuery = clone $initQuery;
        $networkQuery = clone $initQuery;

        $userTotals = [];
        $networkTotals = [];

        $todayUserMoney = $userQuery
            ->select(DB::raw("SUM(offers.click_rate) as total, users.id"))
            ->whereBetween('network_clicks.created_at', [$todayStart, $todayEnd])
            ->groupBy('users.id')
            ->get();

        foreach ($todayUserMoney as $userMoney) {
            $user = User::find($userMoney->id);
            $userTotals[] = [
                'username' => $user->username,
                'total' => $userMoney->total
            ];
        }

        $todayNetworkMoney = $networkQuery
            ->join('networks', 'networks.id', '=', 'network_clicks.network_id')
            ->select(DB::raw("SUM(offers.click_rate) as total, networks.id"))
            ->whereBetween('network_clicks.created_at', [$todayStart, $todayEnd])
            ->groupBy('networks.id')
            ->get();

        foreach ($todayNetworkMoney as $networkMoney) {
            $network = Network::find($networkMoney->id);
            $networkTotals[] = [
                'name' => $network->name,
                'total' => $networkMoney->total
            ];
        }


        //get offers.
        //api using to get real clicks.


        $offerQuery = clone $initQuery;

        $offerQuery = $offerQuery
            ->select(DB::raw("COUNT(network_clicks.id) as totalLeads, offers.id"))
            ->groupBy('offers.id');


        $todayOfferQuery = clone $offerQuery;
        $yesterdayOfferQuery = clone $offerQuery;
        $weekOfferQuery = clone $offerQuery;

        $todayOfferQuery = $todayOfferQuery->whereBetween('network_clicks.created_at', [$todayStart, $todayEnd])->get();
        $yesterdayOfferQuery = $yesterdayOfferQuery->whereBetween('network_clicks.created_at', [$yesterdayStart, $yesterdayEnd])->get();
        $weekOfferQuery = $weekOfferQuery->whereBetween('network_clicks.created_at', [$startWeek, $endWeek])->get();


        $todayOffers = [];
        $yesterdayOffers = [];
        $weekOffers = [];

        if ($todayOfferQuery->count() > 0) {
            foreach ($todayOfferQuery as $offerSection) {
                $offer = Offer::find($offerSection->id);
                if ($offer) {
                    $site_click = DB::table('clicks')->where('offer_id', $offer->id)->whereBetween('created_at', [$todayStart, $todayEnd])->count();
                    $site_cr = ($site_click > 0) ? round(($offerSection->totalLeads / $site_click) * 100, 2) . '%' : 'Not Available';


                    $todayOffers[] = [
                        'offer_name' => $offer->name,
                        'net_lead' => $offerSection->totalLeads,
                        'site_cr' => $site_cr,
                        'site_click' => $site_click,
                        'offer_price' => $offer->click_rate,
                        'offer_total' => $offer->click_rate*$offerSection->totalLeads,
                        'offer_id' => $offer->id,
                    ];
                }
            }
        }


        if ($yesterdayOfferQuery->count() > 0) {
            foreach ($yesterdayOfferQuery as $offerSection) {

                $offer = Offer::find($offerSection->id);
                if ($offer) {
                    $site_click = DB::table('clicks')->where('offer_id', $offer->id)->whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])->count();
                    $site_cr = ($site_click > 0) ? round(($offerSection->totalLeads / $site_click) * 100, 2) . '%' : 'Not Available';


                    $yesterdayOffers[] = [
                        'offer_name' => $offer->name,
                        'net_lead' => $offerSection->totalLeads,
                        'site_cr' => $site_cr,
                        'site_click' => $site_click,
                        'offer_price' => $offer->click_rate,
                        'offer_total' => $offer->click_rate*$offerSection->totalLeads,
                        'offer_id' => $offer->id,
                    ];
                }
            }
        }

        if ($weekOfferQuery->count() > 0) {
            foreach ($weekOfferQuery as $offerSection) {

                $offer = Offer::find($offerSection->id);

                if ($offer) {
                    $site_click = DB::table('clicks')->where('offer_id', $offer->id)->whereBetween('created_at', [$startWeek, $endWeek])->count();
                    $site_cr = ($site_click > 0) ? round(($offerSection->totalLeads / $site_click) * 100, 2) . '%' : 'Not Available';


                    $weekOffers[] = [
                        'offer_name' => $offer->name,
                        'net_lead' => $offerSection->totalLeads,
                        'site_cr' => $site_cr,
                        'site_click' => $site_click,
                        'offer_price' => $offer->click_rate,
                        'offer_total' => $offer->click_rate*$offerSection->totalLeads,
                        'offer_id' => $offer->id,
                    ];
                }
            }
        }

        return [$content, $userRecent, $todayOffers, $yesterdayOffers, $weekOffers, $userTotals, $networkTotals];

    }


    public function index()
    {
        $currentUser = auth('backend')->user();
        $currentUserId = ($currentUser->id == 1) ? 12 : $currentUser->id;
        list($content, $userRecent, $todayOffers, $yesterdayOffers, $weekOffers, $userTotals, $networkTotals) = $this->generateDashboard($currentUserId);
        return view('admin.general.control', compact('content', 'todayOffers', 'yesterdayOffers', 'weekOffers', 'userRecent', 'currentUserId', 'userTotals', 'networkTotals'));
    }

    public function ajaxSiteRecentLead()
    {
        $siteRecentLead = DB::table('network_clicks')
            ->join('clicks', 'network_clicks.click_id', '=', 'clicks.id')
            ->join('offers', 'network_clicks.offer_id', '=', 'offers.id')
            ->join('users', 'clicks.user_id', '=', 'users.id')
            ->select('offers.name', 'offers.id', 'clicks.created_at as click_at', 'network_clicks.ip as network_ip', 'network_clicks.created_at', 'users.username', 'network_clicks.id as postback_id')
            ->orderBy('network_clicks.id', 'desc')
            ->limit(10)
            ->get();

        return response()->json(['success' => true, 'html' => view('admin.ajax_recent_lead', compact('siteRecentLead'))->render()]);
    }

    public function control()
    {
        $currentUserId = null;
        list($content, $userRecent, $todayOffers, $yesterdayOffers, $weekOffers, $userTotals, $networkTotals) = $this->generateDashboard();
        return view('admin.general.control', compact('content', 'todayOffers', 'yesterdayOffers', 'weekOffers', 'userRecent', 'currentUserId', 'userTotals', 'networkTotals'));
    }

    public function clearlead(Request $request)
    {
        $offer_id = $request->input('offer_id');

        if ($offer = Offer::find($offer_id)) {
            Click::where('offer_id', $offer_id)->update(['click_ip' => '10.0.2.2']);
            flash('Clear IP Lead success!');
            return redirect('admin/offers');
        } else {
            flash('No offer found!');
            return redirect('admin/offers');
        }
    }

    public function ajax($content, Request $request)
    {
        if ($content == 'user') {
            $records = User::where('username', 'like', '%' . $request->input('q'). '%')->get();
            $response = [];
            foreach ($records as $record) {
                $response[] = ['id' => $record->id, 'name' => $record->username];
            }
            return response()->json($response);
        }

        if ($content == 'offer') {
            $records = Offer::where('name', 'like', '%' . $request->input('q'). '%')->where('auto', false)->get();
            $response = [];
            foreach ($records as $record) {
                $response[] = ['id' => $record->id, 'name' => $record->name];
            }
            return response()->json($response);
        }
    }

    public function thongke()
    {

        $globalGroups = ['' => 'Choose Group'] + Group::pluck('name', 'id')->all();
        //chi hien thi danh sach cac offer co lead.
        $globalOffers = ['' => 'Choose Offer'] + Offer::has('leads')->pluck('name', 'id')->all();
        foreach ($globalOffers as $key => $value) {
            if ($key) {
                $globalOffers[$key] = $value.' ID='.$key;
            }
        }
        $globalNetworks = ['' => 'Choose Network'] + Network::pluck('name', 'id')->all();
        $globalUsers = User::pluck('username')->all();
        
        return view('admin.result', compact('globalGroups', 'globalOffers', 'globalUsers', 'globalNetworks'));
    }

    public function statistic($content, Request $request)
    {
        $clicks = null;

        $start = ($request->input('start')) ? $request->input('start') : '2016-01-01';
        $end = ($request->input('end')) ? $request->input('end') : '2016-12-31';

        $queryStart = Carbon::createFromFormat('Y-m-d', $start)->startOfDay();
        $queryEnd = Carbon::createFromFormat('Y-m-d', $end)->endOfDay();

        $countTotal = null;
        $network_id = $request->input('network_id');

        $search_user = $request->input('search_user');
        $search_offer = $request->input('search_offer');

        $userSearchId = $request->input('search_user_id');
        $offerSearchId = $request->input('search_offer_id');


        $displaySearchOffer = false;
        $displaySearchUser = false;

        switch ($content) {
            case "group" :
                $userIds = User::where('group_id', $request->input('content_id'))->pluck('id')->all();

                $clicks = DB::table('network_clicks')
                    ->select(
                        'clicks.id',
                        'clicks.offer_id',
                        'clicks.click_ip',
                        'clicks.hash_tag',
                        'clicks.created_at',
                        DB::raw('offers.id as offer_site_id'),
                        DB::raw('offers.name as offer_name'),
                        DB::raw('users.username as username'),
                        DB::raw('offers.allow_devices as offer_allow_devices'),
                        DB::raw('offers.geo_locations as offer_geo_locations')
                    )

                    ->join('clicks', 'network_clicks.click_id', '=', 'clicks.id')
                    ->join('offers', 'network_clicks.offer_id', '=', 'offers.id')
                    ->join('users', 'clicks.user_id', '=', 'users.id')
                   // ->where('offers.auto', false)
                    ->whereIn('users.id', $userIds)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);

                if ($network_id) {
                    $clicks = $clicks->where('network_clicks.network_id', $network_id);
                }

                if ($userSearchId) {
                    $clicks = $clicks->where('users.id', $userSearchId);
                }

                if ($offerSearchId) {
                    $clicks = $clicks->where('offers.id', $offerSearchId);
                }

                $clicks = $clicks->orderBy('network_clicks.id', 'desc')
                    ->paginate(10);

                $countTotal = DB::table('network_clicks')
                    ->select(DB::raw("SUM(offers.click_rate) as totalMoney, COUNT(network_clicks.id) as totalClicks"))
                    ->join('clicks', 'network_clicks.click_id', '=', 'clicks.id')
                    ->join('offers', 'network_clicks.offer_id', '=', 'offers.id')
                    ->join('users', 'clicks.user_id', '=', 'users.id')
                   // ->where('offers.auto', false)
                    ->whereIn('users.id', $userIds)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);
                if ($network_id) {
                    $countTotal = $countTotal->where('network_clicks.network_id', $network_id);
                }

                if ($userSearchId) {
                    $countTotal = $countTotal->where('users.id', $userSearchId);
                }

                if ($offerSearchId) {
                    $countTotal = $countTotal->where('offers.id', $offerSearchId);
                }

                $countTotal = $countTotal->get();

                $displaySearchOffer = true;
                $displaySearchUser =  true;

                break;
            case "user" :
                $userId = User::where('username', $request->input('content_id'))->first()->id;

                $clicks = DB::table('network_clicks')
                    ->select(
                        'clicks.id',
                        'clicks.offer_id',
                        'clicks.click_ip',
                        'clicks.hash_tag',
                        'clicks.created_at',
                        DB::raw('offers.id as offer_site_id'),
                        DB::raw('offers.name as offer_name'),
                        DB::raw('users.username as username'),
                        DB::raw('offers.allow_devices as offer_allow_devices'),
                        DB::raw('offers.geo_locations as offer_geo_locations')
                    )
                    ->join('clicks', 'network_clicks.click_id', '=', 'clicks.id')
                    ->join('offers', 'network_clicks.offer_id', '=', 'offers.id')
                    ->join('users', 'clicks.user_id', '=', 'users.id')
                    //->where('offers.auto', false)
                    ->where('users.id', $userId)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);
                if ($network_id) {
                    $clicks = $clicks->where('network_clicks.network_id', $network_id);
                }

                if ($offerSearchId) {
                    $clicks = $clicks->where('offers.id', $offerSearchId);
                }

                $clicks = $clicks->orderBy('network_clicks.id', 'desc')
                    ->paginate(10);

                $countTotal = DB::table('network_clicks')
                    ->select(DB::raw("SUM(offers.click_rate) as totalMoney, COUNT(network_clicks.id) as totalClicks"))
                    ->join('clicks', 'network_clicks.click_id', '=', 'clicks.id')
                    ->join('offers', 'network_clicks.offer_id', '=', 'offers.id')
                    ->join('users', 'clicks.user_id', '=', 'users.id')
                    //->where('offers.auto', false)
                    ->where('users.id', $userId)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);

                if ($network_id) {
                    $countTotal = $countTotal->where('network_clicks.network_id', $network_id);
                }

                if ($offerSearchId) {
                    $countTotal = $countTotal->where('offers.id', $offerSearchId);
                }

                $countTotal = $countTotal->get();

                $displaySearchOffer = true;

                break;
            case "offer" :
            $clicks = DB::table('network_clicks')
                ->select(
                    'clicks.id',
                    'clicks.offer_id',
                    'clicks.click_ip',
                    'clicks.hash_tag',
                    'clicks.created_at',
                    DB::raw('offers.id as offer_site_id'),
                    DB::raw('offers.name as offer_name'),
                    DB::raw('users.username as username'),
                    DB::raw('offers.allow_devices as offer_allow_devices'),
                    DB::raw('offers.geo_locations as offer_geo_locations')
                )
                ->join('clicks', 'network_clicks.click_id', '=', 'clicks.id')
                ->join('offers', 'network_clicks.offer_id', '=', 'offers.id')
                ->join('users', 'clicks.user_id', '=', 'users.id')
                //->where('offers.auto', false)
                ->where('offers.id', $request->input('content_id'))
                ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);

                if ($userSearchId) {
                    $clicks = $clicks->where('users.id', $userSearchId);
                }

                $clicks = $clicks->orderBy('network_clicks.id', 'desc')
                ->paginate(10);

            $countTotal = DB::table('network_clicks')
                ->select(DB::raw("SUM(offers.click_rate) as totalMoney, COUNT(network_clicks.id) as totalClicks"))
                ->join('clicks', 'network_clicks.click_id', '=', 'clicks.id')
                ->join('offers', 'network_clicks.offer_id', '=', 'offers.id')
                ->join('users', 'clicks.user_id', '=', 'users.id')
               // ->where('offers.auto', false)
                ->where('offers.id', $request->input('content_id'))
                ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);
            if ($userSearchId) {
                $countTotal = $countTotal->where('users.id', $userSearchId);
            }

            $countTotal = $countTotal->get();
            $displaySearchUser = true;

            break;

            case "network" :
                $clicks = DB::table('network_clicks')
                    ->select(
                        'clicks.id',
                        'clicks.offer_id',
                        'clicks.click_ip',
                        'clicks.hash_tag',
                        'clicks.created_at',
                        DB::raw('offers.id as offer_site_id'),
                        DB::raw('offers.name as offer_name'),
                        DB::raw('users.username as username'),
                        DB::raw('offers.allow_devices as offer_allow_devices'),
                        DB::raw('offers.geo_locations as offer_geo_locations')
                    )
                    ->join('clicks', 'network_clicks.click_id', '=', 'clicks.id')
                    ->join('offers', 'network_clicks.offer_id', '=', 'offers.id')
                    ->join('users', 'clicks.user_id', '=', 'users.id')
                   // ->where('offers.auto', false)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);

                if ($userSearchId) {
                    $clicks = $clicks->where('users.id', $userSearchId);
                }

                if ($request->input('content_id')) {
                    $clicks = $clicks->where('network_clicks.network_id', $request->input('content_id'));
                }

                if ($offerSearchId) {
                    $clicks = $clicks->where('offers.id', $offerSearchId);
                }


                $clicks = $clicks->orderBy('network_clicks.id', 'desc')
                    ->paginate(10);

                $countTotal = DB::table('network_clicks')
                    ->select(DB::raw("SUM(offers.click_rate) as totalMoney, COUNT(network_clicks.id) as totalClicks"))
                    ->join('clicks', 'network_clicks.click_id', '=', 'clicks.id')
                    ->join('offers', 'network_clicks.offer_id', '=', 'offers.id')
                    ->join('users', 'clicks.user_id', '=', 'users.id')
                   // ->where('offers.auto', false)
                    ->whereBetween('network_clicks.created_at', [$queryStart, $queryEnd]);

                if ($userSearchId) {
                    $countTotal = $countTotal->where('users.id', $userSearchId);
                }

                if ($request->input('content_id')) {
                    $countTotal = $countTotal->where('network_clicks.network_id', $request->input('content_id'));
                }

                if ($offerSearchId) {
                    $countTotal = $countTotal->where('offers.id', $offerSearchId);
                }

                $countTotal = $countTotal->get();

                $displaySearchUser = true;
                $displaySearchOffer = true;

                break;
        }

        $customUrl = '/admin/statistic/'. $content.'?start='.$start.'&end='.$end;

        if ($request->input('content_id')) {
            $customUrl .= '&content_id='.$request->input('content_id');
        }
        if ($request->input('network_id')) {
            $customUrl .= '&network_id='.$request->input('network_id');
        }

        if ($request->input('search_offer_id')) {
            $customUrl .= '&search_offer_id='.$request->input('search_offer_id');
        }

        if ($request->input('search_user_id')) {
            $customUrl .= '&search_user_id='.$request->input('search_user_id');
        }

        if ($request->input('search_offer')) {
            $customUrl .= '&search_offer='.$request->input('search_offer');
        }

        if ($request->input('search_user')) {
            $customUrl .= '&search_user='.$request->input('search_user');
        }


        $clicks->setPath($customUrl);

        $totalClicks = $countTotal->first()->totalClicks;
        $totalMoney = $countTotal->first()->totalMoney;

        $title = 'Thống kê theo '.strtoupper($content).' từ ngày '.$start .' đến ngày '.$end;

        $globalGroups = ['' => 'Choose Group'] + Group::pluck('name', 'id')->all();
        $globalOffers = ['' => 'Choose Offer'] + Offer::has('leads')->pluck('name', 'id')->all();
        foreach ($globalOffers as $key => $value) {
            if ($key) {
                $globalOffers[$key] = $value.' ID='.$key;
            }
        }
        $globalNetworks = ['' => 'Choose Network'] + Network::pluck('name', 'id')->all();
        $globalUsers = User::pluck('username')->all();

        $content_id = $request->input('content_id') ? $request->input('content_id') : '';


        return view('admin.result', compact(
            'clicks',
            'totalMoney',
            'totalClicks',
            'title',
            'globalGroups',
            'globalOffers',
            'globalNetworks',
            'globalUsers',
            'content',
            'content_id',
            'network_id',
            'start',
            'end',
            'search_user',
            'search_offer',
            'displaySearchUser',
            'displaySearchOffer',
            'offerSearchId',
            'userSearchId'
        ));
    }


    public function cron(Request $request)
    {
        set_time_limit(0);
        if ($request->input('network_id')) {
            $networks = Network::where('id', $request->input('network_id'))->get();
        } else {
            $networks = Network::all();
        }

        $message = null;
        foreach ($networks as $network) {
            if ($network->cron) {
                $message .= 'Network :' . $network->name . ' have cron='.$network->cron.'"\n"';
                $message .= Site::feed($network).'"\n"';
            }
        }
        return view('admin.cron', compact('message'));
    }


    public function submit($id)
    {

        $offer = Offer::find($id);

        $offer_locations = trim(strtoupper($offer->geo_locations));
        if (!$offer_locations || ($offer_locations == 'ALL')) {
            $offer_locations = 'US';
        }

        if (strpos($offer_locations, 'GB') !== false) {
            $offer_locations .= ',UK';
        }

        if (strpos($offer_locations, 'UK') !== false) {
            $offer_locations .= ',GB';
        }

        $country = (strpos($offer_locations, ',') !== false) ? explode(',', $offer_locations)[0] : $offer_locations;

        $country = strtolower($country);

        $url = str_replace('#subId', '', $offer->redirect_link);




        $type = ($offer->allow_devices > 4) ? 0 : 1;

        $userAgent = \DB::connection('lumen')
            ->table('agents')
            ->where('type', $type)
            ->inRandomOrder()
            ->limit(1)
            ->get();

        $trueAgent = $userAgent->first()->agent;

        $username = 'lum-customer-theway_holdings-zone-nam-country-' . strtolower($country);
        $session = mt_rand();

        $background = file_get_contents(resource_path('read.py'));
        $background = str_replace(['#URL#', '#USERNAME#', '#AGENT#', '#OFFERID#'], [$url, $username.'-session-'.$session, $trueAgent, $offer->id], $background);

        $tempPythonFile = '/tmp/exe_'.$session.'_.py';
        file_put_contents($tempPythonFile, $background);

        file_put_contents(storage_path('logs/test_link.log'), '========================'."\n", FILE_APPEND);
        file_put_contents(storage_path('logs/test_link.log'), 'OfferId='.$offer->id.'|Country='.$country.'|Agent='.$trueAgent."\n", FILE_APPEND);

        file_put_contents(storage_path('logs/test_link.log'), '========================'."\n", FILE_APPEND);

        try {
            $process = new Process('python '.$tempPythonFile, '/tmp', null, null, 120);
            $process->run();

            // executes after the command finishes
            if (!$process->isSuccessful()) {
                unlink($tempPythonFile);
                throw new ProcessFailedException($process);
            }

            $offer->test_link = $process->getOutput();
            $offer->save();
            unlink($tempPythonFile);
            $html = $offer->id.'_last.html';
            $image = $offer->id.'_last.png';
            copy('/tmp/'.$html, public_path('test/'.$html));
            copy('/tmp/'.$image, public_path('test/'.$image));
            return response()->json(['data' => $offer->test_link, 'image' => $image]);
        } catch (\Exception $e) {
            return response()->json(['data' => null, 'image' => null, 'error' => $e->getMessage()]);
        }

    }

}
