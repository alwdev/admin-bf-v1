<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Level;
use App\Models\Transfer;

class RankingController extends Controller
{
    //
    public function index(){
        $ranks = Level::orderby('rank','asc')->get();
        $member_ranks = $ranks->where('level_name',auth()->user()->ranking)->first();
        $deposit = Transfer::where('member_id',auth()->user()->id)->where('status',2)->where('type','deposit')->sum('amount');
        return view('profile.ranking',compact('ranks','member_ranks','deposit'));
    }
}
