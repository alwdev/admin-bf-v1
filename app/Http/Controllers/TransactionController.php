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
use App\Models\WheelSpin;
use App\Models\PromotionUsed;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transfer = Transfer::join('members', function ($join) {
            $join->on('members.id', '=', 'transfer.member_id');
        })
            ->select(\DB::raw('transfer.*,members.bank_number,members.account_name,members.bank_name,members.username'))
            ->where('transfer.type', '!=', 'cashback')->where('transfer.type', '!=', 'commission')->where('transfer.type', '!=', 'wheel')
            ->orderby('transfer.created_at', 'desc')
            ->get();

        return view('transaction.list', compact('transfer'));
    }

    public function checkTurnOver($mid)
    {
        $member = Members::where('id', $mid)->first();
        $turn_over = app(\App\Http\Controllers\BetflixController::class)->lastDay_TurnOver($member->username);
        $check_transfers = Transfer::where('member_id', $mid)->where('type', 'deposit')->latest('created_at')->first();

        if ($check_transfers) {
            if ($check_transfers->turnover_on == 1) {
                if ($check_transfers->promotion_id != 0) {
                    $check_balance = Members::where('id', $mid)->first();

                    // $turn_over = app(\App\Http\Controllers\BetflixController::class)->lastDay_TurnOver($member->username);
                    try {
                        $turn_over = app(\App\Http\Controllers\BetflixController::class)->lastDay_TurnOver($member->username);
                    } catch (\Throwable $th) {
                        $turnover = 0;
                    }
                    $current_balance = $check_balance->wallet_balance;
                    $last_transfers = $check_transfers->amount;

                    $promotion = Promotion::find($check_transfers->promotion_id);
                    if (!is_null($promotion)) {
                        if ($promotion->id == 1) {
                            if ($turn_over >= 100) {
                                return "ผ่าน";
                            } else {
                                return $turn_over;
                            }
                        } else {

                            if ($turn_over > ($last_transfers * (int) $promotion->turnover)) {
                                return "ผ่าน";
                            } else {
                                return $turn_over;
                            }
                        }
                    }
                }
            }
        }
    }

    public function Checktransfer()
    {
        $check_transfers = Transfer::where('type', 'withdraw')->where('status', 1)->latest('created_at')->first();
        if ($check_transfers) {
            $check_transfers->status = 4;
            $check_transfers->status_code = 'กำลังดำเนินการ';
            $check_transfers->save();

            return response()->json([$check_transfers], 200);
        } else {
            return response()->json([], 204);
        }
    }

    public function smsOTP(Request $request)
    {
        $log = new Logs;
        $log->sms = "SMS otp : " . $request->sms;
        $log->save();

        try {
            $chectText1 = explode(' ', $request->sms);
            // error_log($chectText1[0]);


            if ($chectText1[0] === 'คุณกำลังโอนเงินให้') {
                $refNo = explode(')', explode('รหัสอ้างอิง: ', $request->sms)[1])[0];
                $otp =  explode(' ',   explode('(รหัสอ้างอิง', explode('OTP: ', $request->sms)[1])[0])[0];
                // error_log("refNo =".$refNo);
                // error_log("otp =".$otp);


                $trans = Transfer::where('refNo', $refNo)->first();
                if ($trans) {
                    $trans->otp = $otp;
                    $trans->save();
                    return response()->json(["OTP" => $otp, "refNo" => $refNo], 200);
                } else {
                    TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                        ->line('BOT ' . env('APP_NAME'))
                        ->line('พบข้อผิดพลาดในการตรวจสอบ SMS')
                        ->line("refNo =" . $refNo)
                        ->line("otp =" . $otp)
                        ->send();
                }
            }
        } catch (\Exception $e) {
            Log::error("Error : " . $e->getMessage());
            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT ' . env('APP_NAME'))
                ->line('พบข้อผิดพลาดในการตรวจสอบ SMS')
                ->send();
            return response()->json(['message' => 'พบข้อผิดพลาดในการตรวจสอบ SMS'], 400);
        }
    }

    public function lineNotify_tranfer(Request $request)
    {
        $log = new Logs;
        $log->log = "lineNotify_tranfer : " . $request->getContent();
        $log->save();
        $data = json_decode($request->getContent(), true);
        error_log("lineNotify_tranfer data = " . json_encode($data));

        $transfer = Transfer::where('amount', $request->amount)
            ->where('deposit_from_bank_no', $request->acc_no)
            ->where('type', 'deposit')
            ->where('status', 1)
            ->whereTime('created_at', '>=', now()
                ->subMinute(5))->first();

        if ($transfer) {
            $lastFourCharacters = substr($transfer->deposit_from_bank_no, -4);
            if ($lastFourCharacters == $request->acc_no) {
               $do_transfer = $this->lineNotify_deposit($transfer);
                return  $do_transfer;
            } else {
                return 404;
            }
        } else {
            return 404;
        }
    }

    public function lineNotify_deposit(Transfer $transfer)
    {
        if ($transfer) {
                $member = Members::find($transfer->member_id);
                $amount_betflix = 0;
                $old_balance = $member->wallet_balance;

                $transfer->status = 2;
                $transfer->status_code = "BOT.อนุมัติ";
                $transfer->old_balance = $old_balance;

                $message = "";
                $pro_name = "";

                if ($transfer->promotion_id != 0) {
                    error_log("promotion id = " . $transfer->promotion_id);
                    if ($transfer->turnover_on == 1) {
                        error_log("turnover on = " . $transfer->turnover_on);
                        $pro = Promotion::find($transfer->promotion_id);
                        $pro_name = $pro->name;
                        $user_transfer = Transfer::where('member_id', $member->id)->where('status', 2)->where('type', 'deposit')->get();  /// เช็คฝากครั้งแรก
                        $user_transfer_count = $user_transfer->count();
                        error_log("user transfer count = " . $user_transfer_count);
                        error_log("Pro is_newuser = " . $pro->is_newuser);
                        if ($pro->is_newuser == 1) { //โปร member ใหม่
                            error_log("โปร member ใหม่");
                            if ($user_transfer_count == 0) {
                                /// ฝากครั้งแรก
                                error_log("เข้าเงื่อนไข member ใหม่");
                                $message .= "เข้าเงื่อนไข member ใหม่, ";
                                $bonus = $pro->bonus;
                                $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                                $amount_betflix = $transfer->amount + $bonus;
                                $transfer->promotion = $pro->name;
                            } else {
                                error_log("ไม่เข้าเงื่อนไข member ใหม่");
                                $message .= "ไม่เข้าเงื่อนไข member ใหม่, ";
                                $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount;
                                $amount_betflix = $transfer->amount;
                            }
                        } else { //โปร member ทุกคน
                            error_log("โปร member ทุกคน");
                            $message .= "โปร member ทุกคน, ";
                            $bonus = $pro->bonus;
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            $amount_betflix = $transfer->amount + $bonus;
                            $transfer->promotion = $pro->name;
                        }
                    } else {
                        $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                        $amount_betflix = $transfer->amount;
                    }
                } else { //ไม่มีโปร
                    error_log("ไม่มีโปร / ไม่กดรับโปร");
                    $message .= "ไม่มีโปร / ไม่กดรับโปร, ";
                    $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                    $amount_betflix = $transfer->amount;
                }



                $bf_deposit =  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username, floor($amount_betflix));
                Log::info('Deposit Betflix ' . $bf_deposit . ' ' . floor($amount_betflix) . ' User =  ' . $member->username);

                if ($bf_deposit == "success") {

                    $wheel_setting = WheelSpin::first();
                    if ((float) $transfer->amount >= (float) $wheel_setting->ticket_condition) {
                        $total_spin = floor((float) $transfer->amount / (float) $wheel_setting->ticket_condition);
                        $member->remaining_spin = (float) $member->remaining_spin + (float) $total_spin;
                    }

                    $member->save();
                    $transfer->new_balance = $member->wallet_balance;
                    $transfer->save();

                    $bank = Bank::where('account_no', $transfer->deposit_to_bank_no)->first();
                    if ($bank) {
                        $bank->balance = (float) $bank->balance + (float) $transfer->amount;
                        $bank->save();
                    }

                    if ($transfer->promotion_id != 0) {
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
                    ->line('BOT ' . env('APP_NAME'))
                    ->line('ทำรายการสำเร็จ โอนเครดิตเข้า ' . $member->username)
                    ->line('จำนวน :' . $transfer->amount)
                    ->line('Bonus :' . $bonus)
                    ->line($pro_name . ': ' . $message)
                    ->send();

                return  200;
            }
    }

    public function smsRequest(Request $request)
    {
        $log = new Logs;
        $log->sms = "smsRequest : " . $request->sms;
        $log->save();

        if ($request->sms == 'test-sms') {
            return  response()->json(['message' => 'test ok'], 200);
        }

        date_default_timezone_set("Asia/Bangkok");
        $amount = '';
        $key = '';
        try {

            $key = explode(' ', $request->sms)[4];
            if ($key == 'รับโอนจาก') {
                $amount = explode(' ', $request->sms)[6];
            } elseif ($key == 'เงินเข้า') {
                $key = 'รับโอนจาก';
                $amount = explode(' คงเหลือ', explode('เงินเข้า ', $request->sms)[1])[0];
            }
            $amount = str_replace(',', '', $amount);

            // return now()->subMinute(5);
            // return response()->json(["amount"=>$amount,"key"=>$key],200);
        } catch (\Exception $e) {
            Log::error("Error : " . $e->getMessage());

            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT ' . env('APP_NAME'))
                ->line('พบข้อผิดพลาดในการตรวจสอบ SMS')
                ->line($e->getMessage())
                ->send();

            return response()->json(['message' => 'พบข้อผิดพลาดในการตรวจสอบ SMS'], 400);
        }

        if ($key == 'รับโอนจาก') {
            $bonus = 0;
            $transfer = Transfer::where('amount', $amount)->where('type', 'deposit')->where('status', 1)->whereTime('created_at', '>=', now()->subMinute(5))->first();
            if ($transfer) {
                // return response()->json(["text"=>$chectText1,"amount"=>$amount,"key"=>$key,"transfer"=>$transfer]);
                $member = Members::find($transfer->member_id);
                $amount_betflix = 0;
                $old_balance = $member->wallet_balance;

                $transfer->status = 2;
                $transfer->status_code = "BOT.อนุมัติ";
                $transfer->old_balance = $old_balance;

                $message = "";
                $pro_name = "";

                if ($transfer->promotion_id != 0) {
                    error_log("promotion id = " . $transfer->promotion_id);
                    if ($transfer->turnover_on == 1) {
                        error_log("turnover on = " . $transfer->turnover_on);
                        $pro = Promotion::find($transfer->promotion_id);
                        $pro_name = $pro->name;
                        $user_transfer = Transfer::where('member_id', $member->id)->where('status', 2)->where('type', 'deposit')->get();  /// เช็คฝากครั้งแรก
                        $user_transfer_count = $user_transfer->count();
                        error_log("user transfer count = " . $user_transfer_count);
                        error_log("Pro is_newuser = " . $pro->is_newuser);
                        if ($pro->is_newuser == 1) { //โปร member ใหม่
                            error_log("โปร member ใหม่");
                            if ($user_transfer_count == 0) {
                                /// ฝากครั้งแรก
                                error_log("เข้าเงื่อนไข member ใหม่");
                                $message .= "เข้าเงื่อนไข member ใหม่, ";
                                $bonus = $pro->bonus;
                                $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                                $amount_betflix = $transfer->amount + $bonus;
                                $transfer->promotion = $pro->name;
                            } else {
                                error_log("ไม่เข้าเงื่อนไข member ใหม่");
                                $message .= "ไม่เข้าเงื่อนไข member ใหม่, ";
                                $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount;
                                $amount_betflix = $transfer->amount;
                            }
                        } else { //โปร member ทุกคน
                            error_log("โปร member ทุกคน");
                            $message .= "โปร member ทุกคน, ";
                            $bonus = $pro->bonus;
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            $amount_betflix = $transfer->amount + $bonus;
                            $transfer->promotion = $pro->name;
                        }
                    } else {
                        $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                        $amount_betflix = $transfer->amount;
                    }
                } else { //ไม่มีโปร
                    error_log("ไม่มีโปร / ไม่กดรับโปร");
                    $message .= "ไม่มีโปร / ไม่กดรับโปร, ";
                    $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                    $amount_betflix = $transfer->amount;
                }



                $bf_deposit =  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username, floor($amount_betflix));
                Log::info('Deposit Betflix ' . $bf_deposit . ' ' . floor($amount_betflix) . ' User =  ' . $member->username);

                if ($bf_deposit == "success") {

                    $wheel_setting = WheelSpin::first();
                    if ((float) $transfer->amount >= (float) $wheel_setting->ticket_condition) {
                        $total_spin = floor((float) $transfer->amount / (float) $wheel_setting->ticket_condition);
                        $member->remaining_spin = (float) $member->remaining_spin + (float) $total_spin;
                    }

                    $member->save();
                    $transfer->new_balance = $member->wallet_balance;
                    $transfer->save();

                    $bank = Bank::where('account_no', $transfer->deposit_to_bank_no)->first();
                    if ($bank) {
                        $bank->balance = (float) $bank->balance + (float) $transfer->amount;
                        $bank->save();
                    }

                    if ($transfer->promotion_id != 0) {
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
                    ->line('BOT ' . env('APP_NAME'))
                    ->line('ทำรายการสำเร็จ โอนเครดิตเข้า ' . $member->username)
                    ->line('จำนวน :' . $amount)
                    ->line('Bonus :' . $bonus)
                    ->line($pro_name . ': ' . $message)
                    ->send();

                return response()->json(['message' => 'SMS request sent successfully.', 'txt' => 'Amount :' . $amount], 200);
            } else {

                $this->sms_step2($request->sms);
            }
        } else {
            return response()->json(['message' => 'SMS Not valid.'], 200);
        }
    }
    public function sms_step2($sms)
    {
        // $log = new Logs;
        // $log->sms = "sms_step2 : " . $sms;
        // $log->save();
        $amount = '';
        $key = '';
        try {
            $key = explode(' ', $sms)[4];
            if ($key == 'รับโอนจาก') {
                $amount = explode(' ', $sms)[6];
            } elseif ($key == 'เงินเข้า') {
                $key = 'รับโอนจาก';
                $amount = explode(' คงเหลือ', explode('เงินเข้า ', $sms)[1])[0];
            }
            $amount = str_replace(',', '', $amount);
            // return response()->json(["amount"=>$amount,"key"=>$key],200);
        } catch (\Exception $e) {
            Log::error("Error : " . $e->getMessage());

            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT ' . env('APP_NAME'))
                ->line('sms_step2 พบข้อผิดพลาดในการตรวจสอบ SMS')
                ->line($e->getMessage())
                ->send();

            return response()->json(['message' => 'พบข้อผิดพลาดในการตรวจสอบ SMS'], 400);
        }

        if ($key == 'รับโอนจาก') {
            $bonus = 0;
            $transfer = Transfer::where('amount', $amount)->where('type', 'deposit')->where('status', 1)->whereTime('created_at', '>=', now()->subMinute(5))->first();
            if ($transfer) {
                // return response()->json(["text"=>$chectText1,"amount"=>$amount,"key"=>$key,"transfer"=>$transfer]);
                $member = Members::find($transfer->member_id);
                $amount_betflix = 0;
                $old_balance = $member->wallet_balance;

                $transfer->status = 2;
                $transfer->status_code = "BOT.อนุมัติ";
                $transfer->old_balance = $old_balance;

                $message = "";
                $pro_name = "";

                if ($transfer->promotion_id != 0) {
                    error_log("promotion id = " . $transfer->promotion_id);
                    if ($transfer->turnover_on == 1) {
                        error_log("turnover on = " . $transfer->turnover_on);
                        $pro = Promotion::find($transfer->promotion_id);
                        $pro_name = $pro->name;
                        $user_transfer = Transfer::where('member_id', $member->id)->where('status', 2)->where('type', 'deposit')->get();  /// เช็คฝากครั้งแรก
                        $user_transfer_count = $user_transfer->count();
                        error_log("user transfer count = " . $user_transfer_count);
                        error_log("Pro is_newuser = " . $pro->is_newuser);
                        if ($pro->is_newuser == 1) { //โปร member ใหม่
                            error_log("โปร member ใหม่");
                            if ($user_transfer_count == 0) {
                                /// ฝากครั้งแรก
                                error_log("เข้าเงื่อนไข member ใหม่");
                                $message .= "เข้าเงื่อนไข member ใหม่, ";
                                $bonus = $pro->bonus;
                                $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                                $amount_betflix = $transfer->amount + $bonus;
                                $transfer->promotion = $pro->name;
                            } else {
                                error_log("ไม่เข้าเงื่อนไข member ใหม่");
                                $message .= "ไม่เข้าเงื่อนไข member ใหม่, ";
                                $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount;
                                $amount_betflix = $transfer->amount;
                            }
                        } else { //โปร member ทุกคน
                            error_log("โปร member ทุกคน");
                            $message .= "โปร member ทุกคน, ";
                            $bonus = $pro->bonus;
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            $amount_betflix = $transfer->amount + $bonus;
                            $transfer->promotion = $pro->name;
                        }
                    } else {
                        $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                        $amount_betflix = $transfer->amount;
                    }
                } else { //ไม่มีโปร
                    error_log("ไม่มีโปร / ไม่กดรับโปร");
                    $message .= "ไม่มีโปร / ไม่กดรับโปร, ";
                    $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                    $amount_betflix = $transfer->amount;
                }


                $bf_deposit =  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username, floor($amount_betflix));
                Log::info('Deposit Betflix ' . $bf_deposit . ' ' . floor($amount_betflix) . ' User =  ' . $member->username);

                if ($bf_deposit == "success") {

                    $wheel_setting = WheelSpin::first();
                    if ((float) $transfer->amount >= (float) $wheel_setting->ticket_condition) {
                        $total_spin = floor((float) $transfer->amount / (float) $wheel_setting->ticket_condition);
                        $member->remaining_spin = (float) $member->remaining_spin + (float) $total_spin;
                    }

                    $member->save();
                    $transfer->new_balance = $member->wallet_balance;
                    $transfer->save();

                    $bank = Bank::where('account_no', $transfer->deposit_to_bank_no)->first();
                    if ($bank) {
                        $bank->balance = (float) $bank->balance + (float) $transfer->amount;
                        $bank->save();
                    }

                    if ($transfer->promotion_id != 0) {
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
                    ->line('BOT ' . env('APP_NAME'))
                    ->line('ทำรายการสำเร็จ โอนเครดิตเข้า ' . $member->username)
                    ->line('จำนวน :' . $amount)
                    ->line('Bonus :' . $bonus)
                    ->line($pro_name . ': ' . $message)
                    ->send();

                return response()->json(['message' => 'SMS request sent successfully.'], 200);
            } else {
                error_log('error No trans');


                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                    ->line('BOT ' . env('APP_NAME'))
                    ->line('ไม่พบรายการโอนเงินในช่วงเวลา')
                    ->line('จำนวน :' . $amount)
                    ->send();


                return response()->json(['message' => 'No trans'], 400);
            }
        } else {
            return response()->json(['message' => 'SMS Not valid.'], 200);
        }
    }

    public function smsRequest2(Request $request)
    {
        $text =   $_POST["text"];
        $log = new Logs;
        $log->sms = "smsRequest2 : " . $text;
        $log->save();

        try {

            $key = explode(' ', $text)[4];
            if ($key == 'รับโอนจาก') {
                $amount = explode(' ', $text)[6];
            } elseif ($key == 'เงินเข้า') {
                $key = 'รับโอนจาก';
                $amount = explode(' คงเหลือ', explode('เงินเข้า ', $text)[1])[0];
            }
            $amount = str_replace(',', '', $amount);


            // return now()->subMinute(5);
            // return response()->json(["amount"=>$amount,"key"=>$key],200);
        } catch (\Exception $e) {
            Log::error("Error : " . $e->getMessage());

            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT ' . env('APP_NAME'))
                ->line('พบข้อผิดพลาดในการตรวจสอบ SMS')
                ->send();

            return response()->json(['message' => 'พบข้อผิดพลาดในการตรวจสอบ SMS'], 400);
        }

        if ($key == 'รับโอนจาก') {
            $bonus = 0;
            $transfer = Transfer::where('amount', $amount)->where('type', 'deposit')->where('status', 1)->whereTime('created_at', '>=', now()->subMinute(5))->first();
            if ($transfer) {
                // return response()->json(["text"=>$chectText1,"amount"=>$amount,"key"=>$key,"transfer"=>$transfer]);
                $member = Members::find($transfer->member_id);
                $amount_betflix = 0;
                $old_balance = $member->wallet_balance;

                $transfer->status = 2;
                $transfer->status_code = "BOT.อนุมัติ";
                $transfer->old_balance = $old_balance;

                $message = "";
                $pro_name = "";

                if ($transfer->promotion_id != 0) {
                    error_log("promotion id = " . $transfer->promotion_id);
                    if ($transfer->turnover_on == 1) {
                        error_log("turnover on = " . $transfer->turnover_on);
                        $pro = Promotion::find($transfer->promotion_id);
                        $pro_name = $pro->name;
                        $user_transfer = Transfer::where('member_id', $member->id)->where('status', 2)->where('type', 'deposit')->get();  /// เช็คฝากครั้งแรก
                        $user_transfer_count = $user_transfer->count();
                        error_log("user transfer count = " . $user_transfer_count);
                        error_log("Pro is_newuser = " . $pro->is_newuser);
                        if ($pro->is_newuser == 1) { //โปร member ใหม่
                            error_log("โปร member ใหม่");
                            if ($user_transfer_count == 0) {
                                /// ฝากครั้งแรก
                                error_log("เข้าเงื่อนไข member ใหม่");
                                $message .= "เข้าเงื่อนไข member ใหม่, ";
                                $bonus = $pro->bonus;
                                $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                                $amount_betflix = $transfer->amount + $bonus;
                                $transfer->promotion = $pro->name;
                            } else {
                                error_log("ไม่เข้าเงื่อนไข member ใหม่");
                                $message .= "ไม่เข้าเงื่อนไข member ใหม่, ";
                                $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount;
                                $amount_betflix = $transfer->amount;
                            }
                        } else { //โปร member ทุกคน
                            error_log("โปร member ทุกคน");
                            $message .= "โปร member ทุกคน, ";
                            $bonus = $pro->bonus;
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            $amount_betflix = $transfer->amount + $bonus;
                            $transfer->promotion = $pro->name;
                        }
                    } else {
                        $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                        $amount_betflix = $transfer->amount;
                    }
                } else { //ไม่มีโปร
                    error_log("ไม่มีโปร / ไม่กดรับโปร");
                    $message .= "ไม่มีโปร / ไม่กดรับโปร, ";
                    $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                    $amount_betflix = $transfer->amount;
                }


                $bf_deposit =  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username, floor($amount_betflix));
                Log::info('Deposit Betflix ' . $bf_deposit . ' ' . floor($amount_betflix) . ' User =  ' . $member->username);

                if ($bf_deposit == "success") {

                    $wheel_setting = WheelSpin::first();
                    if ((float) $transfer->amount >= (float) $wheel_setting->ticket_condition) {
                        $total_spin = floor((float) $transfer->amount / (float) $wheel_setting->ticket_condition);
                        $member->remaining_spin = (float) $member->remaining_spin + (float) $total_spin;
                    }

                    $member->save();
                    $transfer->new_balance = $member->wallet_balance;
                    $transfer->save();

                    $bank = Bank::where('account_no', $transfer->deposit_to_bank_no)->first();
                    if ($bank) {
                        $bank->balance = (float) $bank->balance + (float) $transfer->amount;
                        $bank->save();
                    }

                    if ($transfer->promotion_id != 0) {
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
                    ->line('BOT ' . env('APP_NAME'))
                    ->line('ทำรายการสำเร็จ โอนเครดิตเข้า ' . $member->username)
                    ->line('จำนวน :' . $amount)
                    ->line('Bonus :' . $bonus)
                    ->line($pro_name . ': ' . $message)
                    ->send();

                return response()->json(['message' => 'SMS request sent successfully.', 'txt' => 'Amount :' . $amount], 200);
            } else {

                // $this->sms_step2($request->sms);
                return response()->json(['message' => 'SMS Not valid.', 'txt' => 'Amount :' . $amount . ', Text3 : ' . $key], 200);
            }
        } else {
            return response()->json(['message' => 'SMS Not valid.', 'txt' => 'Amount :' . $amount . ', Text3 : ' . $key], 200);
        }
    }

    public function sms_scb(Request $request)
    {
        $log = new Logs;
        $log->sms = "SMS scb : " . $request->sms;
        $log->save();


        try {
            $bank_number = explode(' ', $request->sms)[5];
            $amount = explode(' ', $request->sms)[6];
            $key = explode(' ', $request->sms)[4];

            // return response()->json(["amount"=>$amount,"key"=>$key],200);
        } catch (\Exception $e) {
            Log::error("Error : " . $e->getMessage());

            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT ' . env('APP_NAME'))
                ->line('พบข้อผิดพลาดในการตรวจสอบ SMS')
                ->send();

            return response()->json(['message' => 'พบข้อผิดพลาดในการตรวจสอบ SMS'], 400);
        }

        if ($key == 'รับโอนจาก') {
            $bonus = 0;
            $transfer = Transfer::where('amount', $amount)->where('type', 'deposit')->where('status', 1)->whereTime('created_at', '>=', now()->subMinute(5))->first();
            if ($transfer) {
                // return response()->json(["text"=>$chectText1,"amount"=>$amount,"key"=>$key,"transfer"=>$transfer]);
                $member = Members::find($transfer->member_id);
                $amount_betflix = 0;
                $old_balance = $member->wallet_balance;

                $transfer->status = 2;
                $transfer->status_code = "อนุมัติ";
                $transfer->old_balance = $old_balance;

                $message = "";
                $pro_name = "";

                if ($transfer->promotion_id != 0) {
                    error_log("promotion id = " . $transfer->promotion_id);
                    if ($transfer->turnover_on == 1) {
                        error_log("turnover on = " . $transfer->turnover_on);
                        $pro = Promotion::find($transfer->promotion_id);
                        $pro_name = $pro->name;
                        $user_transfer = Transfer::where('member_id', $member->id)->where('status', 2)->where('type', 'deposit')->get();  /// เช็คฝากครั้งแรก
                        $user_transfer_count = $user_transfer->count();
                        error_log("user transfer count = " . $user_transfer_count);
                        error_log("Pro is_newuser = " . $pro->is_newuser);
                        if ($pro->is_newuser == 1) { //โปร member ใหม่
                            error_log("โปร member ใหม่");
                            if ($user_transfer_count == 0) {
                                /// ฝากครั้งแรก
                                error_log("เข้าเงื่อนไข member ใหม่");
                                $message .= "เข้าเงื่อนไข member ใหม่, ";
                                $bonus = $pro->bonus;
                                $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                                $amount_betflix = $transfer->amount + $bonus;
                                $transfer->promotion = $pro->name;
                            } else {
                                error_log("ไม่เข้าเงื่อนไข member ใหม่");
                                $message .= "ไม่เข้าเงื่อนไข member ใหม่, ";
                                $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount;
                                $amount_betflix = $transfer->amount;
                            }
                        } else { //โปร member ทุกคน
                            error_log("โปร member ทุกคน");
                            $message .= "โปร member ทุกคน, ";
                            $bonus = $pro->bonus;
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            $amount_betflix = $transfer->amount + $bonus;
                            $transfer->promotion = $pro->name;
                        }
                    } else {
                        $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                        $amount_betflix = $transfer->amount;
                    }
                } else { //ไม่มีโปร
                    error_log("ไม่มีโปร / ไม่กดรับโปร");
                    $message .= "ไม่มีโปร / ไม่กดรับโปร, ";
                    $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                    $amount_betflix = $transfer->amount;
                }


                $bf_deposit =  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username, floor($amount_betflix));
                Log::info('Deposit Betflix ' . $bf_deposit . ' ' . floor($amount_betflix) . ' User =  ' . $member->username);

                if ($bf_deposit == "success") {

                    $wheel_setting = WheelSpin::first();
                    if ((float) $transfer->amount >= (float) $wheel_setting->ticket_condition) {
                        $total_spin = floor((float) $transfer->amount / (float) $wheel_setting->ticket_condition);
                        $member->remaining_spin = (float) $member->remaining_spin + (float) $total_spin;
                    }

                    $member->save();
                    $transfer->new_balance = $member->wallet_balance;
                    $transfer->save();

                    $bank = Bank::where('account_no', $transfer->deposit_to_bank_no)->first();
                    if ($bank) {
                        $bank->balance = (float) $bank->balance + (float) $transfer->amount;
                        $bank->save();
                    }

                    if ($transfer->promotion_id != 0) {
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
                    ->line('BOT ' . env('APP_NAME'))
                    ->line('ทำรายการสำเร็จ โอนเครดิตเข้า ' . $member->username)
                    ->line('จำนวน :' . $amount)
                    ->line('Bonus :' . $bonus)
                    ->line($pro_name . ': ' . $message)
                    ->send();

                return response()->json(['message' => 'SMS request sent successfully.', 'txt' => 'Amount :' . $amount], 200);
            } else {
                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                    // ->content('Choose an option:')
                    ->line('BOT ' . env('APP_NAME'))
                    ->line('sms scbไม่พบรายการในช่วงเวลา')
                    ->line('จำนวน :' . $amount)
                    // ->button('View page', env('APP_URL'))
                    // ->button('View page',env('APP_URL'))
                    // ->keyboard('Button 1')
                    // ->keyboard('Button 2')
                    ->send();
                return response()->json(['message' => 'error'], 404);
            }
        } else {
            return response()->json(['message' => 'SMS Not valid.', 'txt' => 'Amount :' . $amount . ', Text3 : ' . $key], 200);
        }
    }

    public function getOTP($id)
    {
        Log::info('getOTP id: ' . $id);
        $transfer = Transfer::where('id', $id)->first();
        if ($transfer) {
            Log::info('otp ' . $transfer->otp);
            return response()->json([$transfer->otp], 200);
        }
    }

    public function getTranfer($id)
    {
        $transfer = Transfer::find($id);
        if ($transfer) {
            return response()->json([$transfer], 200);
        }
    }

    public function upDaterefNo(Request $request)
    {
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
        $slip_path = public_path() . '/slip/';
        if (!\File::exists($slip_path)) {
            \File::makeDirectory($slip_path, 0777, true);
        }

        Log::info("approvewithdraw " . $request->getContent());
        $member = Members::find($request->member_id);
        $transfer = Transfer::find($request->id);
        error_log($request->type . ' approve ' . $member->username . ' Balance =  ' . $member->wallet_balance . ' transfer amount =' . $transfer->amount);

        if ($transfer->status == 2 || $transfer->status == 3) {
            return response()->json(['message' => 'Transfer approved !!!'], 401);
        }


        $image = str_replace('data:image/png;base64,', '', $request->file);
        $image = str_replace(' ', '+', $image);
        $imageName = $request->id . '.' . 'png';
        \File::put(public_path() . '/slip/' . $imageName, base64_decode($image));
        $path = '/slip/' . $imageName;

        $transfer->old_balance = $member->wallet_balance;
        $old_balance = $member->wallet_balance;

        // $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Withdraw($member->username,floor($transfer->amount));
        // Log::info('Betflix Withdraw '.$bf_deposit.' '.floor($transfer->amount).' User =  '.$member->username);

        $bank = Bank::where('account_no', $transfer->deposit_to_bank_no)->first();
        if ($bank) {
            $bank->balance = (float) $bank->balance - (float) $transfer->amount;
            $bank->save();
        }

        $transfer->ref_id = $request->ref;
        $transfer->status = 2;
        $transfer->status_code = "อนุมัติ";
        $transfer->old_balance = $old_balance;
        $transfer->new_balance = $member->wallet_balance;
        $transfer->withdraw_slip = $path;
        $transfer->save();


        return response()->json([$transfer], 200);;
    }

    public function wrongdeposit_insert(Request $request)
    {
        $request->validate([
            'amount' => ['required'],
            'member_id' => ['required'],
            'bank_from_number' => ['required'],
            'bank_from_name' => ['required'],
            'bank_to' => ['required'],
            'image' => ['required'],
        ]);

        $member = Members::where('username', '=', $request->member_id)->first();
        if (!$member) {
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

        if ($request->image) {
            $fileName = rand() . '.' . $request->image->extension();
            $request->image->move(public_path('images/tranfer'), $fileName);
            $data->image = "/images/tranfer/" . $fileName;
        }

        $data->user_id = auth()->user()->id;
        $data->save();

        return redirect()->route('report.wrongdeposit')->with('status', 'success');
    }

    public function wrongdeposit_update(Request $request)
    {
        $request->validate([
            'amount' => ['required'],
            'member_id' => ['required'],
            'bank_from_number' => ['required'],
            'bank_from_name' => ['required'],
            'bank_to' => ['required'],
            'image' => ['required'],
        ]);

        $member = Members::where('username', '=', $request->member_id)->first();
        if (!$member) {
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

        if ($request->image) {
            $fileName = rand() . '.' . $request->image->extension();
            $request->image->move(public_path('images/tranfer'), $fileName);
            $data->image = "/images/tranfer/" . $fileName;
        }

        $data->user_id = auth()->user()->id;
        $data->save();

        return redirect()->route('report.wrongdeposit')->with('status', 'success');
    }

    public function turnover_on(Request $request)
    {
        $data = Transfer::find($request->id);
        $data->turnover_on = 0;
        $data->save();
        return redirect()->route('managemember.transaction')->with('status', 'success');
    }


    public function smsTest(Request $request)
    {

        $text = $text =  $_POST["text"];
        Log::info('SMS : ' . $text);
        try {

            $key = explode(' ', $request->sms)[4];
            if ($key == 'รับโอนจาก') {
                $amount = explode(' ', $request->sms)[6];
            } elseif ($key == 'เงินเข้า') {
                $key = 'รับโอนจาก';
                $amount = explode(' คงเหลือ', explode('เงินเข้า ', $request->sms)[1])[0];
            }
            $amount = str_replace(',', '', $amount);


            // return now()->subMinute(5);
            // return response()->json(["amount"=>$amount,"key"=>$key],200);
        } catch (\Exception $e) {
            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT ' . env('APP_NAME'))
                ->line('พบข้อผิดพลาดในการตรวจสอบ SMS')
                ->send();
            return response()->json(['message' => 'error'], 400);
        }

        if ($key == 'รับโอนจาก') {

            $transfer = Transfer::where('amount', $amount)->where('type', 'deposit')->whereTime('created_at', '>=', now()->subMinute($request->subMinute))->get();
            return response()->json(['transfer' => $transfer], 200);
        } else {
            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT ' . env('APP_NAME'))
                ->line('พบข้อผิดพลาดในการตรวจสอบ SMS Transfer')
                ->send();
            return response()->json(['message' => 'SMS Not valid.', 'txt' => 'Amount :' . $amount . ', Text3 : ' . $key], 200);
        }
    }

    public function trueCallback(Request $request)
    {
        $key = "86ad666d769cb1b2947f980c459c3aad";
        $data = json_decode($request->getContent());
        $header = explode('.', $data->message);
        $payload = base64_decode($header[1]);
        $payload = json_decode($payload);
        Log::info(json_encode($payload));


        $amount = number_format($payload->amount / 100, 2);


        $transfer = Transfer::where('amount', $amount)
            ->where('type', 'deposit')
            ->where('deposit_from_bank_no', $payload->sender_mobile)
            ->where('status', 1)
            ->whereTime('created_at', '>=', now()->subMinute(5))
            ->first();

        if ($transfer) {
            $member = Members::find($transfer->member_id);
            $amount_betflix = 0;
            $old_balance = $member->wallet_balance;

            $transfer->status = 2;
            $transfer->status_code = "BOT.อนุมัติ";
            $transfer->old_balance = $old_balance;

            $message = "";
            $pro_name = "";

            if ($transfer->promotion_id != 0) {
                error_log("promotion id = " . $transfer->promotion_id);
                if ($transfer->turnover_on == 1) {
                    error_log("turnover on = " . $transfer->turnover_on);
                    $pro = Promotion::find($transfer->promotion_id);
                    $pro_name = $pro->name;
                    $user_transfer = Transfer::where('member_id', $member->id)->where('status', 2)->where('type', 'deposit')->get();  /// เช็คฝากครั้งแรก
                    $user_transfer_count = $user_transfer->count();
                    error_log("user transfer count = " . $user_transfer_count);
                    error_log("Pro is_newuser = " . $pro->is_newuser);
                    if ($pro->is_newuser == 1) { //โปร member ใหม่
                        error_log("โปร member ใหม่");
                        if ($user_transfer_count == 0) {
                            /// ฝากครั้งแรก
                            error_log("เข้าเงื่อนไข member ใหม่");
                            $message .= "เข้าเงื่อนไข member ใหม่, ";
                            $bonus = $pro->bonus;
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                            $amount_betflix = $transfer->amount + $bonus;
                            $transfer->promotion = $pro->name;
                        } else {
                            error_log("ไม่เข้าเงื่อนไข member ใหม่");
                            $message .= "ไม่เข้าเงื่อนไข member ใหม่, ";
                            $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount;
                            $amount_betflix = $transfer->amount;
                        }
                    } else { //โปร member ทุกคน
                        error_log("โปร member ทุกคน");
                        $message .= "โปร member ทุกคน, ";
                        $bonus = $pro->bonus;
                        $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                        $amount_betflix = $transfer->amount + $bonus;
                        $transfer->promotion = $pro->name;
                    }
                } else {
                    $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                    $amount_betflix = $transfer->amount;
                }
            } else { //ไม่มีโปร
                error_log("ไม่มีโปร / ไม่กดรับโปร");
                $message .= "ไม่มีโปร / ไม่กดรับโปร, ";
                $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                $amount_betflix = $transfer->amount;
            }



            $bf_deposit =  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username, floor($amount_betflix));
            Log::info('Deposit Betflix ' . $bf_deposit . ' ' . floor($amount_betflix) . ' User =  ' . $member->username);

            if ($bf_deposit == "success") {

                $wheel_setting = WheelSpin::first();
                if ((float) $transfer->amount >= (float) $wheel_setting->ticket_condition) {
                    $total_spin = floor((float) $transfer->amount / (float) $wheel_setting->ticket_condition);
                    $member->remaining_spin = (float) $member->remaining_spin + (float) $total_spin;
                }

                $member->save();
                $transfer->new_balance = $member->wallet_balance;
                $transfer->save();

                if ($transfer->promotion_id != 0) {
                    PromotionUsed::create([
                        'member_id' => $member->id,
                        'promotion_id' => $transfer->promotion_id,
                        'promotion_name' => $pro->name,
                        'amount' => $bonus
                    ]);
                }
            }

            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT ' . env('APP_NAME'))
                ->line('ทำรายการสำเร็จ โอนเครดิตเข้า ' . $member->username)
                ->line($payload->event_type)
                ->line('จำนวน :' . $transfer->amount)
                ->line('Bonus :' . $bonus)
                ->line($pro_name . ': ' . $message)
                ->send();
            return response()->json(['message' => 'success'], 200);
        } else {
            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT ' . env('APP_NAME'))
                ->line('error Transfer not found.')
                ->line(json_encode($payload))
                ->send();
            return response()->json(['message' => 'error Transfer not found.'], 400);
        }
    }

    public function checkdeposit($id)
    {
        $transfer = Transfer::find($id);
        if ($transfer->status == 1) {
            return response()->json(['error']);
        } else if ($transfer->status == 2) {
            return response()->json(['success']);
        } else {
            return response()->json(['error']);
        }
    }
    public function checkdepositTMN($id)
    {
        // error_log('checkdepositTMN');

        $transfer = Transfer::find($id);
        if ($transfer->status == 1) {
            Log::info($transfer->amount);
            Log::info($transfer->deposit_from_bank_no);
            Log::info($transfer->deposit_from_bank_type);


            $transferAmount = '+' . $transfer->amount;
            $transferAccno = $this->getPhoneAttribute($transfer->deposit_from_bank_no);
            //    error_log($transferAccno);
            //    error_log($transferAmount);
            $tmn_transfer = app(\App\Http\Controllers\TMN_Controller::class)->lastTransactionHistory();
            // error_log(json_encode($tmn_transfer));
            Log::info($tmn_transfer);
            if ($tmn_transfer['type'] == 'p2p' || $tmn_transfer['type'] == 'p2pw') {
                if ($tmn_transfer['amount'] == $transferAmount || $tmn_transfer['transaction_reference_id'] == $transferAccno) {
                    $check_transfers = Transfer::where('ref_id', $tmn_transfer['report_id'])->first();
                    if ($check_transfers) {
                        return response()->json(['error']);
                    } else {
                        $approve = $this->approveDeposit($transfer);
                        if ($approve == 'success') {
                            $transfer->ref_id = $tmn_transfer['report_id'];
                            $transfer->save();

                            $truewallet = Bank::where('active', 1)->where('bank_name', 'TrueMoney Wallet')
                                ->where('account_no', $transfer->deposit_to_bank_no)->first();
                            if ($truewallet) {
                                $truewallet->balance = app(\App\Http\Controllers\TMN_Controller::class)->index();
                                $truewallet->save();
                            }

                            return response()->json(['success']);
                        } else {
                            return response()->json(['error']);
                        }
                    }
                } else {
                    return response()->json(['error']);
                }
            }
        } else {
            return response()->json(['error']);
        }
    }

    static function approveDeposit($transfer)
    {
        $member = Members::find($transfer->member_id);
        $amount_betflix = 0;
        $bonus = 0;
        $old_balance = $member->wallet_balance;

        $transfer->status = 2;
        $transfer->status_code = "BOT.อนุมัติ";
        $transfer->old_balance = $old_balance;

        $message = "";
        $pro_name = "";

        if ($transfer->promotion_id != 0) {
            error_log("promotion id = " . $transfer->promotion_id);
            if ($transfer->turnover_on == 1) {
                error_log("turnover on = " . $transfer->turnover_on);
                $pro = Promotion::find($transfer->promotion_id);
                $pro_name = $pro->name;
                $user_transfer = Transfer::where('member_id', $member->id)->where('status', 2)->where('type', 'deposit')->get();  /// เช็คฝากครั้งแรก
                $user_transfer_count = $user_transfer->count();
                error_log("user transfer count = " . $user_transfer_count);
                error_log("Pro is_newuser = " . $pro->is_newuser);
                if ($pro->is_newuser == 1) { //โปร member ใหม่
                    error_log("โปร member ใหม่");
                    if ($user_transfer_count == 0) {
                        /// ฝากครั้งแรก
                        error_log("เข้าเงื่อนไข member ใหม่");
                        $message .= "เข้าเงื่อนไข member ใหม่, ";
                        $bonus = $pro->bonus;
                        $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                        $amount_betflix = $transfer->amount + $bonus;
                        $transfer->promotion = $pro->name;
                    } else {
                        error_log("ไม่เข้าเงื่อนไข member ใหม่");
                        $message .= "ไม่เข้าเงื่อนไข member ใหม่, ";
                        $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount;
                        $amount_betflix = $transfer->amount;
                    }
                } else { //โปร member ทุกคน
                    error_log("โปร member ทุกคน");
                    $message .= "โปร member ทุกคน, ";
                    $bonus = $pro->bonus;
                    $member->wallet_balance =  (float) $member->wallet_balance + $transfer->amount + $bonus;
                    $amount_betflix = $transfer->amount + $bonus;
                    $transfer->promotion = $pro->name;
                }
            } else {
                $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
                $amount_betflix = $transfer->amount;
            }
        } else { //ไม่มีโปร
            error_log("ไม่มีโปร / ไม่กดรับโปร");
            $message .= "ไม่มีโปร / ไม่กดรับโปร, ";
            $member->wallet_balance = (float) $member->wallet_balance +  (float) $transfer->amount;
            $amount_betflix = $transfer->amount;
        }



        $bf_deposit =  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username, floor($amount_betflix));
        Log::info('Deposit Betflix ' . $bf_deposit . ' amount : ' . floor($amount_betflix) . ' User =  ' . $member->username);

        if ($bf_deposit == "success") {

            $wheel_setting = WheelSpin::first();
            if ((float) $transfer->amount >= (float) $wheel_setting->ticket_condition) {
                $total_spin = floor((float) $transfer->amount / (float) $wheel_setting->ticket_condition);
                $member->remaining_spin = (float) $member->remaining_spin + (float) $total_spin;
            }

            $member->save();
            $transfer->new_balance = $member->wallet_balance;
            $transfer->save();

            $bank = Bank::where('account_no', $transfer->deposit_to_bank_no)->first();
            if ($bank) {
                $bank->balance = (float) $bank->balance + (float) $transfer->amount;
                $bank->save();
            }

            if ($transfer->promotion_id != 0) {
                PromotionUsed::create([
                    'member_id' => $member->id,
                    'promotion_id' => $transfer->promotion_id,
                    'promotion_name' => $pro->name,
                    'amount' => $bonus
                ]);
            }
            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT ' . env('APP_NAME'))
                ->line('ทำรายการสำเร็จ โอนเครดิตเข้า ' . $member->username)
                ->line('จำนวน :' . $transfer->amount)
                ->line('Bonus :' . $bonus)
                ->line($pro_name . ': ' . $message)
                ->send();

            return 'success';
        } else {
            return 'error';
        }
    }
    public function getPhoneAttribute($phone)
    {
        $phone = preg_replace("/[^0-9]/", "", $phone);
        return substr($phone, 0, 3) . "-" . substr($phone, 3, 3) . "-" . substr($phone, 6, 4);
    }
}
