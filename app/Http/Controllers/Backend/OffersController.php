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

    private function virtualCurl($isoCode, $url, $userAgent, $currentRedirection = 0)
    {
        $username = 'lum-customer-theway_holdings-zone-nam-country-' . strtolower($isoCode);
        $password = '99oah6sz26i5';
        $port = 22225;
        $session = mt_rand();
        $super_proxy = 'zproxy.luminati.io';
        $url = str_replace("&amp;", "&", urldecode(trim($url)));
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_PROXY, "http://$super_proxy:$port");
        curl_setopt($curl, CURLOPT_PROXYUSERPWD, "$username-session-$session:$password");
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); //timeout in seconds

        $result = curl_exec($curl);
        curl_close($curl);

        if (preg_match("/itunes.apple.com/i", $result, $value) || preg_match("/play.google.com/i", $result, $value)) {
            return ['OK', $result];
        }

        if ($currentRedirection < 6 &&
            isset($result) &&
            is_string($result) &&
            (preg_match("/window.location.replace('(.*)')/i", $result, $value) ||
                preg_match("/window.top.location\s*=\s*[\"'](.*)[\"']/i", $result, $value) ||
                preg_match("/window.location\s*=\s*[\"'](.*)[\"']/i", $result, $value) ||
                preg_match("/meta\s*http-equiv\s*=\s*[\"']refresh['\"]\s*content=[\"']\d+;url\s*=\s*(.*)['\"]/i", $result, $value) ||
                preg_match("/location.href\s*=\s*[\"'](.*)[\"']/i", $result, $value))) {
            return $this->virtualCurl($isoCode, $value[1], $userAgent, ++$currentRedirection);
        } else {
            return [$url, $result];
        }
    }


    public function test3($id)
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


        $trueAgent = ($offer->allow_devices > 4) ? 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_1 like Mac OS X) AppleWebKit/602.2.14 (KHTML, like Gecko) Mobile/14B72' : 'Mozilla/5.0 (Linux; Android 5.0.1; SAMSUNG SM-N920K Build/LRX22C) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/3.4 Chrome/34.0.1847.118 Mobile Safari/537.36';

        list($endUrl, $result) = $this->virtualCurl($country, $url, $trueAgent);

        file_put_contents(public_path('test/'.$offer->id.'_last.html'), $result.$country);

        return response()->json(['status' => true, 'msg' => $endUrl]);
    }

    public function test5($id)
    {

        $offer = Offer::find($id);
        $trueAgent = ($offer->allow_devices > 4) ? 'ios' : 'android';
        $url = str_replace('#subId', '', $offer->redirect_link);

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


        $url = 'https://offertest.net/offertest/?country='.$country.'&os='.$trueAgent.'&target=android&url='.$url;
        $session = mt_rand();

        $background = file_get_contents(resource_path('test.py'));
        $background = str_replace(['#URL#', '#OFFERID#'], [$url, $offer->id], $background);

        $tempPythonFile = '/tmp/exe_'.$session.'_.py';
        file_put_contents($tempPythonFile, $background);
        $endHtml = null;

        try {
            $process = new Process('python '.$tempPythonFile, '/tmp', null, null, 120);
            $process->run();
            return response()->json(['status' => true, 'msg' => '<img src="'.url('test/'.$offer->id.'_last.png').'" height="100" width="100" />']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }

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

        #iOS 10.2.1
        #Samsung s8
        $trueAgent = ($offer->allow_devices > 4) ? 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_2_1 like Mac OS X) AppleWebKit/602.4.6 (KHTML, like Gecko) Version/10.0 Mobile/14D27 Safari/602.1' : 'Mozilla/5.0 (Linux; Android 7.0; SAMSUNG SM-G950F Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/5.2 Chrome/51.0.2704.106 Mobile Safari/537.36';

        $username = 'lum-customer-theway_holdings-zone-nam-country-' . strtolower($country);
        $session = mt_rand();

        $background = file_get_contents(resource_path('read.py'));
        $background = str_replace(['#URL#', '#USERNAME#', '#AGENT#', '#OFFERID#'], [$url, $username.'-session-'.$session, $trueAgent, $offer->id], $background);

        $tempPythonFile = '/tmp/exe_'.$session.'_.py';
        file_put_contents($tempPythonFile, $background);
        $endHtml = null;

        try {
            $process = new Process('python '.$tempPythonFile, '/tmp', null, null, 120);
            $process->run();

            // executes after the command finishes
            if (!$process->isSuccessful()) {
                unlink($tempPythonFile);
                throw new ProcessFailedException($process);
            } else {
                $output = $process->getOutput();
                if (strpos($output, 'END_OF_LINE') !== FALSE) {
                    $outputArs = explode('END_OF_LINE', $output);
                    foreach ($outputArs as $outputAr) {
                        $endHtml .= '<span>'.$outputAr.'</span><br/>';
                    }
                } else {
                    $endHtml = '<span>'.$output.'</span><br/>';
                }
                unlink($tempPythonFile);
                $html = $offer->id.'_last.html';
                $image = $offer->id.'_last.png';
                if (file_exists('/tmp/'.$html)) {
                    rename('/tmp/'.$html, public_path('test/'.$html));
                }

                if (file_exists('/tmp/'.$image)) {
                    rename('/tmp/'.$image, public_path('test/'.$image));
                }

            }

            if ($endHtml && is_string($endHtml)) {
                if (preg_match("/(itunes\.apple\.com)/im", $endHtml, $matches)) {
                    $endHtml = 'OK '.$matches[1];
                    $offer->update(['test_link' => $endHtml]);
                }

                if (preg_match("/(play\.google\.com)/im", $endHtml, $matches)) {
                    $endHtml = 'OK '.$matches[1];
                    $offer->update(['test_link' => $endHtml]);
                }

            }

            if (file_exists(public_path('test/'.$offer->id.'_last.html'))) {
                $endHtml .= '<br/><span><a href="'.url('test/'.$offer->id.'_last.html').'" target="_blank">Debug</a></span>';
            }

            if (file_exists(public_path('test/'.$offer->id.'_last.png'))) {
                $endHtml .= '<br/><span><img src="'.url('test/'.$offer->id.'_last.png').'" height="100" width="auto" /></span>';
            }

            return response()->json(['status' => true, 'msg' => $endHtml]);
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
