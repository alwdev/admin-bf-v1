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
use App\Models\Bank;
use Illuminate\Support\Facades\Log;
use App\Models\Logs;
use App\Models\PromotionUsed;
use App\Models\Affiliate;
use App\Models\Setting;
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
            $bonus =0;

            if($request->type=="deposit"){
                $amount_betflix = 0;

                $bank = Bank::where('account_no',$transfer->deposit_to_bank_no)->first();
                if($bank){
                    $bank->balance = (float) $bank->balance + (float) $transfer->amount;
                    $bank->save();
                }

                if($transfer->promotion_id != 0){
                    if($transfer->turnover_on == 1){
                        $pro = Promotion::find($transfer->promotion_id);
                        $user_transfer = Transfer::where('member_id',$member->id)->where('status',2)->where('type','deposit')->get();  /// เช็คฝากครั้งแรก
                        $user_transfer_count = $user_transfer->count();

                        if($user_transfer_count == 0){

                            $bonus = $pro->bonus;
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            $amount_betflix = $transfer->amount + $bonus;
                            $transfer->promotion = $pro->name;
                            Log::info($pro->name);

                            // if($transfer->amount >= 20 && $transfer->amount < 300){  /// สมาชิกใหม่ ฝาก 20 รับ 100 บาท
                            //     $bonus = 80;
                            //     $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            //     $amount_betflix = $transfer->amount + $bonus;
                            //     $transfer->promotion ='สมาชิกใหม่ ฝาก 20 รับ 100 บาท';
                            //     Log::info('สมาชิกใหม่ ฝาก 20 รับ 100 บาท');

                            // }else if($transfer->amount >= 300){  /// สมาชิกใหม่ ฝาก 300 รับ 500 บาท
                            //     $bonus = 200;
                            //     $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            //     $amount_betflix = $transfer->amount + $bonus;
                            //     $transfer->promotion ='สมาชิกใหม่ ฝาก 300 รับ 500 บาท';
                            //     Log::info('สมาชิกใหม่ ฝาก 300 รับ 500 บาท');

                            // }


                        }else{
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount;
                            $amount_betflix = $transfer->amount;

                        }
                    }else{
                        $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                        $amount_betflix = $transfer->amount;
                    }
                }else{
                    $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                    $amount_betflix = $transfer->amount;


                }


                $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username,floor($amount_betflix));
                Log::info('Deposit Betflix '.$bf_deposit.' '.$amount_betflix.' User =  '.$member->username);
                error_log('Deposit Betflix '.$bf_deposit.' '.$amount_betflix.' User =  '.$member->username);

                if($bf_deposit == "success"){
                    $member->save();
                    $transfer->new_balance = $member->wallet_balance;
                    $transfer->status = 2;
                    $transfer->status_code ="อนุมัติ";
                    $transfer->old_balance = $old_balance;
                    $transfer->save();

                    if($transfer->promotion_id != 0){
                        PromotionUsed::create([
                            'member_id' => $member->id,
                            'promotion_id' => $transfer->promotion_id,
                            'promotion_name' => $pro->name,
                            'amount' => $bonus
                        ]);
                    }

                }else{
                    return redirect()->back()->with('error',$bf_deposit);
                }

              TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
              ->line(env('APP_NAME'))
              ->line('Admin ทำรายการ อนุมัติเครดิตเข้า '.$member->username)
              ->line('จำนวน :'.floor($transfer->amount))
              ->line('Bonus :'.$bonus)
              ->send();


            }else if($request->type=="withdraw"){


                $bank = Bank::where('account_no',$transfer->deposit_to_bank_no)->first();
                if($bank){
                    $bank->balance = (float) $bank->balance - (float) $transfer->amount;
                    $bank->save();
                }

                $member->save();
                $transfer->new_balance = $member->wallet_balance;
                $transfer->status = 2;
                $transfer->status_code ="อนุมัติ";
                $transfer->old_balance = $old_balance;
                $transfer->save();

                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line(env('APP_NAME'))
                ->line('Admin ทำรายการ อนุมัติถอนเงิน '.$member->username)
                ->line('จำนวน :'.floor($transfer->amount))
                ->line('คำเตือน Admin ต้องทำรายการโอนเงินเองที่แอปธนาคาร')
                ->send();

            }





        }else if($request->status == 'pending'){
            $transfer->status = 1;
            $transfer->status_code ="รอดำเนินการ";
            $transfer->save();

        }else if($request->status == 'reject'){

            $transfer->status = 3;
            $transfer->status_code ="ปฏิเสธ";
            $transfer->turnover_on = 0;
            $transfer->save();

            if($request->type=="withdraw"){
                $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username,floor($transfer->amount));
                Log::info('rollBack Deposit Betflix '.$bf_deposit.' '.$transfer->amount.' User =  '.$member->username);
                if($bf_deposit == "success"){
                    $new_balance = (float) $member->wallet_balance + $transfer->amount;
                    $member->update(['wallet_balance' => strval($new_balance)]);

                }
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

        error_log("request->balance =".$request->balance);

        $update_balance = 0;
        $member = Members::find($request->member_id);
        if($member){
        $old_balance = app(\App\Http\Controllers\BetflixController::class)->Balance($member->username);
        if ($old_balance < $request->balance) {
            $update_balance = $request->balance - $old_balance;
            Log::info(" + Deposit update_balance =".$update_balance);
            $bf = app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username,$update_balance);
            Log::info("Betflix Deposit ".$bf.' '.$update_balance.' User =  '.$member->username);
        }else if ($old_balance > $request->balance) {
            $update_balance = $old_balance - $request->balance;

            Log::info(" - Withdraw update_balance =".$update_balance);
            $bf = app(\App\Http\Controllers\BetflixController::class)->Master_Withdraw($member->username,$update_balance);
            Log::info("Betflix Withdraw ".$bf.' '.$update_balance.' User =  '.$member->username);
        }

        $new_balance = app(\App\Http\Controllers\BetflixController::class)->Balance($member->username);


            $currentBalance = $member->wallet_balance;
            $member->wallet_balance = $new_balance;
            $member->update_by = $request->user_id;
            $member->save();

            MemberEditBalance::create([
                'user_id' => $request->user_id,
                'member_id' => $request->member_id,
                'balance' => $currentBalance,
                'edit_balance' => $new_balance,
            ]);
        }

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
        set_time_limit(300000000);
        Log::info("Run cash_back");
        TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
        ->line(env('APP_NAME'))
        ->line('BOT เริ่มทำการ Cashback ')
        ->send();

        $members = Members::get();

        foreach ($members as $member) {
            sleep(1);
            $last_deposit = Transfer::where('member_id',$member->id)
            ->where('status',2)->where('promotion_id','>',0)
            ->where('type','deposit')
            ->whereDate('created_at', Carbon::now()->subDays(7))->get();

            if($last_deposit){
                Log::info("Cashback !! member  = ".$member->username." มียอดฝากก่อนหน้ารับโปร");
                continue;
            }

            $last_withdraw = Transfer::where('member_id',$member->id)
            ->where('status',2)
            ->where('type','withdraw')
            ->whereDate('created_at', Carbon::now()->subDays(7))->get();
            if($last_withdraw){
                Log::info("Cashback !! member  = ".$member->username." มียอดถอนก่อนหน้า");
                continue;
            }

            if($member->wallet_balance >= 1){
                Log::info("Cashback !! member  = ".$member->username." มียอดคงเหลือมากกว่า 1");
                continue;
            }

            $total_lose = 0;
            $cash_back=0;
            try{
                $winlose= app(\App\Http\Controllers\BetflixController::class)->Single_Member_Report_all_Provider($member->username,-7,-1)->winloss;

                if($winlose){
                    $total_lose =  $winlose;
                }else{
                    $total_lose = 0;
                }
            } catch (\Exception $e) {
                Log::error('Error Betflix API : '.$e->getMessage());
                $winlose = 0;
            }


                if(abs($total_lose) > 0){
                    $setting = Setting::get();
                    if($setting){
                        $cash_back = (float) (abs($total_lose) * ($setting->cashback_percent/100));
                    } else {
                        $cash_back = 0;
                    }
                }


                if($cash_back > 20000){
                    $cash_back = 20000;
                }
                $logs = new Logs;
                $logs->username = $member->username;
                $logs->log = 'total_lose: ' . number_format($total_lose,2).' cash back: ' . number_format($cash_back,2);
                $logs->save();

                if($cash_back > 0 ){
                    Log::info('Cashback ++ Username : '.$member->username.' total_lose: ' . number_format($total_lose,2).' cash back: ' . number_format($cash_back,2));
                    Transfer::create([
                        'member_id' => $member->id,
                        'amount' => $cash_back,
                        'status' => 1,
                        'status_code' => 'รออนุมัติ',
                        'type' => 'cashback',
                        'promotion' => 'cashback',
                        'old_balance' => $member->wallet_balance,
                        'new_balance' => $member->wallet_balance + $cash_back,
                        'transfer_date' => strtotime(now()),
                    ]);
                    // $member->wallet_balance = (float) ($member->wallet_balance + $cash_back);
                    // $member->save();

                    // $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username,floor($cash_back));
                    // Log::info('Betflix CashBack '.$bf_deposit.' '.floor($cash_back).' User =  '.$member->username);
                }

        }

        TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
        ->line(env('APP_NAME'))
        ->line('BOT สิ้นสุดการ Cashback ')
        ->send();
        Log::info("End Cashback");
        return 'success';

    }
    function affiliate(){
        set_time_limit(3000000000);
        Log::info("Run affiliate");
        TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
        ->line(env('APP_NAME'))
        ->line('BOT เริ่มทำการ affiliate')
        ->send();

        $members = Members::where('ref_user','!=',null)->get();
        Log::info("Total Members affiliate : ".count($members));
        foreach ($members as $main_member) {
            sleep(2);
            Log::info("Member main : " . $main_member->username.'uder member count = '.count(json_decode($main_member->ref_user)));
            if(json_decode($main_member->ref_user)){
                set_time_limit(3000000000);
                foreach(json_decode($main_member->ref_user) as $_member){
                    sleep(3);

                    $under_member = Members::where('id',$_member)->first();
                    Log::info("Under of ".$main_member->username." member : " .$under_member->username);

                    try{
                        $bf_total_bet = app(\App\Http\Controllers\BetflixController::class)->Single_Member_Report_all_Provider($under_member->username,-1,-1);
                        if($bf_total_bet){
                            $total_bet = $bf_total_bet->valid_amount;
                            $winlose = $bf_total_bet->winloss;
                            Log::info("bf_total_bet : " . $bf_total_bet->valid_amount);
                        }else{
                            Log::info("bf_total_bet : " . $bf_total_bet->msg);
                        }
                    } catch (\Exception $e) {
                        Log::info('Betflix API Error : '.$e->getMessage());
                        $total_bet =0;
                        $winlose =0;
                        continue;
                    }

                    try{
                        $pg_total_bet = app(\App\Http\Controllers\PgHardController::class)->pg_get_spin_summaryby_user($under_member->username,-1,-1);

                        if(count($pg_total_bet['data']) > 0){
                            Log::info("pg_total_bet : " . $pg_total_bet['data'][0]['totalAmount']);
                            $total_bet = $total_bet + $pg_total_bet['data'][0]['totalAmount'];
                        }else{
                            Log::info('PgHard API No have User Data '.$under_member->username);
                        }
                    } catch (\Exception $e) {
                        Log::info('PgHard API Error : '.$e->getMessage());
                        $pg_total_bet =0;
                    }

                    Log::info("total_bet : ".$total_bet);

                    $affiliate = Affiliate::first();
                    if($affiliate->is_enable_af_winlose == 1){
                        // Log::info("is_enable_af_winlose = ".$affiliate->is_enable_af_winlose);
                        if($total_bet > 1){

                            if($affiliate->af_receive_percent_winlose_1 == "ยอดเดิมพัน"){
                                $commission = $total_bet * ($affiliate->af_receive_percent_winlose_2 / 100);
                                Log::info("commission ยอดเดิมพัน total_bet : ".$total_bet." commission : ".$commission);
                            }else if($affiliate->af_receive_percent_winlose_1 == "ยอดเสีย" && $winlose < 0){
                                $commission = abs($winlose) * ($affiliate->af_receive_percent_winlose_2 / 100);
                                Log::info("commission ยอด winlose : ".$winlose." commission : ".$commission);
                            }

                            Transfer::create([
                                'member_id' => $main_member->id,
                                'amount' => $commission,
                                'status' => 1,
                                'status_code' => 'รออนุมัติ',
                                'type' => 'commission',
                                'promotion' => 'commission',
                                'old_balance' => $main_member->wallet_balance,
                                'new_balance' => $main_member->wallet_balance + $commission,
                                'transfer_date' => strtotime(now()),
                            ]);

                            // $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($main_member->username,floor($commission));
                            // Log::info('Deposit commission to Betflix  '.$bf_deposit.' '.floor($commission).' User =  '.$main_member->username);
                            $main_member->wallet_balance = (float) ($main_member->wallet_balance + $commission);
                            $main_member->save();
                        }
                    }
                }

            }

        }
        Log::info('success Run affiliate');
        TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
            ->line(env('APP_NAME'))
            ->line('BOT สิ้นสุดการ Run affiliate ')
            ->send();
        return 'success';
    }

    function affiliate_fixdate($date_start,$date_end){
        $startDate=date('Y-m-d',strtotime($date_start.' day')).'T00:00:00Z';
        $endDate=date('Y-m-d',strtotime($date_end.' day')).'T23:59:59Z';

        set_time_limit(3000000000);
        Log::info("Run affiliate ย้อนหลัง จากวันที่ : ".$startDate." ถึง ".$endDate);
        TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
        ->line(env('APP_NAME'))
        ->line('BOT เริ่มทำการ affiliate fixdate : '.$startDate.' - '.$endDate)
        ->send();

        $members = Members::where('ref_user','!=',null)->get();
        Log::info("Total Members affiliate : ".count($members));
        foreach ($members as $main_member) {
            sleep(1);
            Log::info("Member main : " . $main_member->username.'uder member count = '.count(json_decode($main_member->ref_user)));
            if(json_decode($main_member->ref_user)){
                set_time_limit(3000000000);
                foreach(json_decode($main_member->ref_user) as $_member){
                    sleep(2);

                    $under_member = Members::where('id',$_member)->first();
                    Log::info("Under of ".$main_member->username." member : " .$under_member->username);

                    try{
                        $bf_total_bet = app(\App\Http\Controllers\BetflixController::class)->Single_Member_Report_all_Provider($under_member->username,$date_start,$date_end);
                        if($bf_total_bet){
                            $total_bet = $bf_total_bet->valid_amount;
                            $winlose = $bf_total_bet->winloss;
                            Log::info("bf_total_bet : " . $bf_total_bet->valid_amount);
                        }else{
                            Log::info("bf_total_bet : " . $bf_total_bet->msg);
                        }
                    } catch (\Exception $e) {
                        Log::info('Betflix API Error : '.$e->getMessage());
                        $total_bet =0;
                        $winlose =0;
                        continue;
                    }

                    try{
                        $pg_total_bet = app(\App\Http\Controllers\PgHardController::class)->pg_get_spin_summaryby_user($under_member->username,$date_start,$date_end);

                        if(count($pg_total_bet['data']) > 0){
                            Log::info("pg_total_bet : " . $pg_total_bet['data'][0]['totalAmount']);
                            $total_bet = $total_bet + $pg_total_bet['data'][0]['totalAmount'];
                        }else{
                            Log::info('PgHard API No have User Data '.$under_member->username);
                        }
                    } catch (\Exception $e) {
                        Log::info('PgHard API Error : '.$e->getMessage());
                        $pg_total_bet =0;
                    }

                    Log::info("total_bet : ".$total_bet);
                    if($total_bet = 0){
                        continue;
                    }

                    $affiliate = Affiliate::first();
                    if($affiliate->is_enable_af_winlose == 1){
                        // Log::info("is_enable_af_winlose = ".$affiliate->is_enable_af_winlose);
                        if($total_bet > 1){

                            if($affiliate->af_receive_percent_winlose_1 == "ยอดเดิมพัน"){
                                $commission = $total_bet * ($affiliate->af_receive_percent_winlose_2 / 100);
                                Log::info("commission ยอดเดิมพัน total_bet : ".$total_bet." commission : ".$commission);
                            }else if($affiliate->af_receive_percent_winlose_1 == "ยอดเสีย" && $winlose < 0){
                                $commission = abs($winlose) * ($affiliate->af_receive_percent_winlose_2 / 100);
                                Log::info("commission ยอด winlose : ".$winlose." commission : ".$commission);
                            }

                            Transfer::create([
                                'member_id' => $main_member->id,
                                'amount' => $commission,
                                'status' => 1,
                                'status_code' => 'รออนุมัติ',
                                'type' => 'commission',
                                'promotion' => 'commission',
                                'old_balance' => $main_member->wallet_balance,
                                'new_balance' => $main_member->wallet_balance + $commission,
                                'transfer_date' => strtotime(now()),
                            ]);

                            // $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($main_member->username,floor($commission));
                            // Log::info('Deposit commission to Betflix  '.$bf_deposit.' '.floor($commission).' User =  '.$main_member->username);
                            $main_member->wallet_balance = (float) ($main_member->wallet_balance + $commission);
                            $main_member->save();
                        }
                    }
                }

            }

        }
        Log::info('success Run affiliate Fixdete');
        TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
            ->line(env('APP_NAME'))
            ->line('BOT สิ้นสุดการ Run affiliate ย้อนหลัง')
            ->send();
        return 'success';
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
