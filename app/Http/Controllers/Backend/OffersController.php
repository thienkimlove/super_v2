<?php namespace App\Http\Controllers\Backend;

use App\Http\Requests\OfferRequest;
use App\Network;
use App\Offer;
use Illuminate\Http\Request;


class OffersController extends AdminController
{
    public $devices;
    public $networks;

    public function __construct()
    {
        parent::__construct();

        $this->devices = [
        '' => 'Choose offer allow devices'
    ];

        foreach (config('devices') as $key =>  $device) {
            $this->devices[$key] = $device;
        }

        $this->networks = [
            '' => 'Choose network'
        ] + Network::pluck('name', 'id')->all();
    }

    public function index(Request $request)
    {
        $searchOffer = null;
        $searchCountry = null;
        $searchDevice = null;
        $searchNetwork = null;
        $searchUid = null;
        $searchInactive = null;
        $searchAuto = null;

        if ($request->input('auto') && $request->input('auto') == 1) {
            $offers = Offer::orderBy('net_offer_id', 'desc');
        } else {
            $offers = Offer::latest('created_at');
        }

        $path = '/admin/offers?init=1';

        if ($request->input('uid')) {
            $searchUid = urldecode($request->input('uid'));
            $offers = $offers->where('id', $searchUid)->orWhere('net_offer_id', $searchUid);
            $path .= '&uid='.$request->input('uid');
        }

        if ($request->input('q')) {
            $searchOffer = urldecode($request->input('q'));
            $offers = $offers->where('name', 'LIKE', '%'. $searchOffer. '%');
            $path .= '&q='.$request->input('q');
        }

        if ($request->input('country')) {
            $searchCountry = urldecode($request->input('country'));
            $offers = $offers->where('geo_locations', 'LIKE', '%'. $searchCountry. '%');
            $path .= '&country='.$request->input('country');
        }

        if ($request->input('device')) {
            $searchDevice = urldecode($request->input('device'));
            if ($searchDevice == 5) {
                $offers = $offers->whereIn('allow_devices', [5, 6, 7]);
            } else {
                $offers = $offers->where('allow_devices', $searchDevice);
            }

            $path .= '&device='.$request->input('device');
        }

        if ($request->input('network')) {
            $searchNetwork = urldecode($request->input('network'));
            $offers = $offers->where('network_id', $searchNetwork);
            $path .= '&network='.$request->input('network');
        }

        if ($request->input('inactive') && $request->input('inactive') == 1) {
            $searchInactive = 1;
            $offers = $offers->where('status', false);
            $path .= '&inactive=1';
        } else {
            $offers = $offers->where('status', true);
        }

        if ($request->input('auto') &&  $request->input('auto') == 1) {
            $searchAuto = 1;
            $offers = $offers->where('auto', true);
            $path .= '&auto=1';
        } else {
            $offers = $offers->where('auto', false);
        }

        $offers = $offers->paginate(10);
        $offers->setPath($path);
        $devices = $this->devices;
        $networks = ['' => 'Choose network'] + Network::whereNotNull('cron')->OrWhere('cron', '<>', '')->pluck('name', 'id')->all();
        return view('admin.offer.index', compact('offers', 'searchOffer', 'searchInactive', 'searchAuto', 'searchCountry', 'devices', 'searchDevice', 'searchNetwork', 'searchUid', 'networks'));
    }


    public function create()
    {
        $devices = $this->devices;
        $networks = $this->networks;
        return view('admin.offer.form', compact('devices', 'networks'));
    }

    public function store(OfferRequest $request)
    {

        try {

            Offer::create([
                'name' => $request->input('name'),
                'redirect_link' => $request->input('redirect_link'),
                'click_rate' => $request->input('click_rate'),
                'geo_locations' => $request->input('geo_locations'),
                'allow_devices' => $request->input('allow_devices'),
                'network_id' => $request->input('network_id'),
                'net_offer_id' => $request->input('net_offer_id'),
                'status' => ($request->input('status') == 'on') ? true : false,
                'allow_multi_lead' => ($request->input('allow_multi_lead') == 'on') ? true : false,
                'check_click_in_network' => ($request->input('check_click_in_network') == 'on') ? true : false,
                'number_when_click' => $request->input('number_when_click')  ? intval($request->input('number_when_click')) : 0,

                'number_when_lead' => $request->input('number_when_lead')  ? intval($request->input('number_when_lead')) : 0,

                'image' => $request->input('image')
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                $e->getMessage()
            ]);
        }

        flash('Create offer success!', 'success');
        return redirect('admin/offers');
    }


    public function edit($id)
    {
        $devices = $this->devices;
        $networks = $this->networks;
        $offer = Offer::find($id);
        return view('admin.offer.form', compact('devices', 'offer', 'networks'));
    }

    public function show($id)
    {
        $devices = $this->devices;
        $networks = $this->networks;
        $offer = Offer::find($id);
        return view('admin.offer.show', compact('devices', 'offer', 'networks'));
    }


    public function update($id, OfferRequest $request)
    {
        $offer = Offer::find($id);

        $data = [
            'name' => $request->input('name'),
            'redirect_link' => $request->input('redirect_link'),
            'click_rate' => $request->input('click_rate'),
            'geo_locations' => $request->input('geo_locations'),
            'allow_devices' => $request->input('allow_devices'),
            'network_id' => $request->input('network_id'),
            'net_offer_id' => $request->input('net_offer_id'),
            'image' => $request->input('image'),
            'status' => ($request->input('status') == 'on') ? true : false,
            'allow_multi_lead' => ($request->input('allow_multi_lead') == 'on') ? true : false,
            'check_click_in_network' => ($request->input('check_click_in_network') == 'on') ? true : false,
            'number_when_click' => $request->input('number_when_click')  ? intval($request->input('number_when_click')) : 0,

            'number_when_lead' => $request->input('number_when_lead')  ? intval($request->input('number_when_lead')) : 0,
        ];

        try {
            $offer->update($data);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                $e->getMessage()
            ]);
        }

        flash('Update offer success!', 'success');
        return redirect('admin/offers');
    }


    public function destroy($id)
    {
        Offer::find($id)->delete();
        flash('Success deleted offer!');
        return redirect('admin/offers');
    }

}
