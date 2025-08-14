<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transfer;
use App\Models\Logs;
use Illuminate\Support\Facades\Http;


class AppWalletController extends Controller
{
    public function callback(Request $request) // walletconnect
    {
        $validated = $request->validate([
            'id' => 'required|string',
        ]);
        Logs::create([
            'log' => 'walletconnect callback' . json_encode($request->getContent())
        ]);

        $transfer = Transfer::where('ref_id', $request->id)->first();
        if ($transfer) {
            if ($request->status == 'success') {
                $transfer->amount = $this->cryptoToTHB($request->symbol, $request->amount);
                $transfer->status = 2;
                $transfer->status_code = 'อนุมัติ';
                $transfer->save();
            } else {
                $transfer->status = 3;
                $transfer->status_code = $request->status;
                $transfer->save();
            }
        }

        return response()->json(['success' => true]);
    }

   function cryptoToTHB(string $symbol, float $amount): ?float
    {
        $apiKey = env('CMC_API_KEY');
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest';

        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $apiKey,
            'Accept' => 'application/json'
        ])->get($url, [
            'symbol' => strtoupper($symbol),
            'convert' => 'THB'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $price = $data['data'][strtoupper($symbol)]['quote']['THB']['price'] ?? null;

            if ($price !== null) {
                return $price * $amount;
            }
        }

        return null; // ถ้าไม่เจอข้อมูล
    }
    public function helio_callback(Request $request)
    {
        Logs::create([
            'log' => json_encode($request->getContent())
        ]);

        $transfer = Transfer::where('ref_id', $request->id)->first();
        if ($transfer) {
            if ($request->status == 'success') {
                $transfer->status = 2;
                $transfer->status_code = 'อนุมัติ';
                $transfer->save();
            } else {
                $transfer->status = 3;
                $transfer->status_code = $request->status;
                $transfer->save();
            }
        }

        return response()->json(['success' => true]);
    }

    public function moonpay_handle(Request $request)
    {
        $payload = $request->getContent();
        $data = json_decode($payload);
        if (isset($data->data) && is_string($data->data)) {
            $nested = json_decode($data->data);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data->data = $nested;
            }
        }
        Logs::create([
            'log' => 'moonpay' . json_encode($data)
        ]);
        // Log::info('MoonPay type:', gettype($data));


        if ($data->type === 'transaction_updated' && $data->data->status === 'completed') {
            $tx = $data->data;
            $externalCustomerId = strtolower($tx->externalCustomerId);

            // สมมุติผูก wallet กับ user
            $user = \App\Models\Members::where('username', $externalCustomerId)->first();

            if ($user) {
                // update Transfers table
                $transfer = Transfer::where('amount', $tx->amount)
                    ->where('member_id', $user->id)
                    ->where('status', '1')
                    ->where('type', 'deposit')
                    ->where('ref_id', $tx->id)
                    ->first();
                if (!$transfer) {
                    Transfer::create([
                        'member_id' => $user->id,
                        'amount' => $tx->amount,
                        'status' => '2',
                        'status_code' => 'completed',
                        'type' => 'deposit',
                        'ref_id' => $tx->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $transfer->update([
                        'status' => '2',
                        'status_code' => 'completed',
                        'updated_at' => now(),
                    ]);
                }
                $user->increment('wallet_balance', $tx->amount);
                $user->save();

                return response()->json(['success' => true]);
            } else {

                return response()->json(['error' => 'User not found'], 404);
            }
        } else {
            return response()->json(['Unhandled MoonPay event type: '], 400);
        }
    }
}
