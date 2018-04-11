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
        'test_link',
        'reject',
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
        $offer = static::select('*');

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

                if ($request->filled('reject')) {
                    $query->where('reject', $request->get('reject'));
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
            ->editColumn('geo_locations', function ($offer) {
                return str_limit($offer->geo_locations, 20);
            })
            ->editColumn('status', function ($offer) {
                return $offer->status ? '<i class="ion ion-checkmark-circled text-success"></i>' : '<i class="ion ion-close-circled text-danger"></i>';
            })
            ->editColumn('allow_devices', function ($offer) {
                return config('devices')[$offer->allow_devices];
            })
            ->addColumn('redirect_link_for_user', function ($offer) {
                return url('camp?offer_id='.$offer->id.'&user_id='.auth('backend')->user()->id);
            })
            ->addColumn('network_name', function ($offer) {
                return $offer->network ? $offer->network->name : '';
            })
            ->addColumn('virtual_click', function ($offer) {
                return ' <span>Number clicks when have click:</span>'.$offer->number_when_click.' <br/>
                                            <span>Number clicks when have lead:</span> '.$offer->number_when_lead.' <br/>';
            })
            ->addColumn('action', function ($offer) {
                $response = '<a class="table-action-btn" title="Chỉnh sửa offer" href="' . route('offers.edit', $offer->id) . '"><i class="fa fa-pencil text-success"></i></a>  <a class="table-action-btn" data-offer="'.$offer->id.'" id="btn-test-' . $offer->id . '" title="Test Offer" data-url="' . route('offers.test', $offer->id) . '" href="javascript:;"><i class="fa fa-terminal text-warning"></i></a> <a class="table-action-btn" title="Clear Click IP" href="' . route('offers.clear', $offer->id) . '"><i class="fa fa-commenting text-warning"></i></a>';

                if ($offer->reject) {
                    $response .= '<a class="table-action-btn" data-url="' . route('offers.accept', $offer->id) . '" id="btn-accept-' . $offer->id . '"  title="Accept Offer" href="javascript:;"><i class="fa fa-unlock text-danger"></i></a>';
                } else {
                    $response .= '<a class="table-action-btn" data-url="' . route('offers.reject', $offer->id) . '" id="btn-reject-' . $offer->id . '"  title="Reject Offer" href="javascript:;"><i class="fa fa-lock text-danger"></i></a>';
                }

                return $response;

            })->addColumn('process', function ($offer) {
                $ok = null;
                if (strpos($offer->test_link, 'OK') !== false) {
                    $ok = '<div class="alert alert-success"><b>'.$offer->test_link.'</b></div>';
                }
                return '<div id="test_status_'.$offer->id.'">'.$ok.'</div>';
            })
            ->rawColumns(['network_name', 'status', 'action', 'name', 'allow_devices', 'redirect_link_for_user', 'check_click_in_network', 'allow_multi_lead', 'virtual_click', 'process'])
            ->make(true);
    }


    public static function exportToExcel($request)
    {
        ini_set('memory_limit', '2048M');

        $query = static::select('*')->latest('created_at');

        if ($request->filled('filter_name')) {
            $query->where('name', 'like', '%' . $request->get('filter_name') . '%');
        }

        if ($request->filled('filter_network_id')) {
            $query->where('network_id', $request->get('filter_network_id'));
        }

        if ($request->filled('filter_auto')) {
            $query->where('auto', $request->get('filter_auto'));
        }

        if ($request->filled('filter_uid')) {
            $query->where('id', $request->get('filter_uid'))
                ->orWhere('net_offer_id', $request->get('filter_uid'));
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->get('filter_status'));
        }

        if ($request->filled('filter_reject')) {
            $query->where('reject', $request->get('filter_reject'));
        }

        if ($request->filled('filter_country')) {
            $query->where('geo_locations', 'like', '%' . $request->get('filter_country') . '%');
        }

        if ($request->filled('filter_device')) {
            $searchDevice = urldecode($request->get('filter_device'));
            if ($searchDevice == 5) {
                $query->whereIn('allow_devices', [5, 6, 7]);
            } else {
                $query->where('allow_devices', $searchDevice);
            }
        }

        $reports = $query->get();

        return (new static())->createExcelFile($reports);
    }

    public function createExcelFile($reports)
    {
        $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(resource_path('templates/offers.xlsx'));

        $row = 2;
        foreach ($reports as $report) {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $row - 1)
                ->setCellValue('B'.$row, $report->network->name)
                ->setCellValue('C'.$row, $report->id)
                ->setCellValue('D'.$row, $report->net_offer_id)
                ->setCellValue('E'.$row, $report->name)
                ->setCellValue('F'.$row, $report->geo_locations)
                ->setCellValue('G'.$row, $report->click_rate)
                ->setCellValue('H'.$row, $report->redirect_link);

            $row++;
        }


        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $path = 'reports_'.date('Y_m_d_His').'.xlsx';

        $objWriter->save(storage_path('app/public/' . $path));

        return redirect('/storage/' . $path);
    }

}
