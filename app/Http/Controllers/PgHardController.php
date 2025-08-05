<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Members;
use App\Models\PgHard;

class PgHardController extends Controller
{
    public function pghard_report($date_start,$date_end){
        $members = Members::whereIn('username', function($query) {
            $query->select('username')->from('pg_hards');
        })->get();
        $report = [];
        foreach ($members as $member) {
            $username = $member->username;
            $startDate=date('Y-m-d',strtotime($date_start.' day')).'T00:00:00Z';
            $endDate=date('Y-m-d',strtotime($date_end.' day')).'T23:59:59Z';
            $dateStr =date('Y/m/d',strtotime($date_start.' day')).'-'.date('Y/m/d',strtotime($date_end.' day'));
            $spin_summary = $this->pg_get_spin_summaryby_user($username, $date_start, $date_end);

            if (isset($spin_summary['data']) && count($spin_summary['data']) > 0) {
                foreach ($spin_summary['data'] as $summary) {
                    $report[] = [
                        'username' => $username,
                        'totalAmount' => $summary['totalAmount'],
                        'totalPayoff' => $summary['totalPayoff'],
                        'totalSuccessSpin' => $summary['totalSuccessSpin'],
                        'totalSuccessMainSpin' => $summary['totalSuccessMainSpin']
                    ];
                }
            }
        }
        // return $report;
        return view('report.pghard_report', compact('report','dateStr','date_start','date_end'));
    }
    public function pghard_detail_report($username,$start_day,$end_day){

        $dateStr =date('Y/m/d',strtotime($start_day.' day')).'-'.date('Y/m/d',strtotime($end_day.' day'));
        $report_ = $this->get_spin_orderby_username($username, $start_day, $end_day);
        $report = $report_['data'] ?? [];
        // return $report['data'];
        return view('report.pghard_detail_report', compact('report','username','dateStr'));
    }
    public function pg_get_spin_summaryby_user($username,$date_start,$date_end){

        $startDate=date('Y-m-d',strtotime($date_start.' day')).'T00:00:00Z';
        $endDate=date('Y-m-d',strtotime($date_end.' day')).'T23:59:59Z';


        // error_log($username);
        // error_log($startDate);
        // error_log($endDate);

        $curl = curl_init();
        $payload = [
            "skip" => 0,
            "take" => 1000,
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
    public function get_spin_orderby_username($username,$date_start,$date_end){

        $startDate=date('Y-m-d',strtotime($date_start.' day')).'T00:00:00Z';
        $endDate=date('Y-m-d',strtotime($date_end.' day')).'T23:59:59Z';


        error_log($username);
        error_log($startDate);
        error_log($endDate);

        $curl = curl_init();
        $payload = [
            "skip" => 0,
            "take" => 1000,
            "startDate" => $startDate,
            "endDate" => $endDate,
            "username" => $username,
            "operatorId" => env('PGHARD_OPERATOR_ID')
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.pghard.com/external/get-spin-order-by-username",
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
