<?php namespace App\Http\Controllers\Backend;

use App\Click;
use App\Http\Controllers\Controller;
use App\Http\Requests\OfferRequest;
use App\Offer;
use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class OffersController extends Controller
{

    public function clear($id)
    {

        if ($offer = Offer::find($id)) {
            Click::where('offer_id', $id)->update(['click_ip' => '10.0.2.2']);
            flash()->success('Success!','Clear IP Lead success!');
            return redirect('admin/offers');
        } else {
            flash()->error('Error!','No Offer Found!');
            return redirect('admin/offers');
        }
    }


    public function reject($id)
    {
        $offer = Offer::find($id);

        $offer->reject = true;

        $offer->save();

        flash()->success('Success!', 'Offer successfully rejected.');

        return redirect()->back();
    }

    public function accept($id)
    {
        $offer = Offer::find($id);

        $offer->reject = false;

        $offer->save();

        flash()->success('Success!', 'Offer successfully accepted.');

        return redirect()->back();
    }

    public function test($id)
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
            return response()->json(['status' => true, 'msg' => $offer->test_link]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }

    }

    public function index()
    {
        return view('offers.index');
    }

    public function create()
    {
        return view('offers.create');
    }

    public function store(OfferRequest $request)
    {
        $request->store();

        flash()->success('Success!', 'Offer successfully created.');

        return redirect()->route('offers.index');
    }

    public function edit($id)
    {
        $offer = Offer::find($id);

        return view('offers.edit', compact('offer'));
    }

    public function update(OfferRequest $request, $id)
    {
        $request->save($id);

        flash()->success('Thành công', 'Cập nhật thành công!');

        return redirect()->route('offers.edit', $id);
    }

    public function dataTables(Request $request)
    {
        return Offer::getDataTables($request);
    }

}
