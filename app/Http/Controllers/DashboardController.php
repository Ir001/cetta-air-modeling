<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
   public function index(){
        return view('dashboard');
   }

   public function store(Request $request){
        $this->validate($request, [

        ]);
        $title = Str::title($request->title);
        $subtitle = Str::title($request->title);
        $file = Str::slug($title)."-".Str::random(4);
        $coordinatesLat = $request->lat;
        $coordinatesLon = $request->lon;
        $labels = $request->label;
        $locations = "";
        $zone = 1;
        foreach ($coordinatesLat as $key => $lat) {
            $lat = $coordinatesLat[$key];
            $lon = $coordinatesLon[$key];
            $label = $labels[$key];
            $utm = $this->getUTM($lon,$lat)[0];
            $xUTM = round(@$utm['easting']);
            $yUTM = round(@$utm['northing']);
            $locations.="   LOCATION  {$label}  POINT     {$xUTM}.  {$yUTM}.\n";
        }
        $response=null;
        $AERMAP = file_get_contents(__DIR__.'/../../../public/AERMAP.cfg');
        eval("\$response = \"$AERMAP\";");
        return response()->json(['success' => true, 'data' => $response]);
   }
   public function getUTM($lon, $lat){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://www.latlong.net/dec2utm.php?lat={$lat}&long={$lon}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = 'Host: www.latlong.net';
        $headers[] = 'Cookie: _ga=GA1.2.1342904562.1655195677; PHPSESSID=t5ql6uuqhqq4brbst7h46o0an6; _gid=GA1.2.1857140321.1656993750; OptanonAlertBoxClosed=2022-07-05T04:02:30.850Z; OptanonConsent=isGpcEnabled=0&datestamp=Tue+Jul+05+2022+11%3A02%3A31+GMT%2B0700+(Western+Indonesia+Time)&version=6.34.0&isIABGlobal=false&hosts=&landingPath=NotLandingPage&groups=C0003%3A1%2CC0004%3A1%2CC0002%3A1%2CC0001%3A1&AwaitingReconsent=false';
        $headers[] = 'Sec-Ch-Ua: \".Not/A)Brand\";v=\"99\", \"Google Chrome\";v=\"103\", \"Chromium\";v=\"103\"';
        $headers[] = 'Sec-Ch-Ua-Mobile: ?0';
        $headers[] = 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36';
        $headers[] = 'Sec-Ch-Ua-Platform: \"Linux\"';
        $headers[] = 'Accept: */*';
        $headers[] = 'Sec-Fetch-Site: same-origin';
        $headers[] = 'Sec-Fetch-Mode: cors';
        $headers[] = 'Sec-Fetch-Dest: empty';
        $headers[] = 'Referer: https://www.latlong.net/lat-long-utm.html';
        $headers[] = 'Accept-Language: en-US,en;q=0.9,id;q=0.8';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = json_decode(curl_exec($ch),true);
        if (curl_errno($ch)) {
            // echo 'Error:' . curl_error($ch);
            return null;
        }
        curl_close($ch);
        return $result;
   }
}
