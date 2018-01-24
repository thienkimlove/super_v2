<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;

class NetworkClick extends Model
{
    protected $fillable = [
        'network_id',
        'network_offer_id',
        'sub_id',
        'amount',
        'ip',
        'offer_id',
        'click_id',
        'status',
        'json_data',
    ];

    public function network()
    {
        return $this->belongsTo(Network::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function click()
    {
        return $this->belongsTo(Click::class);
    }

    public static function getDataTables($request)
    {
        $query = NetworkClick::join('clicks', 'network_clicks.click_id', '=', 'clicks.id')
            ->join('offers', 'network_clicks.offer_id', '=', 'offers.id')
            ->join('users', 'clicks.user_id', '=', 'users.id')
            ->join('networks', 'network_clicks.network_id', '=', 'networks.id')
            ->join('groups', 'users.group_id', '=', 'groups.id')
            ->selectRaw("ROUND(SUM(offers.click_rate), 2) as total_money, COUNT(network_clicks.id) as total_leads")
            ->where('offers.reject', false);

        if ($request->filled('user_id')) {
            $query->where('users.id', $request->get('user_id'));
        }

        if ($request->filled('group_id')) {
            $query->where('groups.id', $request->get('group_id'));
        }

        if ($request->filled('network_id')) {
            $query->where('network_clicks.network_id', $request->get('network_id'));
        }

        if ($request->filled('offer_id')) {
            $query->where('network_clicks.offer_id', $request->get('offer_id'));
        }

        if ($request->filled('date')) {
            $dateRange = explode(' - ', $request->get('date'));
            $query->whereDate('network_clicks.created_at', '>=', Carbon::createFromFormat('d/m/Y', $dateRange[0])->toDateString());
            $query->whereDate('network_clicks.created_at', '<=', Carbon::createFromFormat('d/m/Y', $dateRange[1])->toDateString());
        }
        $reports = $query->first();

        $networkClick = static::select('*')->with('network', 'offer', 'click')->whereHas('offer', function($q) {
            $q->where('reject', false);
        });

        return DataTables::of($networkClick)
            ->filter(function ($query) use ($request) {
                if ($request->filled('user_id')) {
                    $query->whereHas('click', function($q) use($request) {
                        $q->where('user_id', $request->get('user_id'));
                    });
                }

                if ($request->filled('group_id')) {
                    $query->whereHas('click', function($q) use($request) {
                        $q->whereHas('user', function($q2) use($request) {
                            $q2->where('group_id', $request->get('group_id'));
                        });
                    });
                }

                if ($request->filled('network_id')) {
                    $query->where('network_id', $request->get('network_id'));
                }

                if ($request->filled('offer_id')) {
                    $query->where('offer_id', $request->get('offer_id'));
                }

                if ($request->filled('date')) {
                    $dateRange = explode(' - ', $request->get('date'));
                    $query->whereDate('created_at', '>=', Carbon::createFromFormat('d/m/Y', $dateRange[0])->toDateString());
                    $query->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', $dateRange[1])->toDateString());
                }

            })
            ->addColumn('offer_name', function ($networkClick) {
                return $networkClick->offer->name;
            })->addColumn('offer_id', function ($networkClick) {
                return $networkClick->offer->id;
            })->addColumn('offer_click_rate', function ($networkClick) {
                return $networkClick->offer->click_rate;
            })->addColumn('username', function ($networkClick) {
                return $networkClick->click->user->username;
            })
            ->addColumn('total_money', function() use ($reports) {
                return $reports->total_money;
            })->addColumn('total_leads', function() use ($reports) {
                return $reports->total_leads;
            })
            ->make(true);
    }


    public static function exportToExcel($request)
    {
        ini_set('memory_limit', '2048M');

        $query = NetworkClick::join('clicks', 'network_clicks.click_id', '=', 'clicks.id')
            ->join('offers', 'network_clicks.offer_id', '=', 'offers.id')
            ->join('users', 'clicks.user_id', '=', 'users.id')
            ->join('networks', 'network_clicks.network_id', '=', 'networks.id')
            ->join('groups', 'users.group_id', '=', 'groups.id')
            ->where('offers.reject', false);

        if ($request->filled('filter_user_id')) {
            $query->where('users.id', $request->get('filter_user_id'));
        }

        if ($request->filled('filter_group_id')) {
            $query->where('groups.id', $request->get('filter_group_id'));
        }

        if ($request->filled('filter_network_id')) {
            $query->where('network_clicks.network_id', $request->get('filter_network_id'));
        }

        if ($request->filled('filter_offer_id')) {
            $query->where('network_clicks.offer_id', $request->get('filter_offer_id'));
        }

        if ($request->filled('filter_date')) {
            $dateRange = explode(' - ', $request->get('filter_date'));
            $query->whereDate('network_clicks.created_at', '>=', Carbon::createFromFormat('d/m/Y', $dateRange[0])->toDateString());
            $query->whereDate('network_clicks.created_at', '<=', Carbon::createFromFormat('d/m/Y', $dateRange[1])->toDateString());
        }

        $totalQuery = clone $query;
        $reportQuery = clone $query;

        $totals = $totalQuery->selectRaw("ROUND(SUM(offers.click_rate), 2) as total_money, COUNT(network_clicks.id) as total_leads")->first();

        $reports = $reportQuery->selectRaw("networks.name as network_name, users.username as user_name, network_clicks.ip as network_click_ip, offers.name as offer_name, offers.net_offer_id as net_offer_id, offers.id as offer_id")->get();

        return (new static())->createExcellFile($reports, $totals);
    }

    public function createExcellFile($reports, $totals)
    {
        $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(resource_path('templates/results.xlsx'));

        $row = 2;
        foreach ($reports as $report) {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $row - 1)
                ->setCellValue('B'.$row, $report->network_name)
                ->setCellValue('C'.$row, $report->offer_id)
                ->setCellValue('D'.$row, $report->offer_name)
                ->setCellValue('E'.$row, $report->network_click_ip)
                ->setCellValue('F'.$row, $report->user_name);

            $row++;
        }

        $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, 'Total')
            ->setCellValue('B'.$row, '')
            ->setCellValue('C'.$row, '')
            ->setCellValue('D'.$row, '')
            ->setCellValue('E'.$row, 'Total Leads : '.$totals->total_leads)
            ->setCellValue('F'.$row, 'Total Money : '.$totals->total_money);


        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $path = 'reports_'.date('Y_m_d_His').'.xlsx';

        $objWriter->save(storage_path('app/public/' . $path));

        return redirect('/storage/' . $path);
    }

}
