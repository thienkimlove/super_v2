<?php namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\NetworkClick;
use Illuminate\Http\Request;


class NetworkClicksController extends Controller
{


    public function index()
    {
        return view('leads.index');
    }

    public function dataTables(Request $request)
    {
        return NetworkClick::getDataTables($request);
    }

    public function export(Request $request)
    {
        return NetworkClick::exportToExcel($request);
    }

}
