<?php

namespace App\Http\Controllers\Backend;

use App\Group;
use App\Network;
use App\NetworkClick;
use App\Offer;
use App\Site;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Cache;

class HomeController extends AdminController
{

    protected function generateDashboard()
    {

        $user = auth('backend')->user();

        $userId = $user->isAdmin() ? null : $user->id;

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
            ->join('users', 'clicks.user_id', '=', 'users.id')
            ->join('networks', 'networks.id', '=', 'network_clicks.network_id')
            ->where('offers.reject', false);

        if ($userId) {
            $initQuery = $initQuery->where('users.id', $userId);
        }

        //recent lead.


        $userRecentQuery = clone $initQuery;

        $userRecent =  $userRecentQuery
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
            ->select(DB::raw("SUM(offers.click_rate) as total, networks.name, networks.id"))
            ->whereBetween('network_clicks.created_at', [$todayStart, $todayEnd])
            ->groupBy('networks.id')
            ->get();

        foreach ($todayNetworkMoney as $networkMoney) {
            $networkTotals[] = [
                'name' => $networkMoney->name,
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

        $generate =  [$content, $userRecent, $todayOffers, $yesterdayOffers, $weekOffers, $userTotals, $networkTotals];

        return $generate;

    }

    public function super()
    {
        $user = auth('backend')->user();
        if (in_array($user->email, config('site.super'))) {
            return view('super');
        } else {
            redirect(route('main.index'));
        }
    }
    public function dataTables(Request $request)
    {
        return NetworkClick::getDataTablesByDatabase($request);

    }

    public function getByDatabase($db)
    {
        $users = Site::getUserByDb($db);

        $networks = Site::getNetworkByDb($db);

        $user_html = "<option value=''>Choose User</option>";
        $network_html = "<option value=''>Choose Network</option>";

        foreach ($users as $id => $name) {
            $user_html .= "<option value='$id'>$name</option>";
        }

        foreach ($networks as $id => $name) {
            $network_html .= "<option value='$id'>$name</option>";
        }
        return response()->json(['user' => $user_html, 'network' => $network_html]);
    }

    public function index()
    {
        list($content, $userRecent, $todayOffers, $yesterdayOffers, $weekOffers, $userTotals, $networkTotals) = $this->generateDashboard();

        return view('index', compact('content', 'todayOffers', 'yesterdayOffers', 'weekOffers', 'userRecent', 'userTotals', 'networkTotals'));
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
                    ->where('offers.reject', false)
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

                $clicks =  $clicks->orderBy('network_clicks.id', 'desc')
                    ->paginate(10);

                $countTotal = DB::table('network_clicks')
                    ->select(DB::raw("SUM(offers.click_rate) as totalMoney, COUNT(network_clicks.id) as totalClicks"))
                    ->join('clicks', 'network_clicks.click_id', '=', 'clicks.id')
                    ->join('offers', 'network_clicks.offer_id', '=', 'offers.id')
                    ->join('users', 'clicks.user_id', '=', 'users.id')
                    ->where('offers.reject', false)
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
                    ->where('offers.reject', false)
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
                    ->where('offers.reject', false)
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
                ->where('offers.reject', false)
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
                ->where('offers.reject', false)
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
                    ->where('offers.reject', false)
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
                    ->where('offers.reject', false)
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

    public function clearFinishLog()
    {

        \DB::connection('virtual')->table('logs')->where('sent', 1)->delete();

        flash('success', 'Clear old Virtual Logs!');

        return redirect()->back();

    }

    public function recentLead()
    {

        $siteRecentLead = Cache::remember(env('DB_DATABASE').'_recent_lead', 1, function () {
            return DB::table('network_clicks')
                ->join('clicks', 'network_clicks.click_id', '=', 'clicks.id')
                ->join('offers', 'network_clicks.offer_id', '=', 'offers.id')
                ->join('users', 'clicks.user_id', '=', 'users.id')
                ->where('offers.reject', false)
                ->select('offers.name', 'offers.id', 'clicks.created_at as click_at', 'network_clicks.ip as network_ip', 'network_clicks.created_at', 'users.username', 'network_clicks.id as postback_id')
                ->orderBy('network_clicks.id', 'desc')
                ->limit(10)
                ->get();
        });

        return response()->json(['html' => view('recent', compact('siteRecentLead'))->render()]);

    }

}
