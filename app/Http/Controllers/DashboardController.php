<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Transfer;
use App\Models\Members;
use App\Models\Payout;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller
{
    public function index(){
        $member = Members::get();
        $member_new = Members::whereDate('created_at', Carbon::today())->orderby('id','desc')->get();
        $transfer = Transfer::where('status',2)->whereDate('created_at', Carbon::today())->where('type','!=','cashback')->get();
        $total_deposit = 0;
        $total_withdraw = 0;
        $total_member = 0;
        $total_online = 0;
        $new_member = 0;
        if($member_new){
            foreach ($member_new as $key => $value) {
                    $new_member++;
            }

        }
        $total_member = count($member);
        if($transfer){
            foreach($transfer as $t){
                if($t->type == 'deposit'){
                    $total_deposit += $t->amount;
                }else{
                    $total_withdraw += $t->amount;
                }
            }
        }

            $players =[];

            $topgame = [];

            $transfer = Transfer::join('members',function($join){
                $join->on('members.id','=','transfer.member_id');
            })
            ->select(DB::raw('transfer.*,members.bank_number,members.account_name,members.bank_name,members.username'))
            ->orderby('transfer.created_at','desc')->limit(5)
            ->get();


        return view('welcome', compact('total_deposit', 'total_withdraw','new_member','total_member','players','total_online','topgame','transfer','member_new'));
    }

    public function dashboard_date(Request $request){

        $d = explode('-',$request->date_);
        $dateS = Carbon::parse($d[0]);
        $dateE = Carbon::parse($d[1]);

        $member = Members::get();
        $member_new = Members::whereBetween('created_at', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"])->orderby('id','desc')->get();
        $transfer = Transfer::where('status',2)->whereBetween('created_at', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"])->where('type','!=','cashback')->get();
        $total_deposit = 0;
        $total_withdraw = 0;
        $total_member = 0;
        $total_online = 0;
        $new_member = 0;
        if($member_new){
            foreach ($member_new as $key => $value) {
                    $new_member++;
            }

        }
        $total_member = count($member);
        if($transfer){
            foreach($transfer as $t){
                if($t->type == 'deposit'){
                    $total_deposit += $t->amount;
                }else{
                    $total_withdraw += $t->amount;
                }
            }
        }
            $players = DB::table('gplay')
            ->select(DB::raw('hour(created_at) as hour'),'playerUsername' )  //  \DB::raw('COUNT(username) as count')
            ->groupBy('hour', 'playerUsername')
            ->where('eventName','debit')
            ->whereBetween('created_at', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"])
            ->get()->groupBy('hour');

            foreach ($players as $hour) {
                $total_online += count($hour);
            }

            $topgame = DB::table('gplay')
            ->select(DB::raw('COUNT(gameName) as count'),'gameName' )  //
            ->groupBy('gameName')
            ->where('eventName','debit')
            ->whereBetween('created_at', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"])->limit(10)
            ->get();

            $transfer = Transfer::join('members',function($join){
                $join->on('members.id','=','transfer.member_id');
            })
            ->select(DB::raw('transfer.*,members.bank_number,members.account_name,members.bank_name,members.username'))
            ->whereBetween('transfer.created_at', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"])
            ->orderby('transfer.created_at','desc')->limit(5)
            ->get();


        return view('welcome', compact('total_deposit', 'total_withdraw','new_member','total_member','players','total_online','topgame','transfer','member_new'));
    }
}
