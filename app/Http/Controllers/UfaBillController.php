<?php

namespace App\Http\Controllers;

use App\Models\UfaTransaction;
use Illuminate\Http\Request;

class UfaBillController extends Controller
{
    public function show(Request $request, string $betId)
    {
        $tx = UfaTransaction::where('bet_id', $betId)->orderByDesc('id')->firstOrFail();

        $apiUrl = rtrim((string) env('UFABET_BILL_API_URL', 'https://api.ax928.com'), '/');
        $apiKey = (string) env('UFABET_BILL_APIKEY', '');

        $apiEnabled = $apiKey !== '';
        $apiError = null;
        $apiResponseRaw = null;
        $apiResponseJson = null;

        if ($apiEnabled) {
            try {
                $client = new \GuzzleHttp\Client([
                    'timeout' => 12,
                    'connect_timeout' => 6,
                ]);

                $resp = $client->request('GET', $apiUrl . '/bill', [
                    'query' => [
                        'apikey' => md5($apiKey),
                        'user' => $tx->username,
                        'bill_id' => $betId,
                    ],
                ]);

                $apiResponseRaw = (string) $resp->getBody();
                $decoded = json_decode($apiResponseRaw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $apiResponseJson = $decoded;
                }
            } catch (\Throwable $e) {
                $apiError = $e->getMessage();
            }
        } else {
            $apiError = 'UFABET_BILL_APIKEY is not configured';
        }

        return view('report.ufa_bill_detail', compact(
            'tx',
            'betId',
            'apiUrl',
            'apiEnabled',
            'apiError',
            'apiResponseRaw',
            'apiResponseJson'
        ));
    }
}

