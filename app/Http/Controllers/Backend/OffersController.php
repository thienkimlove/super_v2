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

    private function virtualCurl($isoCode, $url, $userAgent, $offer, $session, $currentRedirection = 0)
    {
        $username = 'lum-customer-theway_holdings-zone-nam-country-' . strtolower($isoCode);
        $password = '99oah6sz26i5';
        $port = 22225;
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

        $result = str_replace('\/', '/', $result);

        if ($currentRedirection < 6 &&
            isset($result) &&
            is_string($result) &&
            (preg_match('/window\.location\.replace\(["\']?(https?\:\/\/[^"\']+)/i', $result, $value) ||
                preg_match('/window.location\s*=\s*["\']?(https?\:\/\/[^"\']+)/i', $result, $value) ||
                preg_match('/window\.top\.location\.replace\(["\']?(https?\:\/\/[^"\']+)/i', $result, $value) ||
                preg_match("/window.top.location\s*=\s*[\"']?(https?\:\/\/[^\"']+)/i", $result, $value) ||
                preg_match('/meta\s*http-equiv\s*=\s*["\']?refresh["\']?\s*content=["\']?\d+;(?:url\s*=)?\s*[\'"]?(https?\:\/\/[^"\']+)/i', $result, $value) ||
                preg_match('/meta\s*http-equiv\s*=\s*["\']?refresh["\']?\s*content=["\']?\d+;\s*(?:url\s*=)?\s*[\'"]?(https?:\/\/[^"\']+)/i', $result, $value) ||
                preg_match('/meta\.content\s*=\s*["\']?\d+;\s*(?:url\s*=)?\s*[\'"]?(https?:\/\/[^"\']+)/i', $result, $value) ||
                preg_match("/location.href\s*=\s*[\"']?(https?\:\/\/[^\"']+)/i", $result, $value))) {

            return $this->virtualCurl($isoCode, $value[1], $userAgent, $offer, $session, ++$currentRedirection);

        }

        $responseUrl = $url;

        if (preg_match("/(itunes\.apple\.com)/im", $url, $matches)) {
            $responseUrl = 'OK '.$matches[1];
            $offer->update(['test_link' => $responseUrl]);
        }

        if (preg_match("/(play\.google\.com)/im", $url, $matches)) {
            $responseUrl = 'OK '.$matches[1];
            $offer->update(['test_link' => $responseUrl]);
        }

        if (strpos($responseUrl, 'OK') === false) {
            if ($result) {
                if (preg_match("/(itunes\.apple\.com)/im", $result, $matches)) {
                    $responseUrl = 'OK '.$matches[1];
                    $offer->update(['test_link' => $responseUrl]);
                }

                if (preg_match("/(play\.google\.com)/im", $result, $matches)) {
                    $responseUrl = 'OK '.$matches[1];
                    $offer->update(['test_link' => $responseUrl]);
                }
            }
        }

        $responseUrl = '<div class="alert alert-success">EndURL <b>'. $responseUrl.'</b></div>';


        if (strpos($responseUrl, 'OK') === false && $result) {
            $responseUrl .= '<br/><div class="alert alert-danger">'. preg_replace("/&#?[a-z0-9]{2,8};/i","",strip_tags($result)).'</div>';
        }

        return $responseUrl;

    }

    private function python($country, $url, $trueAgent, $offer)
    {
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

        } catch (\Exception $e) {
            $endHtml = $e->getMessage();
        }
        return $endHtml;

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
        $url = str_replace('#subid', '', $url);

        $testUrl = 'http://local.python.vn/core/test?allow='.$offer->allow_devices.'&link='.urlencode($url).'&country='.$country;

        $result = @file_get_contents($testUrl);

        if (strpos($result, 'OK!') !== false) {
            $class = 'success';
            $offer->update(['test_link' => $result]);
        } else {
            $class = 'danger';
        }

        $responseUrl = '<div class="alert alert-'.$class.'"><b>'.$result.'</b></div>';



        //$pythonUrl = $this->python($country, $url, $trueAgent, $offer);

        //return response()->json(['status' => true, 'msg' => '<div><span>PHP</span><br/>'.$phpUrl.'<br/><span>Python</span><br/>'.$pythonUrl.'</div>']);

        return response()->json(['status' => true, 'msg' => $responseUrl]);
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

    public function export(Request $request)
    {
        return Offer::exportToExcel($request);
    }

}
