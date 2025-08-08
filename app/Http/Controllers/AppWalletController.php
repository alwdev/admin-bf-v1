<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transfer;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;


class AppWalletController extends Controller
{
    public function callback(Request $request) // walletconnect
    {
        $validated = $request->validate([
            'id' => 'required|string',
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

        Logs::create([
            'log' => 'walletconnect'.json_encode($validated)
        ]);

        Log::info('Payment received:', $validated);

        return response()->json(['success' => true]);
    }

    public function helio_callback(Request $request)
    {
        Logs::create([
            'log' => json_encode($request->getContent())
        ]);
        Log::info('Payment received:', $request->getContent());

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
        $data = json_decode($payload, true);
        Logs::create([
            'log' =>'moonpay'. json_decode($data)
        ]);


        if ($data['type'] === 'transaction_updated' && $data['data']['status'] === 'completed') {
            $tx = $data['data'];
            $externalCustomerId = strtolower($tx['externalCustomerId']);

            // สมมุติผูก wallet กับ user
            $user = \App\Models\Members::where('username', $externalCustomerId)->first();

            if ($user) {
                // update Transfers table
                $transfer = Transfer::where('amount', $tx['amount'])
                    ->where('member_id', $user->id)
                    ->where('status', '1')
                    ->where('type', 'deposit')
                    ->where('ref_id', $tx['id'])
                    ->first();
                if (!$transfer) {
                    Transfer::create([
                        'member_id' => $user->id,
                        'amount' => $tx['amount'],
                        'status' => '2',
                        'status_code' => 'completed',
                        'type' => 'deposit',
                        'ref_id' => $tx['id'],
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
                $user->increment('wallet_balance', $tx['amount']);
                $user->save();
                Log::info('MoonPay deposit success', [
                    'user_id' => $user->id,
                    'amount' => $tx['amount'],
                    'transaction_id' => $tx['id'],
                ]);
                return response()->json(['success' => true]);
            } else {
                Log::warning('MoonPay deposit failed: User not found', [
                    'externalCustomerId' => $externalCustomerId,
                    'transaction_id' => $tx['id'],
                ]);
                return response()->json(['error' => 'User not found'], 404);
            }
        } else {
            return response()->json(['Unhandled MoonPay event type: ' . $data]);
        }
    }
}
