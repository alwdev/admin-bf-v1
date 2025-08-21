<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Members;

class LottoController extends Controller
{
    public function get_balance(Request $request)
    {
        $log = new \App\Models\Logs;
        $log->log = "Lotto Get Balance updated for {$request->username}";
        $log->save();
        $member = Members::where('username', $request->username)->first();
        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Member not found.'], 404);
        }
        return $member->wallet_balance;
    }
    // Ubdate Balance
    public function update_balance(Request $request)
    {
        $member = Members::where('username', $request->username)->first();
        $amount = $member->wallet_balance - $request->balance;
        if ($member) {
            $member->wallet_balance = $amount;
            $member->save();

            $bf_deposit = app(\App\Http\Controllers\BetflixController::class)->Master_Withdraw($member->username, ($request->balance));
            if ($bf_deposit == 'success') {
                // Log the deposit transaction
                $log = new \App\Models\Logs;
                $log->log = "Lotto Balance updated for {$member->username} with amount {$request->balance}";
                $log->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Balance updated successfully.'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Member not found.'], 404);
        }
    }

    // Bet lotto
    public function bet(Request $request)
    {
        $member = Members::where('username', $request->username)->first();
        $amount = $member->wallet_balance - $request->balance;
        if ($member) {

            $bf = app(\App\Http\Controllers\BetflixController::class)
                ->Master_Withdraw($member->username, $request->balance);

            if ($bf == 'success') {
                $member->wallet_balance = $amount;
                $member->save();
                // Log the bet transaction
                $log = new \App\Models\Logs;
                $log->log = "Lotto Bet placed by {$member->username} for amount {$request->balance}";
                $log->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Balance updated successfully.'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Member not found.'], 404);
        }
    }

    public function win(Request $request)
    {
        $member = Members::where('username', $request->username)->first();
        $amount = $member->wallet_balance - $request->balance;
        if ($member) {

            $bf = app(\App\Http\Controllers\BetflixController::class)
                ->Master_Deposit($member->username, $request->balance);

            if ($bf == 'success') {
                $member->wallet_balance = $amount;
                $member->save();
                // Log the bet transaction
                $log = new \App\Models\Logs;
                $log->log = "Lotto Win  by {$member->username} for amount {$request->balance}";
                $log->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Balance updated successfully.'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Member not found.'], 404);
        }
    }

    public function refun(Request $request)
    {
        $member = Members::where('username', $request->username)->first();
        $amount = $member->wallet_balance - $request->balance;
        if ($member) {

            $bf = app(\App\Http\Controllers\BetflixController::class)
                ->Master_Deposit($member->username, $request->balance);

            if ($bf == 'success') {
                $member->wallet_balance = $amount;
                $member->save();
                // Log the bet transaction
                $log = new \App\Models\Logs;
                $log->log = "Lotto Refun  by {$member->username} for amount {$request->balance}";
                $log->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Balance updated successfully.'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Member not found.'], 404);
        }
    }
}
