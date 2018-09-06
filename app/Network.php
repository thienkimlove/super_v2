<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;

class Network extends Model
{
    protected $fillable = [
        'name',
        'cron',
        'rate_offer',
        'virtual_click',
        'virtual_lead',
        'prefix',
        'idfa_name',
    ];

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public static function getDataTables($request)
    {
        $network = static::select('*')->latest('created_at');

        return DataTables::of($network)
            ->filter(function ($query) use ($request) {
                if ($request->filled('name')) {
                    $query->where('name', 'like', '%' . $request->get('name') . '%');
                }
            })
            ->addColumn('action', function ($network) {
                $response_html = '<a class="table-action-btn" title="Chỉnh sửa network" href="' . route('networks.edit', $network->id) . '"><i class="fa fa-pencil text-success"></i></a>';

                if ($network->cron) {
                    if (in_array(env('DB_DATABASE'), config('site.list'))) {
                        $response_html .= '<a class="table-action-btn" title="Run Cron" target="_blank" href="http://115.146.127.8:8080/core/cron/?site='.env('DB_DATABASE').'&network_id='.$network->id.'"><i class="fa fa-tasks text-success"></i></a>';
                    } else {
                        $response_html .= '<a class="table-action-btn" title="Run Cron" href="' . route('networks.cron', $network->id) . '"><i class="fa fa-tasks text-success"></i></a>';
                    }
                }

                return $response_html;

            })
            ->addColumn('not_lead_count', function ($network) {
                return Offer::where('network_id', $network->id)->whereDoesntHave('leads')->count();

            }) ->editColumn('cron', function ($network) {
                return str_limit($network->cron, 30);

            }) ->addColumn('postback_link', function ($network) {
                return url('postback?network_id='.$network->id.'&subid={require_subId}&status={optional_status}&amount={optional_amount}');

            })->addColumn('haspostback_link', function ($network) {
                return url('hashpostback?network_id='.$network->id.'&subid={require_subId}');

            })
            ->rawColumns(['action', 'postback_link', 'haspostback_link'])
            ->make(true);
    }
}
