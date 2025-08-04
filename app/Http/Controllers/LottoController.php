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
}
