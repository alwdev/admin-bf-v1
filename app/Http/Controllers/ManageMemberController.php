<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use App\Models\Members;
use App\Models\Transfer;
use App\Models\User;
use App\Models\MemberEditBalance;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\Promotion;
use App\Models\Payout;
use Illuminate\Support\Facades\Log;
use App\Models\Logs;
use NotificationChannels\Telegram\TelegramMessage;

class ManageMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $memberlist = Members::where('active',1)->orderBy('id', 'DESC')->get();
        return view('manage-member.index',compact('memberlist'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function historyTransfer($id)
    {
        //
        $transfer = Transfer::join('members',function($join){
            $join->on('members.id','=','transfer.member_id');
        })
        ->select(\DB::raw('transfer.*,members.bank_number,members.account_name,members.bank_name,members.username'))
        ->where('transfer.member_id', $id)->get();
        return view('manage-member.historyTransfer',compact('transfer'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function approveDeposit(Request $request)
    {
        //
        $member = Members::find($request->member_id);
        $transfer = Transfer::find($request->transfer_id);
        Log::info($request->type.'Admin approve '.$member->username.' Balance =  '.$member->wallet_balance.' transfer amount ='.$transfer->amount);


        if($transfer->status == 2 || $transfer->status == 3){
            return redirect()->back();
        }

        $transfer->old_balance = $member->wallet_balance;
        if($request->status == 'approve'){
            $old_balance = $member->wallet_balance;

            $transfer->status = 2;
            $transfer->status_code ="อนุมัติ";
            $transfer->old_balance = $old_balance;

            if($request->type=="deposit"){
                if($transfer->promotion_id != 0){
                    $pro = Promotion::find($transfer->promotion_id);
                    if($transfer->promotion_id == 1){
                        $b =  (float) $member->wallet_balance + $transfer->amount + 100;
                    }else{
                        $b =  (float) $member->wallet_balance + (float) $transfer->amount + ((float) $transfer->amount * $pro->bonus / 100);
                    }
                    $member->wallet_balance = $b;
                }else{
                    $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                }
              $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username,floor($transfer->amount));
              Log::info('Deposit Betflix '.$bf_deposit.' '.$transfer->amount.' User =  '.$member->username);

              TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
              ->line(env('APP_NAME'))
              ->line('Admin ทำรายการ อนุมัติเครดิตเข้า '.$member->username)
              ->line('จำนวน :'.$transfer->amount)
              ->send();
            }else if($request->type=="withdraw"){
                if($transfer->promotion_id != 0){
                    $pro = Promotion::find($transfer->promotion_id);
                    // $member->wallet_balance = (float) $member->wallet_balance -  (float) $transfer->amount;
                }else{
                    // $member->wallet_balance = (float) $member->wallet_balance -  (float) $transfer->amount;
                }

                $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Withdraw($member->username,floor($transfer->amount));
                Log::info('Betflix Withdraw '.$bf_deposit.' '.floor($transfer->amount).' User =  '.$member->username);

                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line(env('APP_NAME'))
                ->line('Admin ทำรายการ อนุมัติถอนเงิน '.$member->username)
                ->line('จำนวน :'.$transfer->amount)
                ->line('****คำเตือน Admin ต้องทำรายการโอนเงินเองที่แอปธนาคาร  ****')
                ->send();

            }else{
                $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
            }
            $member->save();
            $transfer->new_balance = $member->wallet_balance;
            $transfer->save();


        }else{

            $transfer->status = 3;
            $transfer->status_code ="ปฏิเสธ";
            $transfer->save();

            if($request->type=="withdraw"){
                $new_balance = (float) $member->wallet_balance + $transfer->amount;
                $member->update(['wallet_balance' => strval($new_balance)]);
            }

        }
        return redirect()->back()->with('status','200');
    }

    function changePassword(Request $request){
        $member = Members::find($request->id);
        $random_pass = $this->strRandom(8);
        $member->password = Hash::make($random_pass);
        $member->save();
        return [$member,$random_pass];
    }

    public static function strRandom($length)
    {
        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($str, 5)), 0, $length);
    }

    public function memberlock(Request $request){
        $status = 0;
        if($request->status == 1){
            $status =0;
        }else if($request->status ==0){
            $status =1;
        }
        $member = Members::find($request->member_id);
        $member->enable = $status;
        $member->update_by = $request->user_id;
        $member->save();
        return redirect()->route('managemember.index');
    }

    public function memberdelete(Request $request){
        $member = Members::find($request->member_id);
        $member->active = 0;
        $member->update_by = $request->user_id;
        $member->save();
        return redirect()->route('managemember.index');
    }

    public function memberEditBalance(Request $request){

        error_log($request->member_id);
        error_log($request->user_id);
        error_log($request->balance);

        $member = Members::find($request->member_id);
        if($member){
            $currentBalance = $member->wallet_balance;
            $member->wallet_balance = $request->balance;
            $member->update_by = $request->user_id;
            $member->save();

            MemberEditBalance::create([
                'user_id' => $request->user_id,
                'member_id' => $request->member_id,
                'balance' => $currentBalance,
                'edit_balance' => $request->balance,
            ]);
        }
        // return $member->wallet_balance;
        return redirect()->route('managemember.index');
    }

    public static function staff_detail($id)
    {
        $name = 'System';
        $user = User::where('id',$id)->first();
        if($user){
            $name = $user->name;
        }
        return $name;
    }

    public function cash_back(){
        Log::info("Run cash_back");

        $members = Members::get();

        foreach ($members as $member) {
            $last_play = History::where('username',$member->username)->whereDate('created_at', Carbon::yesterday())->orderby('created_at','desc')->get();
            $last_deposit = Transfer::where('member_id',$member->id)->where('status',2)->where('type','deposit')->whereDate('created_at', Carbon::yesterday())->orderby('created_at','desc')->get();
            if($last_play && $last_deposit){
                $total_lose = 0;
                $cash_back=0;
                $amount=0;
                $winlose=0;
                $deposit=0;
                foreach($last_play as $t){
                    $amount += $t->amount;
                    $winlose += $t->winlose;
                }
                foreach($last_deposit as $a){
                    $deposit += $a->amount;
                }
                $total_lose =  $winlose;

                if($amount > $deposit) {

                    // if(abs($total_lose) > 0){
                    //     $cash_back = (float) (abs($total_lose) * 0.05);
                    // }
                    $cash_back = $deposit * 0.05;

                    // Log::info($member->username.' play amount: ' . number_format($amount,2).', last_deposit: '.number_format($deposit,2).', total_lose:' . number_format($total_lose,2));
                    // Log::info($member->username.'cash back: ' . number_format($cash_back,2));

                    $logs = new Logs;
                    $logs->username = $member->username;
                    $logs->log = 'last_deposit: '.number_format($deposit,2).', total_lose: ' . number_format($total_lose,2).' cash back: ' . number_format($cash_back,2);
                    $logs->save();
                    if($cash_back > 0   )
                        Transfer::create([
                            'member_id' => $member->id,
                            'amount' => $cash_back,
                            'status' => 2,
                            'status_code' => 'อนุมัติ',
                            'type' => 'cashback',
                            'promotion' => 'cashback',
                            'old_balance' => $member->wallet_balance,
                            'new_balance' => $member->wallet_balance + $cash_back,
                            'transfer_date' => strtotime(now()),
                        ]);
                        $member->wallet_balance = (float) ($member->wallet_balance + $cash_back);
                        $member->save();
                    }
                }

            }

        }

    function affiliate(){

        Log::info("Run affiliate");

        $date =  Carbon::today()->format('Y-m-d');
        $date_end =  Carbon::today()->format('Y-m-d');

        $startTime = $date."T00:00:00Z";
        $endTime = $date_end."T23:59:00Z";
        $firstDate = $date;
        $lastDate = $date_end;


        $members = Members::get();
        foreach ($members as $member) {
            if(json_decode($member->ref_user)){

                $member_play = History::select('username')->whereIn('username',json_decode($member->ref_user))->whereBetween('created_at', [$firstDate." 00:00:00", $lastDate." 23:59:59"])->groupby('username')->get();
                foreach ($member_play as $key => $his) {

                    $total_bet = 0;
                    $winlose = 0;

                    $nextId =1;

                    $history = History::select('provider')->where('username',$his->username)->whereBetween('created_at', [$firstDate." 00:00:00", $lastDate." 23:59:59"])->groupby('provider')->get();

                    foreach ($history as $key => $value) {
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

                        if(isset($data->data)){

                            foreach ($data->data->txns as $key => $item) {
                                if($his->username== $item->username){
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

                    if($winlose < 1){
                        $commission = abs($winlose) * 0.05;
                        Transfer::create([
                            'member_id' => $member->id,
                            'amount' => $commission,
                            'status' => 2,
                            'status_code' => 'อนุมัติ',
                            'type' => 'commission',
                            'promotion' => 'commission',
                            'old_balance' => $member->wallet_balance,
                            'new_balance' => $member->wallet_balance + $commission,
                            'transfer_date' => strtotime(now()),
                        ]);
                        $member->wallet_balance = (float) ($member->wallet_balance + $commission);
                        $member->save();
                    }

                }

            }

        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
