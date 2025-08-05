<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Members;

class LottoController extends Controller
{
    public function get_balance(Request $request){
        $member = Members::where('username', $request->username)->first();
        return $member->wallet_balance;
    }
    // Ubdate Balance
    public function update_balance(Request $request){
        $member = Members::where('username', $request->username)->first();
        if ($member) {
            $member->wallet_balance = $request->balance;
            $member->save();
            return response()->json(['status' => 'success', 'message' => 'Balance updated successfully.'], 200);
        } else {
            return response()->json(['status' => 'error','message' => 'Member not found.'], 404);
            }
    }
}
