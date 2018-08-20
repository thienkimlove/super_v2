<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function notice()
    {
        return view('notice');
    }

    public function sshView()
    {
        return view('ssh_view');
    }

    private function getCountryAndCountryCodeByIp()
    {

    }

    public function sshTool(Request $request)
    {
        $content = array();
        if ($request->hasFile('name')) {
            $file = $request->file('name');
            $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES);
            foreach ($lines as $line) {
                $temp = explode('|', $line);
                $getIp = geoip()->getLocation($temp[0]);
                $isoCode = $getIp['iso_code'];
                $country = $getIp['country'];
                $content[] = "$temp[0]|$temp[1]|$temp[2]|$country($isoCode)||";
            }


            return response(implode("\r\n", $content), 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="ssh.txt"',
            ]);
        }
    }
}
