<?php

namespace App;

use GuzzleHttp\Client;

class Site
{

    public static function groupList()
    {
        return Group::pluck('name', 'id')->all();
    }

    public static function networkList()
    {
        return Network::pluck('name', 'id')->all();
    }


    public static function userList()
    {
        return User::pluck('username', 'id')->all();
    }

    public static function offerList()
    {
        $offers = Offer::where('reject', false)->where('status', true)->get();

        $response = [];

        foreach ($offers as $offer) {
            $response[$offer->id] = $offer->id.' - '.$offer->name;
        }

        return $response;
    }


    public static function parseOffer($offer, $network)
    {
        $isIphone = false;
        $isIpad = false;
        $android = false;
        $ios = false;
        $countries = [];
        $netOfferId = null;
        $redirectLink = null;
        $payout = 0;
        $offerName = null;
        $geoLocations = null;
        $devices = null;
        $realDevice = 1;


        #style 1

        if (isset($offer['devices'])) {
            $devices = $offer['devices'];
        }

        if (isset($offer['Platforms'])) {
            $devices = explode(',', $offer['Platforms']);
        }

        if (isset($offer['platform'])) {
            $devices = explode(',', $offer['platform']);
        }

        foreach ($devices as $device) {

            $deviceType = null;

            if (is_array($device)) {
                $deviceType = strtolower($device['device_type']);
            } else {
                $deviceType = strtolower($device);
            }

            if (strpos($deviceType, 'ios') !== false) {
                $ios = true;
            }

            if (strpos($deviceType, 'iphone') !== false) {
                $isIphone = true;
            }
            if (strpos($deviceType, 'ipad') !== false) {
                $isIpad = true;
            }
            if (strpos($deviceType, 'droid') !== false) {
                $android = true;
            }

            if ($isIphone && $isIpad) {
                $ios = true;
            }
        }

        if ($ios && $android) {
            $realDevice = 2;
        } else if ($android) {
            $realDevice = 4;
        } else if ($ios) {
            $realDevice = 5;
        } else if ($isIphone) {
            $realDevice = 6;
        } else if ($isIpad) {
            $realDevice = 7;
        }

        if (isset($offer['countries'])) {
            foreach ($offer['countries'] as $country) {
                $countries[]  = $country['code'];
            }
        }

        if (isset($offer['id'])) {
            $netOfferId = $offer['id'];
        }

        if (isset($offer['offer_id'])) {
            $netOfferId = $offer['offer_id'];
        }

        if (isset($offer['ID'])) {
            $netOfferId = $offer['ID'];
        }

        if (isset($offer['offerid'])) {
            $netOfferId = $offer['offerid'];
        }


        if (isset($offer['tracking_link'])) {
            $redirectLink = $offer['tracking_link'].'&aff_sub=#subId';
        }

        if (isset($offer['tracking_url'])) {
            $redirectLink = str_replace('&s1=&s2=&s3=', '&s1=#subId', $offer['tracking_url']);
        }

        if (isset($offer['Tracking_url'])) {
            $redirectLink = $offer['Tracking_url'].'&aff_sub=#subId';
        }

        if (isset($offer['offer_url'])) {
            $redirectLink = $offer['offer_url'].'&aff_sub=#subId';
        }


        if (isset($offer['payout'])) {
            $payout = $offer['payout'];
        }

        if (isset($offer['rate'])) {
            $payout = $offer['rate'];
        }

        if (isset($offer['Payout'])) {
            $payout = $offer['Payout'];
        }

        if (isset($offer['name'])) {
            $offerName = str_limit( $offer['name'], 250);
        }

        if (isset($offer['offer_name'])) {
            $offerName = str_limit( $offer['offer_name'], 250);
        }

        if (isset($offer['Name'])) {
            $offerName = str_limit( $offer['Name'], 250);
        }

        if (isset($offer['app_name'])) {
            $offerName = str_limit( $offer['app_name'], 250);
        }

        if (isset($offer['geos'])) {
            $geoLocations = implode(',', $offer['geos']);
        }

        if ($countries) {
            $geoLocations = implode(',', $countries);
        }

        if (isset($offer['Countries'])) {
            $geoLocations = $offer['Countries'];
        }

        if (isset($offer['geo'])) {
            $geoLocations = $offer['geo'];
        }

        if ($network->rate_offer > 0) {
            $payout = round(floatval(str_replace('$', '', $payout))/intval($network->rate_offer), 2);
        } else {
            $payout = round(floatval(str_replace('$', '', $payout))/intval(env('RATE_CRON')), 2);
        }


        $offerName = iconv(mb_detect_encoding($offerName, mb_detect_order(), true), "UTF-8", $offerName);

        $geoLocations = str_replace('|', ',', $geoLocations);

        $updated = [
            'name' => $offerName,
            'redirect_link' => $redirectLink,
            'click_rate' => $payout,
            'allow_devices' => $realDevice,
            'geo_locations' => $geoLocations,
            'status' => true,
            'auto' => true
        ];

        if ($network->virtual_click > 0) {
            $updated['number_when_click'] = $network->virtual_click;
        }

        if ($network->virtual_lead > 0) {
            $updated['number_when_lead'] = $network->virtual_lead;
        }


        Offer::updateOrCreate([
            'net_offer_id' => $netOfferId,
            'network_id' => $network->id,
        ],$updated);

        return $netOfferId;
    }


    public static function feed($network)
    {

        $feed_url = $network->cron;
        $offers = self::getUrlContent($feed_url);
        $listCurrentNetworkOfferIds = [];

        $total = 0;

        if ($offers) {
            $rawContent = isset($offers['offers']) ? $offers['offers'] : $offers;
            foreach ($rawContent as $offer) {
                $listCurrentNetworkOfferIds[] = self::parseOffer($offer, $network);
                $total ++;
            }
        }


        #update cac offer tu dong khong co trong API ve status inactive.

        if ($listCurrentNetworkOfferIds && !env('NO_UPDATE_CRON')) {
            $listCurrentNetworkOfferIds = array_unique($listCurrentNetworkOfferIds);

            Offer::where('auto', true)
                ->where('network_id', $network->id)
                ->whereNotIn('net_offer_id', $listCurrentNetworkOfferIds)
                ->update(['status' => false]);

        }
        return 'Total Offers Retrieved : '. $total;
    }

    public static function download($file_source, $file_target) {
        $rh = fopen($file_source, 'rb');
        $wh = fopen($file_target, 'w+b');
        if (!$rh || !$wh) {
            return false;
        }

        while (!feof($rh)) {
            if (fwrite($wh, fread($rh, 4096)) === FALSE) {
                return false;
            }
            flush();
        }

        fclose($rh);
        fclose($wh);

        return true;
    }

    public static function getUrlContent($url)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);

        $response = [];

        try {
            $client = new Client();
            $res = $client->request('GET', $url);
            $ticketResponse = $res->getBody();
            $response = json_decode($ticketResponse, true);
        } catch (\Exception $e) {

        }

        return $response;
    }
}