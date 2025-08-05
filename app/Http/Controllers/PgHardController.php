<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Members;
use App\Models\PgHard;

class PgHardController extends Controller
{
    public function pghard_report(){
        // ดึง members ที่มีข้อมูลใน PgHard
        $members = Members::whereIn('username', function($query) {
            $query->select('username')->from('pg_hards');
        })->get();
        $report = [];
        foreach ($members as $member) {
            $username = $member->username;
            $date_start = Carbon::now()->subDays(30)->format('Y-m-d');
            $date_end = Carbon::now()->format('Y-m-d');

            // ดึงข้อมูลสรุปการหมุนวงล้อ
            $spin_summary = $this->pg_get_spin_summaryby_user($username, $date_start, $date_end);
            if (isset($spin_summary['data']) && count($spin_summary['data']) > 0) {
                foreach ($spin_summary['data'] as $summary) {
                    $report[] = [
                        'username' => $username,
                        'gameId' => $summary['gameId'],
                        'totalBet' => $summary['totalBet'],
                        'totalWin' => $summary['totalWin'],
                        'totalProfit' => $summary['totalProfit'],
                        'date' => Carbon::parse($summary['createdAt'])->format('Y-m-d')
                    ];
                }
            }

            // ดึงข้อมูลการหมุนวงล้อโดยละเอียด
            $spin_orders = $this->get_spin_orderby_username($username, $date_start, $date_end);
            if (isset($spin_orders['data']) && count($spin_orders['data']) > 0) {
                foreach ($spin_orders['data'] as $order) {
                    PgHard::updateOrCreate(
                        ['transactionId' => $order['transactionId']],
                        [
                            'username' => $username,
                            'payoff' => $order['payoff'],
                            'betAmount' => $order['betAmount'],
                            'userToken' => $order['userToken'],
                            'roundId' => $order['roundId'],
                            'gameId' => $order['gameId'],
                            'gameStringId' => $order['gameStringId'],
                            'gameName' => $order['gameName']
                        ]
                    );
                }
            }
        }
        return $report;
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
