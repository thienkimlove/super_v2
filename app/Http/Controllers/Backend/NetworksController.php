<?php namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\NetworkRequest;
use App\Network;
use App\Site;
use Illuminate\Http\Request;


class NetworksController extends Controller
{

    public function cron($id)
    {
        set_time_limit(0);

        $network = Network::find($id);

        try {
            if ($network->cron) {
                $message = Site::feed($network);
                flash()->success('Success!', $message);
                return redirect()->route('networks.index');
            }
        } catch (\Exception $e) {
            flash()->error('Error!', $e->getMessage());
            return redirect()->route('networks.index');
        }


    }

    public function index()
    {
        return view('networks.index');
    }

    public function create()
    {
        return view('networks.create');
    }

    public function store(NetworkRequest $request)
    {
        $request->store();

        flash()->success('Success!', 'Network successfully created.');

        return redirect()->route('networks.index');
    }

    public function edit($id)
    {
        $network = Network::find($id);

        return view('networks.edit', compact('network'));
    }

    public function update(NetworkRequest $request, $id)
    {
        $request->save($id);

        flash()->success('Thành công', 'Cập nhật thành công!');

        return redirect()->route('networks.edit', $id);
    }

    public function dataTables(Request $request)
    {
        return Network::getDataTables($request);
    }

}
