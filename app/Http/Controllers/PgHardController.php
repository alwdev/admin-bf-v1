<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PgHardController extends Controller
{
    public function pg_get_spin_summaryby_user($username,$date_start,$date_end){

        $startDate=date('Y-m-d',strtotime($date_start.' day')).'T00:00:00Z';
        $endDate=date('Y-m-d',strtotime($date_end.' day')).'T23:59:59Z';


        // error_log($username);
        // error_log($startDate);
        // error_log($endDate);

        $curl = curl_init();
        $payload = [
            "skip" => 0,
            "take" => 10,
            "startDate" => $startDate,
            "endDate" => $endDate,
            "username" => $username,
            "operatorId" => env('PGHARD_OPERATOR_ID')
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.pghard.com/external/get-spin-order-summary-by-usernames",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                "Authorization: ".env('PGHARD_SECRET_KEY'),
                "Content-Type: application/json"
            ]
        ]);

        $response = curl_exec($curl);
        $data = json_decode($response, true);
        curl_close($curl);
        return $data;
    }
}
