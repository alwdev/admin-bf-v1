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
        Log::info($request->type.' Admin approve '.$member->username.' Balance =  '.$member->wallet_balance.' transfer amount ='.$transfer->amount);
        error_log($request->type.' Admin approve '.$member->username.' Balance =  '.$member->wallet_balance.' transfer amount ='.$transfer->amount);

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
                $amount_betflix = 0;
                if($transfer->promotion_id != 0){
                    $pro = Promotion::find($transfer->promotion_id);
                    $user_transfer = Transfer::where('member_id',$member->id)->where('status',2)->where('type','deposit')->get();  /// เช็คฝากครั้งแรก
                    $user_transfer_count = $user_transfer->count();

                    if($user_transfer_count == 0){

                        if($transfer->amount == 20){  /// สมาชิกใหม่ ฝาก 20 รับ 100 บาท
                            $bonus = 80;
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            $amount_betflix = $transfer->amount + $bonus;
                            $transfer->promotion ='สมาชิกใหม่ ฝาก 20 รับ 100 บาท';
                        }else if($transfer->amount == 300){  /// สมาชิกใหม่ ฝาก 300 รับ 500 บาท
                            $bonus = 200;
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            $amount_betflix = $transfer->amount + $bonus;
                            $transfer->promotion ='สมาชิกใหม่ ฝาก 300 รับ 500 บาท';
                        }


                    }else{
                        $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount;
                        $amount_betflix = $transfer->amount;

                    }
                }else{
                    $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                    $amount_betflix = $transfer->amount;


                }
                $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username,floor($amount_betflix));
                Log::info('Deposit Betflix '.$bf_deposit.' '.$amount_betflix.' User =  '.$member->username);
                error_log('Deposit Betflix '.$bf_deposit.' '.$amount_betflix.' User =  '.$member->username);

            //   TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
            //   ->line(env('APP_NAME'))
            //   ->line('Admin ทำรายการ อนุมัติเครดิตเข้า '.$member->username)
            //   ->line('จำนวน :'.floor($transfer->amount))
            //   ->send();
            }else if($request->type=="withdraw"){
                // if($transfer->promotion_id != 0){
                //     $pro = Promotion::find($transfer->promotion_id);
                //     $member->wallet_balance = (float) $member->wallet_balance -  (float) $transfer->amount;
                // }else{
                //     $member->wallet_balance = (float) $member->wallet_balance -  (float) $transfer->amount;
                // }

                $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Withdraw($member->username,floor($transfer->amount));
                Log::info('Betflix Withdraw '.$bf_deposit.' '.floor($transfer->amount).' User =  '.$member->username);

                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line(env('APP_NAME'))
                ->line('Admin ทำรายการ อนุมัติถอนเงิน '.$member->username)
                ->line('จำนวน :'.floor($transfer->amount))
                ->line('คำเตือน Admin ต้องทำรายการโอนเงินเองที่แอปธนาคาร')
                ->send();

            }
            if($bf_deposit == "success"){
                $member->save();
                $transfer->new_balance = $member->wallet_balance;
                $transfer->save();
            }else{
                return redirect()->back()->with('error','error');
            }



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

    public function memberlock(Request $request)
    {
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

    public function memberdelete(Request $request)
    {
        $member = Members::find($request->member_id);
        $member->active = 0;
        $member->update_by = $request->user_id;
        $member->save();
        return redirect()->route('managemember.index');
    }

    public function memberEditBalance(Request $request)
    {

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
            // $last_play = History::where('username',$member->username)->whereDate('created_at', Carbon::yesterday())->orderby('created_at','desc')->get();
            $last_deposit = Transfer::where('member_id',$member->id)->where('status',2)->where('promotion_id',0)->where('type','deposit')->whereDate('created_at', Carbon::now()->subDays(7))->get();
            if($last_deposit){
                $total_lose = 0;
                $cash_back=0;
                $amount=0;
                $winlose= app(\App\Http\Controllers\BetflixController::class)->Single_Member_Report_all_Provider($member->username,-7,-1)->winloss;
                $deposit=0;

                foreach($last_deposit as $a){
                    $deposit += $a->amount;
                }
                if($winlose){
                    $total_lose =  $winlose;
                }else{
                    $total_lose = 0;
                }

                if($amount > $deposit) {

                    if(abs($total_lose) > 0){
                        $cash_back = (float) (abs($total_lose) * 0.05);
                    }
                    // $cash_back = $deposit * 0.05;

                    if($cash_back > 20000){
                        $cash_back = 20000;
                    }
                    $logs = new Logs;
                    $logs->username = $member->username;
                    $logs->log = 'last_deposit: '.number_format($deposit,2).', total_lose: ' . number_format($total_lose,2).' cash back: ' . number_format($cash_back,2);
                    $logs->save();
                    if($cash_back > 0   ){
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

                        $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username,floor($cash_back));
                        Log::info('Betflix CashBack '.$bf_deposit.' '.floor($cash_back).' User =  '.$member->username);
                    }
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
        foreach ($members as $main_member) {
            error_log("Member main : " . $main_member->username);
            if(json_decode($main_member->ref_user)){
                error_log(json_encode($main_member->ref_user));
                foreach(json_decode($main_member->ref_user) as $_member){

                    $under_member = Members::where('id',$_member)->first();
                    error_log($under_member->username);

                    try{
                        $total_bet = app(\App\Http\Controllers\BetflixController::class)->Single_Member_Report_all_Provider($under_member->username,-7,-1)->valid_amount;
                        $winlose = app(\App\Http\Controllers\BetflixController::class)->Single_Member_Report_all_Provider($under_member->username,-7,-1)->winloss;
                    } catch (\Exception $e) {
                        error_log('Betflix API Error : '.$e->getMessage());
                        $total_bet =0;
                        $winlose =0;
                        continue;
                    }
                    error_log("total bet : ".$total_bet." win loss : ".$winlose);
                    if($total_bet > 1){
                        $commission = abs($total_bet) * 0.01;
                        Transfer::create([
                            'member_id' => $main_member->id,
                            'amount' => $commission,
                            'status' => 2,
                            'status_code' => 'อนุมัติ',
                            'type' => 'commission',
                            'promotion' => 'commission',
                            'old_balance' => $main_member->wallet_balance,
                            'new_balance' => $main_member->wallet_balance + $commission,
                            'transfer_date' => strtotime(now()),
                        ]);
                        $main_member->wallet_balance = (float) ($main_member->wallet_balance + $commission);
                        $main_member->save();
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
