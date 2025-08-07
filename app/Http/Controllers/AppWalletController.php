<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transfer;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;


class AppWalletController extends Controller
{
    public function callback(Request $request)
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
            }else {
                $transfer->status = 3;
                $transfer->status_code = $request->status;
                $transfer->save();
            }
        }

        Logs::create([
            'log' => json_encode($validated)
        ]);

        Log::info('Payment received:', $validated);

        return response()->json(['success' => true]);
    }
}
