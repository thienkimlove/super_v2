<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;

class Offer extends Model
{
    protected $fillable = [
        'name',
        'redirect_link',
        'click_rate',
        'geo_locations',
        'allow_devices',
        'network_id',
        'net_offer_id',
        'image',
        'status',
        'auto',
        'allow_multi_lead',
        'check_click_in_network',
        'number_when_click',
        'number_when_lead',
        'test_link'
    ];

    public $dates = ['created_at', 'updated_at'];

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }

    public function network()
    {
        return $this->belongsTo(Network::class);
    }

    public function leads()
    {
        return $this->hasMany(NetworkClick::class);
    }


    public static function getDataTables($request)
    {
        $offer = static::select('*')->latest('created_at');

        return DataTables::of($offer)
            ->filter(function ($query) use ($request) {
                if ($request->filled('name')) {
                    $query->where('name', 'like', '%' . $request->get('name') . '%');
                }

                if ($request->filled('network_id')) {
                    $query->where('network_id', $request->get('network_id'));
                }

                if ($request->filled('auto')) {
                    $query->where('auto', $request->get('auto'));
                }

                if ($request->filled('uid')) {
                    $query->where('id', $request->get('uid'))
                        ->orWhere('net_offer_id', $request->get('uid'));
                }

                if ($request->filled('status')) {
                    $query->where('status', $request->get('status'));
                }

                if ($request->filled('country')) {
                    $query->where('geo_locations', 'like', '%' . $request->get('country') . '%');
                }

                if ($request->filled('device')) {
                    $searchDevice = urldecode($request->get('device'));
                    if ($searchDevice == 5) {
                        $query->whereIn('allow_devices', [5, 6, 7]);
                    } else {
                        $query->where('allow_devices', $searchDevice);
                    }
                }


            })
            ->editColumn('status', function ($offer) {
                return $offer->status ? '<i class="ion ion-checkmark-circled text-success"></i>' : '<i class="ion ion-close-circled text-danger"></i>';
            })
            ->editColumn('check_click_in_network', function ($offer) {
                return $offer->check_click_in_network ? '<i class="ion ion-checkmark-circled text-success"></i>' : '<i class="ion ion-close-circled text-danger"></i>';
            })
            ->editColumn('allow_multi_lead', function ($offer) {
                return $offer->allow_multi_lead ? '<i class="ion ion-checkmark-circled text-success"></i>' : '<i class="ion ion-close-circled text-danger"></i>';
            })
            ->editColumn('status', function ($offer) {
                return $offer->status ? '<i class="ion ion-checkmark-circled text-success"></i>' : '<i class="ion ion-close-circled text-danger"></i>';
            })
            ->editColumn('geo_locations', function ($offer) {
                return config('devices')[$offer->allow_devices];
            })
            ->addColumn('redirect_link_for_user', function ($offer) {
                return url('camp?offer_id='.$offer->id.'&user_id='.auth('backend')->user()->id);
            })
            ->addColumn('network_name', function ($offer) {
                return $offer->network ? $offer->network->name : '';
            })
            ->addColumn('action', function ($offer) {
                return '<a class="table-action-btn" title="Chỉnh sửa offer" href="' . route('offers.edit', $offer->id) . '"><i class="fa fa-pencil text-success"></i></a>';

            })
            ->rawColumns(['network_name', 'status', 'action', 'name', 'geo_locations', 'redirect_link_for_user', 'check_click_in_network', 'allow_multi_lead'])
            ->make(true);
    }

}
