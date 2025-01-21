<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PgHardController extends Controller
{
    public function pg_get_spin_summaryby_user($username,$date_start,$date_end){
        $headers = array();
		$headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: '.env('PGHARD_SECRET_KEY');
        $headers[] = 'accept: application/json';

        // $startDate = Carbon::today()->format('Y-m-d').'T00:00:00Z';
        // $endDate = Carbon::today()->format('Y-m-d').'T23:59:59Z';

        $startDate=date('Y-m-d',strtotime($date_start.' day'));
        $endDate=date('Y-m-d',strtotime($date_end.' day'));


        error_log($username);
        error_log($startDate);
        error_log($endDate);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.pghard.com/external/get-spin-order-by-username');
		curl_setopt($curl, CURLOPT_POSTFIELDS, '{
            "skip": 0,
            "take": 100,
            "startDate": "'.$startDate.'",
            "endDate": "'.$endDate.'",
            "username": "'.$username.'",
            "operatorId": "'.env('PGHARD_OPERATOR_ID').'",
            }');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }
}
