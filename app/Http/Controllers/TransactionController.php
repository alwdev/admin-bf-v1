<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Transfer;
use App\Models\Bank;
use App\Models\Members;
use App\Models\History;
use App\Models\Payout;
use App\Models\Promotion;
use App\Models\Wrongdeposit;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;
use Auth;
use \Crypt;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use NotificationChannels\Telegram\TelegramMessage;
use App\Models\PromotionUsed;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transfer = Transfer::join('members',function($join){
            $join->on('members.id','=','transfer.member_id');
        })
        ->select(\DB::raw('transfer.*,members.bank_number,members.account_name,members.bank_name,members.username'))
        ->where('transfer.type','!=','cashback')
        ->orderby('transfer.created_at','desc')
        ->get();

        return view('transaction.list',compact('transfer'));
    }

    public function checkTurnOver($mid){
        $member = Members::where('id',$mid)->first();
        $turn_over = app(\App\Http\Controllers\BetflixController::class)->lastDay_TurnOver($member->username);
        $check_transfers = Transfer::where('member_id',$mid)->where('type','deposit')->latest('created_at')->first();

        if($check_transfers){
            if($check_transfers->turnover_on == 1){
                if($check_transfers->promotion_id != 0){
                    $check_balance = Members::where('id',$mid)->first();

                    // $turn_over = app(\App\Http\Controllers\BetflixController::class)->lastDay_TurnOver($member->username);
                    try {
                        $turn_over = app(\App\Http\Controllers\BetflixController::class)->lastDay_TurnOver($member->username);
                    } catch (\Throwable $th) {
                        $turnover =0;
                    }
                    $current_balance = $check_balance->wallet_balance;
                    $last_transfers = $check_transfers->amount;

                    $promotion = Promotion::find($check_transfers->promotion_id);
                    if(!is_null($promotion)){
                        if($promotion->id == 1){
                            if($turn_over >= 100){
                                return "ผ่าน";
                            }else{
                                return $turn_over;
                            }
                        }else{

                            if($turn_over > ($last_transfers * (int) $promotion->turnover)){
                                return "ผ่าน";
                            }else{
                                return $turn_over;
                            }
                        }
                    }
                }
            }
        }
    }

    public function Checktransfer(){
        $check_transfers = Transfer::where('type','withdraw')->where('status',1)->latest('created_at')->first();
        if($check_transfers){
            return response()->json([$check_transfers],200);
        }else{
            return response()->json([],204);
        }
    }

    public function smsOTP(Request $request){
        $log = new Logs;
        $log->log = "SMS otp : ".$request->sms;
        $log->save();

        try{
            $chectText1 = explode(' ',$request->sms);
            if($chectText1[0] === 'คุณกำลังโอนเงินให้'){
                $otp = $chectText1[12];
                $refNo = explode(')',$chectText1[14])[0];


                $trans = Transfer::where('refNo',$refNo)->first();
                if($trans){
                    $trans->otp = $otp;
                    $trans->save();
                }
                return response()->json(["OTP"=>$otp,"refNo"=>$refNo],200);
            }

        } catch(\Exception $e){
            Log::error("Error : ".$e->getMessage());
            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT '.env('APP_NAME'))
                ->line('พบข้อผิดพลาดในการตรวจสอบ SMS')
                ->send();
            return response()->json(['message' => 'พบข้อผิดพลาดในการตรวจสอบ SMS'], 400);
        }
    }

    public function smsRequest(Request $request){
        $log = new Logs;
        $log->log = "SMS : ".$request->sms;
        $log->save();
        date_default_timezone_set("Asia/Bangkok");

        try{

            $key = explode(' ',$request->sms)[4] ;
            if($key == 'รับโอนจาก'){
                $amount = explode(' ',$request->sms)[6];
            }elseif($key == 'เงินเข้า'){
                $key = 'รับโอนจาก';
                $amount = explode(' คงเหลือ',explode('เงินเข้า ',$request->sms)[1])[0] ;
            }


            // return now()->subMinute(5);
            // return response()->json(["amount"=>$amount,"key"=>$key],200);
        } catch(\Exception $e){
            Log::error("Error : ".$e->getMessage());

            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT '.env('APP_NAME'))
                ->line('พบข้อผิดพลาดในการตรวจสอบ SMS')
                ->send();

            return response()->json(['message' => 'พบข้อผิดพลาดในการตรวจสอบ SMS'], 400);
        }

        if($key == 'รับโอนจาก'){
            $bonus =0;
            $transfer = Transfer::where('amount',$amount)->where('type','deposit')->where('status',1)->whereTime('created_at', '>=', now()->subMinute(5))->first();
            if($transfer){
                // return response()->json(["text"=>$chectText1,"amount"=>$amount,"key"=>$key,"transfer"=>$transfer]);
                $member = Members::find($transfer->member_id);
                $amount_betflix=0;
                $old_balance = $member->wallet_balance;

                $transfer->status = 2;
                $transfer->status_code ="BOT.อนุมัติ";
                $transfer->old_balance = $old_balance;

                if($transfer->promotion_id != 0){
                    $pro = Promotion::find($transfer->promotion_id);
                    $user_transfer = Transfer::where('member_id',$member->id)->where('status',2)->where('type','deposit')->get();  /// เช็คฝากครั้งแรก
                    $user_transfer_count = $user_transfer->count();
                    if($user_transfer_count == 0){

                        if($transfer->amount >= 20 && $transfer->amount < 300){  /// สมาชิกใหม่ ฝาก 20 รับ 100 บาท
                            $bonus = 80;
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            $amount_betflix = $transfer->amount + $bonus;
                            $transfer->promotion ='สมาชิกใหม่ ฝาก 20 รับ 100 บาท';
                            Log::info("สมาชิกใหม่ ฝาก 20 รับ 100 บาท");

                        }else if($transfer->amount >= 300){  /// สมาชิกใหม่ ฝาก 300 รับ 500 บาท
                            $bonus = 200;
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            $amount_betflix = $transfer->amount + $bonus;
                            $transfer->promotion ='สมาชิกใหม่ ฝาก 300 รับ 500 บาท';
                            Log::info("สมาชิกใหม่ ฝาก 300 รับ 500 บาท");

                        }
                    }else{
                        $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount;
                        $amount_betflix = $transfer->amount;
                    }

                }else{
                    $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                    $amount_betflix = (float) $transfer->amount;
                }


                $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username,floor($amount_betflix));
                Log::info('Deposit Betflix '.$bf_deposit.' '.floor($amount_betflix).' User =  '.$member->username);

                if($bf_deposit == "success"){
                    $member->save();
                    $transfer->new_balance = $member->wallet_balance;
                    $transfer->save();

                    if($transfer->promotion_id != 0){
                        PromotionUsed::create([
                            'member_id' => $member->id,
                            'promotion_id' => $transfer->promotion_id,
                            'promotion_name' => $pro->name,
                            'amount' => $bonus
                        ]);
                    }
                }

                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                // ->content('Choose an option:')
                ->line('BOT '.env('APP_NAME'))
                ->line('ทำรายการสำเร็จ โอนเครดิตเข้า '.$member->username)
                ->line('จำนวน :'.$amount)
                ->line('Bonus :'.$bonus)
                // ->button('View page', env('APP_URL'))
                // ->button('View page',env('APP_URL'))
                // ->keyboard('Button 1')
                // ->keyboard('Button 2')
                ->send();

                return response()->json(['message' => 'SMS request sent successfully.','txt' => 'Amount :'.$amount], 200);
            }else{
                // TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                // // ->content('Choose an option:')
                // ->line('BOT '.env('APP_NAME'))
                // ->line('ไม่พบรายการโอนเงินในช่วงเวลา')
                // ->line('จำนวน :'.$amount)
                // // ->button('View page', env('APP_URL'))
                // // ->button('View page',env('APP_URL'))
                // // ->keyboard('Button 1')
                // // ->keyboard('Button 2')
                // ->send();
                // return response()->json(['message' => 'ไม่พบรายการโอนเงินในช่วงเวลา','txt' => 'Amount :'.$amount], 404);
                $this->sms_step2($request->sms);
            }


        }else{
            return response()->json(['message' => 'SMS Not valid.','txt' => 'Amount :'.$amount.', Text3 : '.$key], 200);
        }
    }
    public function sms_step2($sms){
        $log = new Logs;
        $log->log = "sms_step2 : ".$sms;
        $log->save();


        try{
            $key = explode(' ',$sms)[4] ;
            if($key == 'รับโอนจาก'){
                $amount = explode(' ',$sms)[6];
            }elseif($key == 'เงินเข้า'){
                $key = 'รับโอนจาก';
                $amount = explode(' คงเหลือ',explode('เงินเข้า ',$sms)[1])[0] ;
            }

            // return response()->json(["amount"=>$amount,"key"=>$key],200);
        } catch(\Exception $e){
            Log::error("Error : ".$e->getMessage());

            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT '.env('APP_NAME'))
                ->line('พบข้อผิดพลาดในการตรวจสอบ SMS')
                ->send();

            return response()->json(['message' => 'พบข้อผิดพลาดในการตรวจสอบ SMS'], 400);
        }

        if($key == 'รับโอนจาก'){
            $bonus =0;
            $transfer = Transfer::where('amount',$amount)->where('type','deposit')->where('status',1)->whereTime('created_at', '>=', now()->subMinute(5))->first();
            if($transfer){
                // return response()->json(["text"=>$chectText1,"amount"=>$amount,"key"=>$key,"transfer"=>$transfer]);
                $member = Members::find($transfer->member_id);
                $amount_betflix=0;
                $old_balance = $member->wallet_balance;

                $transfer->status = 2;
                $transfer->status_code ="BOT.อนุมัติ";
                $transfer->old_balance = $old_balance;

                if($transfer->promotion_id != 0){
                    $pro = Promotion::find($transfer->promotion_id);
                    $user_transfer = Transfer::where('member_id',$member->id)->where('status',2)->where('type','deposit')->get();  /// เช็คฝากครั้งแรก
                    $user_transfer_count = $user_transfer->count();
                    if($user_transfer_count == 0){

                        if($transfer->amount >= 20 && $transfer->amount < 300){  /// สมาชิกใหม่ ฝาก 20 รับ 100 บาท
                            $bonus = 80;
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            $amount_betflix = $transfer->amount + $bonus;
                            $transfer->promotion ='สมาชิกใหม่ ฝาก 20 รับ 100 บาท';
                        }else if($transfer->amount >= 300){  /// สมาชิกใหม่ ฝาก 300 รับ 500 บาท
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
                    $amount_betflix = (float) $transfer->amount;
                }




                $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username,floor($amount_betflix));
                Log::info('Deposit Betflix '.$bf_deposit.' '.floor($amount_betflix).' User =  '.$member->username);

                if($bf_deposit == "success"){
                    $member->save();
                    $transfer->new_balance = $member->wallet_balance;
                    $transfer->save();

                    if($transfer->promotion_id != 0){
                        PromotionUsed::create([
                            'member_id' => $member->id,
                            'promotion_id' => $transfer->promotion_id,
                            'promotion_name' => $pro->name,
                            'amount' => $bonus
                        ]);
                    }
                }

                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                // ->content('Choose an option:')
                ->line('BOT '.env('APP_NAME'))
                ->line('ทำรายการสำเร็จ โอนเครดิตเข้า '.$member->username)
                ->line('จำนวน :'.$amount)
                ->line('Bonus :'.$bonus)
                // ->button('View page', env('APP_URL'))
                // ->button('View page',env('APP_URL'))
                // ->keyboard('Button 1')
                // ->keyboard('Button 2')
                ->send();

                return response()->json(['message' => 'SMS request sent successfully.','txt' => 'Amount :'.$amount], 200);
            }else{
                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                // ->content('Choose an option:')
                ->line('BOT '.env('APP_NAME'))
                ->line('ไม่พบรายการโอนเงินในช่วงเวลา ')
                ->line('จำนวน :'.$amount)
                ->line('subMinute(5)'.now()->subMinute(5))
                // ->button('View page', env('APP_URL'))
                // ->button('View page',env('APP_URL'))
                // ->keyboard('Button 1')
                // ->keyboard('Button 2')
                ->send();
                return response()->json(['message' => 'ไม่พบรายการโอนเงินในช่วงเวลา','txt' => 'Amount :'.$amount], 404);
            }


        }else{
            return response()->json(['message' => 'SMS Not valid.','txt' => 'Amount :'.$amount.', Text3 : '.$key], 200);
        }
    }
    public function sms_scb(Request $request){
        $log = new Logs;
        $log->log = "SMS scb : ".$request->sms;
        $log->save();


        try{
            $bank_number = explode(' ',$request->sms)[5];
            $amount = explode(' ',$request->sms)[6];
            $key = explode(' ',$request->sms)[4] ;

            // return response()->json(["amount"=>$amount,"key"=>$key],200);
        } catch(\Exception $e){
            Log::error("Error : ".$e->getMessage());

            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT '.env('APP_NAME'))
                ->line('พบข้อผิดพลาดในการตรวจสอบ SMS')
                ->send();

            return response()->json(['message' => 'พบข้อผิดพลาดในการตรวจสอบ SMS'], 400);
        }

        if($key == 'รับโอนจาก'){
            $bonus =0;
            $transfer = Transfer::where('amount',$amount)->where('type','deposit')->where('status',1)->whereTime('created_at', '>=', now()->subMinute(5))->first();
            if($transfer){
                // return response()->json(["text"=>$chectText1,"amount"=>$amount,"key"=>$key,"transfer"=>$transfer]);
                $member = Members::find($transfer->member_id);
                $amount_betflix=0;
                $old_balance = $member->wallet_balance;

                $transfer->status = 2;
                $transfer->status_code ="อนุมัติ";
                $transfer->old_balance = $old_balance;

                if($transfer->promotion_id != 0){
                    $pro = Promotion::find($transfer->promotion_id);
                    $user_transfer = Transfer::where('member_id',$member->id)->where('status',2)->where('type','deposit')->get();  /// เช็คฝากครั้งแรก
                    $user_transfer_count = $user_transfer->count();
                    if($user_transfer_count == 0){

                        if($transfer->amount >= 20 && $transfer->amount < 300){  /// สมาชิกใหม่ ฝาก 20 รับ 100 บาท
                            $bonus = 80;
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            $amount_betflix = $transfer->amount + $bonus;
                            $transfer->promotion ='สมาชิกใหม่ ฝาก 20 รับ 100 บาท';
                        }else if($transfer->amount >= 300){  /// สมาชิกใหม่ ฝาก 300 รับ 500 บาท
                            $bonus = 200;
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            $amount_betflix = $transfer->amount + $bonus;
                            $transfer->promotion ='สมาชิกใหม่ ฝาก 300 รับ 500 บาท';
                        }
                    }else{
                        $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount;
                        $amount_betflix = $transfer->amount;
                    }

                    // if($transfer->promotion_id == 1){
                    //     $b =  (float) $member->wallet_balance + $transfer->amount + 100;
                    //     $amount_betflix = $transfer->amount + 100;
                    // }else{
                    //     $b =  (float) $member->wallet_balance + (float) $transfer->amount + ((float) $transfer->amount * $pro->bonus / 100);
                    //     $amount_betflix = (float) $transfer->amount + ((float) $transfer->amount * $pro->bonus / 100);
                    // }


                }else{
                    $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                    $amount_betflix = (float) $transfer->amount;
                }
                $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username,floor($amount_betflix));
                Log::info('Deposit Betflix '.$bf_deposit.' '.floor($amount_betflix).' User =  '.$member->username);

                if($bf_deposit == "success"){
                    $member->save();
                    $transfer->new_balance = $member->wallet_balance;
                    $transfer->save();

                    if($transfer->promotion_id != 0){
                        PromotionUsed::create([
                            'member_id' => $member->id,
                            'promotion_id' => $transfer->promotion_id,
                            'promotion_name' => $pro->name,
                            'amount' => $bonus
                        ]);
                    }
                }

                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                // ->content('Choose an option:')
                ->line('BOT '.env('APP_NAME'))
                ->line('ทำรายการสำเร็จ โอนเครดิตเข้า '.$member->username)
                ->line('จำนวน :'.$amount)
                ->line('Bonus :'.$bonus)
                // ->button('View page', env('APP_URL'))
                // ->button('View page',env('APP_URL'))
                // ->keyboard('Button 1')
                // ->keyboard('Button 2')
                ->send();

                return response()->json(['message' => 'SMS request sent successfully.','txt' => 'Amount :'.$amount], 200);
            }else{
                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                // ->content('Choose an option:')
                ->line('BOT '.env('APP_NAME'))
                ->line('ไม่พบรายการโอนเงินในช่วงเวลา')
                ->line('จำนวน :'.$amount)
                // ->button('View page', env('APP_URL'))
                // ->button('View page',env('APP_URL'))
                // ->keyboard('Button 1')
                // ->keyboard('Button 2')
                ->send();
                return response()->json(['message' => 'ไม่พบรายการโอนเงินในช่วงเวลา','txt' => 'Amount :'.$amount], 404);
            }


        }else{
            return response()->json(['message' => 'SMS Not valid.','txt' => 'Amount :'.$amount.', Text3 : '.$key], 200);
        }
    }

    public function getOTP($id){
        Log::info('getOTP id: '.$id);
        $transfer = Transfer::where('id',$id)->first();
        if($transfer){
            Log::info('otp '.$transfer->otp);
            return response()->json([$transfer->otp], 200);
        }
    }

    public function getTranfer($id){
        $transfer = Transfer::find($id);
        if($transfer){
            return response()->json([ $transfer], 200);
        }
    }

    public function upDaterefNo(Request $request){
        $transfer = Transfer::find($request->id);
        $transfer->refNo = $request->refNo;
        $transfer->save();
        return response()->json(['message' => 'Ref No updated successfully'], 200);
    }

    // public function updateOTP(Request $request){
    //     $transfer = Transfer::where('refNo',$request->refNo);
    //     $transfer->otp = $request->otp;
    //     $transfer->save();
    //     return response()->json(['message' => 'OTP updated successfully'], 200);
    // }

    public function approvewithdraw(Request $request)
    {
        Log::info("approvewithdraw ".$request->getContent());
        $member = Members::find($request->member_id);
        $transfer = Transfer::find($request->id);
        error_log($request->type.' approve '.$member->username.' Balance =  '.$member->wallet_balance.' transfer amount ='.$transfer->amount);

        if($transfer->status == 2 || $transfer->status == 3){
            return response()->json(['message' => 'Transfer approved !!!'], 401);
        }


        $image = str_replace('data:image/png;base64,', '', $request->file);
        $image = str_replace(' ', '+', $image);
        $imageName = $request->id.'.'.'png';
        \File::put(public_path().'/slip/'. $imageName, base64_decode($image));
        $path = '/slip/'.$imageName;

        $transfer->old_balance = $member->wallet_balance;
        $old_balance = $member->wallet_balance;

        $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Withdraw($member->username,floor($transfer->amount));
        Log::info('Betflix Withdraw '.$bf_deposit.' '.floor($transfer->amount).' User =  '.$member->username);

        $bank = Bank::where('account_no',$transfer->deposit_to_bank_no)->first();
            if($bank){
                $bank->balance = (float) $bank->balance - (float) $transfer->amount;
                $bank->save();
            }

        $transfer->ref_id = $request->ref;
        $transfer->status = 2;
        $transfer->status_code ="อนุมัติ";
        $transfer->old_balance = $old_balance;
        $transfer->new_balance = $member->wallet_balance;
        $transfer->withdraw_slip = $path;
        $transfer->save();


        return response()->json([$transfer],200);;
    }

    public function wrongdeposit_insert(Request $request){
        $request->validate([
            'amount' => ['required'],
            'member_id' => ['required'],
            'bank_from_number' => ['required'],
            'bank_from_name' => ['required'],
            'bank_to' => ['required'],
            'image' => ['required'],
        ]);

        $member = Members::where('username', '=',$request->member_id)->first();
        if(!$member){
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'member_id' => ['Member not found.'],
             ]);
             throw $error;
        }

        $bank = Bank::find($request->bank_to);

        $data = new Wrongdeposit;
        $data->member_id = $member->id;
        $data->username = $member->username;
        $data->amount = $request->amount;
        $data->note = $request->note;

        $data->bank_from_number = $request->bank_from_number;
        $data->bank_from_name = $request->bank_from_name;
        $data->bank_from_account_name = $request->bank_from_account_name;

        $data->bank_to_number = $bank->account_no;
        $data->bank_to_name = $bank->bank_name;
        $data->bank_to_account_name = $bank->account_name;

        if($request->image){
            $fileName = rand().'.'.$request->image->extension();
            $request->image->move(public_path('images/tranfer'), $fileName);
            $data->image = "/images/tranfer/".$fileName;
        }

        $data->user_id = auth()->user()->id;
        $data->save();

        return redirect()->route('report.wrongdeposit')->with('status','success');
    }

    public function wrongdeposit_update(Request $request){
        $request->validate([
            'amount' => ['required'],
            'member_id' => ['required'],
            'bank_from_number' => ['required'],
            'bank_from_name' => ['required'],
            'bank_to' => ['required'],
            'image' => ['required'],
        ]);

        $member = Members::where('username', '=',$request->member_id)->first();
        if(!$member){
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'member_id' => ['Member not found.'],
             ]);
             throw $error;
        }

        $bank = Bank::find($request->bank_to);

        $data = Wrongdeposit::find($request->id);
        $data->member_id = $member->id;
        $data->username = $member->username;
        $data->amount = $request->amount;
        $data->note = $request->note;

        $data->bank_from_number = $request->bank_from_number;
        $data->bank_from_name = $request->bank_from_name;
        $data->bank_from_account_name = $request->bank_from_account_name;

        $data->bank_to_number = $bank->account_no;
        $data->bank_to_name = $bank->bank_name;
        $data->bank_to_account_name = $bank->account_name;

        if($request->image){
            $fileName = rand().'.'.$request->image->extension();
            $request->image->move(public_path('images/tranfer'), $fileName);
            $data->image = "/images/tranfer/".$fileName;
        }

        $data->user_id = auth()->user()->id;
        $data->save();

        return redirect()->route('report.wrongdeposit')->with('status','success');
    }

    public function turnover_on(Request $request){
        $data = Transfer::find($request->id);
        $data->turnover_on = 0;
        $data->save();
        return redirect()->route('managemember.transaction')->with('status','success');
    }


    public function smsTest(Request $request){
        $log = new Logs;
        $log->log = "SMS : ".$request->sms;
        $log->save();
        date_default_timezone_set("Asia/Bangkok");

        try{

            $key = explode(' ',$request->sms)[4] ;
            if($key == 'รับโอนจาก'){
                $amount = explode(' ',$request->sms)[6];
            }elseif($key == 'เงินเข้า'){
                $key = 'รับโอนจาก';
                $amount = explode(' คงเหลือ',explode('เงินเข้า ',$request->sms)[1])[0] ;
            }


            // return now()->subMinute(5);
            // return response()->json(["amount"=>$amount,"key"=>$key],200);
        } catch(\Exception $e){

            return response()->json(['message' => 'พบข้อผิดพลาดในการตรวจสอบ SMS'], 400);
        }

        if($key == 'รับโอนจาก'){

            $transfer = Transfer::where('amount',$amount)->where('type','deposit')->whereTime('created_at', '>=', now()->subMinute($request->subMinute))->get();
            return response()->json(['transfer' => $transfer], 200);

        }else{
            return response()->json(['message' => 'SMS Not valid.','txt' => 'Amount :'.$amount.', Text3 : '.$key], 200);
        }
    }
}
