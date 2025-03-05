<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Members;
use App\Models\Payout;
use App\Models\MemberEditBalance;
use Illuminate\Support\Carbon;
use App\Models\Transfer;
use App\Models\CommonCallback;
use App\Models\SexyCallback;
use App\Models\ProductList;
use App\Models\WmCallback;
use App\Models\Bank;
use App\Models\Gplay;
use App\Models\Wrongdeposit;
use Illuminate\Support\Facades\DB;
use DateTime;

class ReportController extends Controller
{
    public function list_member_play($date,$date_end){
        if($date == 0 && $date ==0){
            $date = Carbon::now()->format('Y-m-d');
            $date_end = Carbon::now()->format('Y-m-d');
        }else{
            $date =  Carbon::parse($date)->format('Y-m-d');
            $date_end =  Carbon::parse($date_end)->format('Y-m-d');
        }
        $startTime = $date."T00:00:00Z";
        $endTime = $date_end."T23:59:00Z";
        $firstDate = $date;
        $lastDate = $date_end;

        $date1 = $date;
        $date2 = $date_end;

        $date_id = 0;

        $member_play = Gplay::select('playerUsername')->whereBetween('created_at', [$firstDate." 00:00:00", $lastDate." 23:59:59"])->groupby('playerUsername')->get();

        foreach ($member_play as $key => $his) {



            $total_bet = 0;
            $winlose = 0;

            $nextId=1;

            $Gplay = Gplay::select('productName')->where('playerUsername',$his->playerUsername)->whereBetween('created_at', [$firstDate." 00:00:00", $lastDate." 23:59:59"])->groupby('productName')->get();

            foreach ($Gplay as $key => $value) {
                $productId = $value->provider;
                if($value->provider == 'PGSOFT'){
                    $productId = "PGSOFT2";
                }
                // $productId = "PRAGMATIC_SLOT";

                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', env('APP_ASK_API_URL'). "/betTransactionsV2", [
                    "query" => [
                        "productId" => $productId,
                        "date"  => $date,
                        "startTime" => $startTime,
                        "endTime" => $endTime,
                        "nextId" => $nextId,
                    ],
                    'headers' => [
                        'Authorization' => 'Basic '. $this->auth_basic(),
                        'Content-Type' => 'application/json'
                    ]
                ]);
                $data = json_decode($response->getBody());
                // dd($data);
                if(isset($data->data)){

                    foreach ($data->data->txns as $key => $item) {

                        if($his->playerUsername== $item->playerUsername){
                            $total_bet += $item->stake;
                            if(strtolower($item->payoutStatus) == "lose"){
                                $winlose = $winlose - $item->stake + $item->payout;
                            }elseif(strtolower($item->payoutStatus) == "win"){
                                $winlose = $winlose + $item->payout - $item->stake;
                            }
                        }
                    }
                }
            }

            $his->amount = $total_bet;
            $his->winlose = $winlose;
        }

        return view('report.list_memberplay',compact('member_play','date1','date2','date_id','date','date_end'));
    }

    public function member_play_name($username,$date_id){
        if($date_id == 0){
            $firstDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 23:59:59';
        }else if($date_id == 1){
            $firstDate = Carbon::yesterday()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::yesterday()->isoFormat('YYYY-MM-DD').' 23:59:59';
        }else if($date_id == 2){
            $lastDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $firstDate = Carbon::now()->subDays(7)->isoFormat('YYYY-MM-DD').' 23:59:59';
        }else if($date_id = 3){
            $firstDate = Carbon::now()->startOfMonth()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::now()->endOfMonth()->isoFormat('YYYY-MM-DD').' 23:59:59';
        }

        $member_play = Gplay::whereBetween('created_at', [$firstDate,$lastDate ])
        ->where('playerUsername',$username)
        ->where('eventName','!=','balance')
        ->orderby('created_at','DESC')
        ->get();

        return view('report.index_v2',compact('member_play'));
    }
    protected $casino = ['SEXY','SEXYBCRT','CQ9_LIVECASINO','DREAM2','MICRO_LIVECASINO','PRAGMATIC_LIVECASINO','PRETTY','SA Gaming','WECASINO','WM Casino','YEEBET','ALLBET','AGGAME','Big Gaming','BETGAME'];
    protected $sports =['SBO','SABASPORTS','AMBSPORTBOOK','COCKFIGHT'];

    public function member_play_casino($date_id){
        if($date_id == 0){
            $firstDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 23:59:59';
        }else if($date_id == 1){
            $firstDate = Carbon::yesterday()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::yesterday()->isoFormat('YYYY-MM-DD').' 23:59:59';
        }else if($date_id == 2){
            $firstDate = Carbon::now()->subDays(7)->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 23:59:59';
        }else if($date_id = 3){
            $firstDate = Carbon::now()->startOfMonth()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::now()->endOfMonth()->isoFormat('YYYY-MM-DD').' 23:59:59';
        }
        $member_play = array();
        $memberplay = Gplay::whereBetween('created_at', [$firstDate,$lastDate ])
        ->orderby('created_at','DESC')->limit(10000)
        ->get();
        foreach ($memberplay as $key => $value) {
            if(in_array($value->provider,$this->casino)){
               array_push($member_play,$value);
            }

        }
        return view('report.casino_report',compact('member_play','date_id'));
    }

    public function member_play_sport($date_id){
        if($date_id == 0){
            $firstDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 23:59:59';
        }else if($date_id == 1){
            $firstDate = Carbon::yesterday()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::yesterday()->isoFormat('YYYY-MM-DD').' 23:59:59';
        }else if($date_id == 2){
            $firstDate = Carbon::now()->subDays(7)->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 23:59:59';
        }else if($date_id = 3){
            $firstDate = Carbon::now()->startOfMonth()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::now()->endOfMonth()->isoFormat('YYYY-MM-DD').' 23:59:59';
        }
        $member_play = array();
        $memberplay = Gplay::whereBetween('created_at', [$firstDate,$lastDate ])
        ->orderby('created_at','DESC')->limit(10000)
        ->get();
        foreach ($memberplay as $key => $value) {
            if(in_array($value->provider,$this->sports)){
               array_push($member_play,$value);
            }

        }
        return view('report.sport_report',compact('member_play','date_id'));
    }

    public function member_play_egame($date_id){
        if($date_id == 0){
            $firstDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 23:59:59';
        }else if($date_id == 1){
            $firstDate = Carbon::yesterday()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::yesterday()->isoFormat('YYYY-MM-DD').' 23:59:59';
        }else if($date_id == 2){
            $firstDate = Carbon::now()->subDays(7)->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 23:59:59';
        }else if($date_id = 3){
            $firstDate = Carbon::now()->startOfMonth()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::now()->endOfMonth()->isoFormat('YYYY-MM-DD').' 23:59:59';
        }
        $member_play = array();
        $memberplay = Gplay::whereBetween('created_at', [$firstDate,$lastDate ])
        ->orderby('created_at','DESC')->limit(10000)
        ->get();
        foreach ($memberplay as $key => $value) {
            if((!in_array($value->provider,$this->sports)) && (!in_array($value->provider,$this->casino))){
               array_push($member_play,$value);
            }

        }
        return view('report.egame_report',compact('member_play','date_id'));
    }
    public function member_play(){
        $day= 'today';
        $member_play = Gplay::whereDate('created_at', Carbon::$day())
        ->where('eventName','!=','balance')->where('amount','>',0)
        ->orderby('created_at','DESC')
        ->limit(10000)
        ->get();

        return view('report.index',compact('member_play'));
    }
    public function member_play_v2(){
        $day= 'today';
        $member_play = Gplay::whereDate('created_at', Carbon::$day())
        ->where('eventName','!=','balance')->where('amount','>',0)
        ->orderby('created_at','DESC')
        ->limit(10000)
        ->get();

        return view('report.index_v2',compact('member_play'));
    }

    //Test ประวัติการเล่น
    public function member_play_callback(Request $request){
        $is_payout_db = 0;
        $type = "";
        if(isset($request->date_picker)){
            $d = explode('-',$request->date_picker);
            $dateS = Carbon::parse($d[0]);
            $dateE = Carbon::parse($d[1]);

            $provider = ProductList::where('product_id',$request->product_code)->first();
            $type = $provider->category;
            if($provider->category == "EGAMES"){

                $provider2 = strtolower($request->product_code);
                    switch ($request->product_code) {
                        case 'PGSOFT2':
                            $provider2 = "pgsoft";
                            break;
                        case 'PRAGMATIC_SLOT':
                            $provider2 = "pragmaticplay";
                            break;
                        case 'JOKER':
                            $provider2 = "slotxo";
                            break;
                        case 'SPADE':
                            $provider2 = "spadegaming";
                            break;
                        case 'MANNA':
                            $provider2 = "mannaplay";
                            break;
                        case 'ADVANT':
                            $provider2 = "advantplay";
                            break;
                        case 'MICRO':
                            $provider2 = "microgaming";
                            break;

                        default:
                            # code...
                            break;
                    }
                    $is_payout_db = 1;
                    $member_play = Payout::whereBetween('created_at', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"])
                            ->where('product',$provider2)
                            ->orderby('created_at','DESC')
                            ->get();

            }elseif($provider->category == "LIVECASINO"){
                switch ($request->product_code) {
                    case 'SEXY':
                        $member_play = SexyCallback::whereBetween('created_at', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"])->get();
                        break;
                    case 'WM':
                        $member_play = WmCallback::whereBetween('created_at', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"])->get();
                        break;
                    default:
                        $member_play = CommonCallback::select(DB::raw('username,productId,betAmount,gameCode,status,created_at'))->whereBetween('created_at', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"])->where('productId',$request->product_code)->groupby('username','productId','betAmount','gameCode','created_at','status')->orderby('id')->get();
                        // dd($member_play);
                        break;
                }

            }else{
                $member_play = CommonCallback::whereBetween('created_at', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"])->where('productId',$request->product_code)->get();
            }


        }else{
            $day= 'today';
            $member_play = CommonCallback::whereDate('created_at', Carbon::$day())->get();
        }

        $prduct_list = ProductList::all();
        return view('report.member_play_callback',compact('member_play','prduct_list','is_payout_db','type'));
    }


    public function edit_balance(){
        $edit_balance = MemberEditBalance::orderby('created_at','DESC')->get();
        return view('report.edit_balance',compact('edit_balance'));
    }
    public static function member_detail($id)
    {
        $name = 'member';
        $user = Members::where('id',$id)->first();
        if($user){
            $name = $user->username;
        }
        return $name;
    }

    public static function count_last_tranfer(){
        $transfer = Transfer::where('status',1)->get();
        $tran_count = 0;
        if($transfer){
            $tran_count = Count($transfer);
        }
        return $tran_count;
    }

    public function transfer_report($date_id){

        if($date_id == 0){
            $firstDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 23:59:59';
            $date_ = Carbon::now()->isoFormat('DD/MM/YYYY').' - '.Carbon::now()->isoFormat('DD/MM/YYYY');
        }else if($date_id == 1){
            $firstDate = Carbon::yesterday()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::yesterday()->isoFormat('YYYY-MM-DD').' 23:59:59';
            $date_ = Carbon::yesterday()->isoFormat('DD/MM/YYYY').' - '.Carbon::yesterday()->isoFormat('DD/MM/YYYY');
        }else if($date_id == 2){
            $lastDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $firstDate = Carbon::now()->subDays(7)->isoFormat('YYYY-MM-DD').' 23:59:59';
            $date_ = Carbon::now()->subDays(7)->isoFormat('DD/MM/YYYY').' - '.Carbon::now()->isoFormat('DD/MM/YYYY');
        }else if($date_id = 3){
            $lastDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $firstDate = Carbon::now()->subDays(30)->isoFormat('YYYY-MM-DD').' 23:59:59';
            $date_ = Carbon::now()->startOfMonth()->isoFormat('DD/MM/YYYY').' - '.Carbon::now()->endOfMonth()->isoFormat('DD/MM/YYYY');
        }
        error_log($firstDate.','.$lastDate);
        $transfers = Transfer::join('members',function($join){
            $join->on('members.id','=','transfer.member_id');
        })
        ->select(\DB::raw('transfer.member_id,members.username,transfer.type'),\DB::raw('SUM(transfer.amount) as amount'))
        ->where('transfer.status',2)
        ->whereBetween('transfer.created_at', [$firstDate,$lastDate ])
        ->groupby('transfer.member_id','members.username','transfer.type')
        ->get();

        return view('report.transfer_report',compact('transfers','date_id','date_'));
    }

    public function sum_trans($date_id){
        if($date_id == 0){
            $firstDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 23:59:59';
            $date_ = Carbon::now()->isoFormat('DD/MM/YYYY').' - '.Carbon::now()->isoFormat('DD/MM/YYYY');
        }else if($date_id == 1){
            $firstDate = Carbon::yesterday()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::yesterday()->isoFormat('YYYY-MM-DD').' 23:59:59';
            $date_ = Carbon::yesterday()->isoFormat('DD/MM/YYYY').' - '.Carbon::yesterday()->isoFormat('DD/MM/YYYY');
        }else if($date_id == 2){
            $lastDate = Carbon::now()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $firstDate = Carbon::now()->subDays(7)->isoFormat('YYYY-MM-DD').' 23:59:59';
            $date_ = Carbon::now()->subDays(7)->isoFormat('DD/MM/YYYY').' - '.Carbon::now()->isoFormat('DD/MM/YYYY');
        }else if($date_id = 3){
            $firstDate = Carbon::now()->startOfMonth()->isoFormat('YYYY-MM-DD').' 00:00:00';
            $lastDate = Carbon::now()->endOfMonth()->isoFormat('YYYY-MM-DD').' 23:59:59';
            $date_ = Carbon::now()->startOfMonth()->isoFormat('DD/MM/YYYY').' - '.Carbon::now()->endOfMonth()->isoFormat('DD/MM/YYYY');
        }

        $deposit = DB::table('transfer')
        ->select(DB::raw('SUM(amount) as amount'))
        ->where('status',2)->where('type','deposit')
        ->whereBetween('created_at', [$firstDate,$lastDate ])
        ->get();

        $withdraw = DB::table('transfer')
        ->select(DB::raw('SUM(amount) as amount'))
        ->where('status',2)->where('type','withdraw')
        ->whereBetween('created_at', [$firstDate,$lastDate ])
        ->get();

        return view('report.sum_trans', compact('deposit','withdraw','date_id','date_'));
    }
    public function auth_basic()
    {
        $auth = base64_encode(env('APP_ASK_AGENT') . ":" . env('APP_ASK_API_SECRET'));
        return $auth;
    }
    public function QueryBetRecordsV2()
    {

        $total_bet = 0;
        $winlose = 0;

        $username = "0666666611";

        $date = Carbon::now()->format('Y-m-d');
        $date_end = Carbon::now()->format('Y-m-d');
        $nextId=1;
        $startTime = $date."T00:00:00Z";
        $endTime = $date_end."T23:59:00Z";

        $firstDate = $date;
        $lastDate = $date_end;

        $Gplay = Gplay::select('provider')->where('username',$username)->whereBetween('created_at', [$firstDate." 00:00:00", $lastDate." 23:59:59"])->groupby('provider')->get();

        foreach ($Gplay as $key => $value) {
            $productId = $value->provider;
            if($value->provider == 'PGSOFT'){
                $productId = "PGSOFT2";
            }
            // $productId = "PRAGMATIC_SLOT";

            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', env('APP_ASK_API_URL'). "/betTransactionsV2", [
                "query" => [
                    "productId" => $productId,
                    "date"  => $date,
                    "startTime" => $startTime,
                    "endTime" => $endTime,
                    "nextId" => $nextId,
                ],
                'headers' => [
                    'Authorization' => 'Basic '. $this->auth_basic(),
                    'Content-Type' => 'application/json'
                ]
            ]);
            $data = json_decode($response->getBody());
            // dd($data);
            foreach ($data->data->txns as $key => $item) {

                if($username == $item->username){

                    // if($value->provider == 'PGSOFT'){

                    // }else{
                    //     dd($item);
                    // }
                    $total_bet += $item->stake;
                    if(strtolower($item->payoutStatus) == "lose"){
                        $winlose = $winlose - $item->stake + $item->payout;
                    }elseif(strtolower($item->payoutStatus) == "win"){
                        $winlose = $winlose + $item->payout - $item->stake;
                    }
                }
            }
        }


        echo 'total bet : ' . $total_bet;
        echo 'winlose : ' . $winlose;
        dd(1);
        return $data;
    }

    function wrongdeposit(){
        $transfer = Wrongdeposit::get();
        $members = Members::where('enable',1)->where('active',1)->get();
        $banks = Bank::where('enable',1)->where('active',1)->get();
        return view('report.report_wrong',compact('transfer','members','banks'));
    }
}
