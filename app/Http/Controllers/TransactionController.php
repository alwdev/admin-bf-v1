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
        error_log("lineNotify_tranfer amount = " . $request->amount);
        error_log("lineNotify_tranfer acc_no = " . $request->acc_no);


        $transfer = Transfer::where('amount', $request->amount)
            ->where('deposit_from_bank_no', 'like', '%' . $request->acc_no)
            ->where('type', 'deposit')
            ->where('status', 1)->first();

        if ($transfer) {

            $do_transfer = $this->lineNotify_deposit($transfer->id);
            error_log("lineNotify_tranfer do_transfer = " . $do_transfer);
            return  $do_transfer;
        } else {
            error_log("lineNotify_tranfer transfer not found");
            $recheck_transfer = Transfer::where('amount', $request->amount)
                ->where('type', 'deposit')
                ->where('status', 1)->first();
            if ($recheck_transfer) {
                $lastFourCharacters = substr($recheck_transfer->deposit_from_bank_no, -4);
                error_log("recheck_transfer lastFourCharacters deposit_from_bank_no = " . $lastFourCharacters);
                if ($lastFourCharacters == $request->acc_no) {

                    $do_transfer = $this->lineNotify_deposit($recheck_transfer->id);
                    error_log("lineNotify_tranfer do_transfer = " . $do_transfer);
                    return  $do_transfer;
                } else {
                    error_log("lineNotify_tranfer acc_no not match");
                    $member = Members::find($recheck_transfer->member_id);
                    TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                        ->line('LINE-BOT ' . env('APP_NAME'))
                        ->line('เลขบัญชีผู้โอนเงินไม่ตรงกับเลขบัญชีที่แจ้งไว้')
                        ->line('User : ' . $member->username)
                        ->line("amount = " . $request->amount)
                        ->line("acc_no = " . $request->acc_no)
                        ->line("transfer acc_no = " . $lastFourCharacters)
                        ->send();
                    return 404;
                }
            } else {

                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                    ->line('LINE-BOT ' . env('APP_NAME'))
                    ->line('ไม่พบรายการโอนเงินในระบบ')
                    ->line("amount = " . $request->amount)
                    ->line("acc_no = " . $request->acc_no)
                    ->send();
                return 404;
            }

            return 404;
        }
    }

    public function lineNotify_deposit($id)
    {
        error_log("lineNotify_deposit id = " . $id);
        $transfer = Transfer::where('id', $id)->first();

        if (!$transfer) {
            error_log("lineNotify_deposit: Transfer not found for ID: " . $id);
            return 404; // หรือ return response ที่เหมาะสม
        }

        error_log("lineNotify_deposit transfer found id = " . $transfer->id);
        $member = Members::find($transfer->member_id);

        if (!$member) {
            error_log("lineNotify_deposit: Member not found for transfer ID: " . $transfer->id);
            return 404; // หรือ return response ที่เหมาะสม
        }

        $amount_betflix = 0;
        $old_balance = (float) $member->wallet_balance; // เก็บยอดเงินเก่าก่อนคำนวณ

        // ตั้งค่าสถานะ Transfer เบื้องต้น (จะถูกบันทึกเมื่อ Betflix สำเร็จ)
        $transfer->status = 2;
        $transfer->status_code = "BOT.อนุมัติ";
        $transfer->old_balance = $old_balance;

        $message = ""; // ใช้สำหรับเก็บข้อความ log/แจ้งเตือน
        $bonus = 0.0; // ตั้งค่าเริ่มต้นสำหรับ bonus ที่จะใช้ใน log/telegram (จะถูกอัปเดตภายหลัง)

        // ** กำหนดค่าเริ่มต้นสำหรับผลลัพธ์การคำนวณโบนัสและ turnover **
        $bonus_to_apply = 0.0;
        $calculated_required_turnover = 0.0; // จะเก็บยอด turnover ที่ต้องทำจริง (บาท)
        $applied_promotion_name = "ไม่มีโปรโมชั่น"; // Default value
        $promotion_found_and_applied = false; // Flag เพื่อติดตามว่าได้มีการใช้โปรโมชั่นหรือไม่

        // ดึงข้อมูลโปรโมชั่นต่อเนื่องทั้งหมดที่ active และเป็นของ store_id นี้
        // และเป็นโปรโมชั่นสำหรับสมาชิกใหม่แบบต่อเนื่อง
        $recurring_new_user_promotions = Promotion::where('enable', 1)
            ->where('active', 1)
            ->where('is_newuser', 1)
            ->where('is_recurring_promotion', 1)
            ->get();

        // ตรวจสอบจำนวนการฝากของสมาชิก (เพื่อดูว่าเป็นการฝากครั้งแรกหรือไม่)
        $user_transfer_count = Transfer::where('member_id', $member->id)
            ->where('status', 2)
            ->where('type', 'deposit')
            ->count();
        error_log("user transfer count = " . $user_transfer_count);


        // --------------------------------------------------------------------------------------
        // *** Logic สำหรับโบนัสต่อเนื่องสำหรับสมาชิกใหม่ (ไม่ต้องรับโปรเข้ามา) ***
        // --------------------------------------------------------------------------------------
        // เงื่อนไข: ต้องไม่ใช่การฝากครั้งแรก (user_transfer_count > 0) และมีวันที่สมัคร
        if ($user_transfer_count > 0 && $member->created_at) {
            $registrationDate = Carbon::parse($member->created_at);
            $now = Carbon::now();

            foreach ($recurring_new_user_promotions as $pro_recurring) {
                $promotionEndDate = $registrationDate->copy()->addDays($pro_recurring->recurring_promotion_days);

                // ถ้ายังอยู่ในช่วงเวลาโปรโมชั่นต่อเนื่อง
                if ($now->lt($promotionEndDate)) {
                    error_log("Auto-applying recurring new member promotion: " . $pro_recurring->name);
                    $message .= "Auto recurring promo applied, ";

                    // คำนวณโบนัสจาก recurring_bonus_percentage
                    if ($pro_recurring->recurring_bonus_percentage !== null && $pro_recurring->recurring_bonus_percentage > 0) {
                        $bonus_to_apply = $transfer->amount * ($pro_recurring->recurring_bonus_percentage / 100);
                        error_log("Recurring Bonus Percentage: {$pro_recurring->recurring_bonus_percentage}%, Calculated Recurring Bonus: {$bonus_to_apply}");
                    } else {
                        error_log("Recurring bonus percentage is null or zero for auto-applied promo.");
                        $bonus_to_apply = 0.0;
                    }

                    // กำหนด turnover สำหรับโปรต่อเนื่อง - ใช้แค่ recurring_turnover_percentage เท่านั้น
                    $base_amount_for_turnover = $transfer->amount + $bonus_to_apply;

                    if ($pro_recurring->recurring_turnover_percentage !== null && $pro_recurring->recurring_turnover_percentage > 0) {
                        $calculated_required_turnover = $base_amount_for_turnover * ($pro_recurring->recurring_turnover_percentage / 100);
                        error_log("Recurring Turnover (Percentage Only): {$pro_recurring->recurring_turnover_percentage}%, Calculated Turnover Amount: {$calculated_required_turnover}");
                    } else {
                        $calculated_required_turnover = 0.0;
                        error_log("No recurring turnover percentage defined or is zero for auto-applied promo.");
                    }

                    $applied_promotion_name = $pro_recurring->name . " (Recurring)";
                    $promotion_found_and_applied = true; // ตั้งค่า flag ว่าได้ใช้โปรโมชั่นแล้ว
                    $bonus = $bonus_to_apply; // อัปเดตตัวแปร $bonus สำหรับ Telegram log
                    break; // เจอโปรต่อเนื่องที่เข้าเงื่อนไขแล้ว ออกจาก loop
                }
            }
        }


        // --------------------------------------------------------------------------------------
        // *** Logic สำหรับโปรโมชั่นที่ลูกค้าเลือก (promotion_id != 0) หรือ โปรโมชั่นแรกของสมาชิกใหม่ ***
        // *** จะทำงานก็ต่อเมื่อยังไม่มีโปรโมชั่นต่อเนื่องถูก apply อัตโนมัติ ***
        // --------------------------------------------------------------------------------------
        if (!$promotion_found_and_applied) { // ถ้ายังไม่มีโปรโมชั่นใดๆ ถูก apply
            if ($transfer->promotion_id != 0) {
                error_log("promotion id = " . $transfer->promotion_id);
                // ดึงโปรโมชั่นที่ผู้ใช้เลือก
                $pro = Promotion::find($transfer->promotion_id);

                if (!$pro) {
                    error_log("Promotion not found for ID: " . $transfer->promotion_id);
                    $message .= "Promotion not found, ";
                    // ในกรณีนี้จะไม่มีโบนัสจากโปรโมชั่นที่เลือก
                } else {
                    error_log("Pro is_newuser = " . $pro->is_newuser);
                    error_log("Pro is_percentage_based = " . $pro->is_percentage_based);
                    error_log("turnover on = " . $transfer->turnover_on);

                    if ($transfer->turnover_on == 1) {
                        // *** คำนวณโบนัสและ turnover ตามประเภทโปรโมชั่นที่เลือก ***
                        $current_calculated_bonus = 0.0;
                        $current_turnover_value = 0.0; // เก็บค่าจากโปรโมชั่นก่อนคำนวณเป็นยอดจริง (จะเป็นเปอร์เซ็นต์หรือเท่า)

                        if ($pro->is_percentage_based) { // ถ้าโปรโมชั่นนี้ใช้ระบบเปอร์เซ็นต์
                            error_log("Calculating bonus based on percentage (selected promo).");
                            if ($transfer->amount >= $pro->deposit) {
                                if ($pro->bonus_percentage !== null && $pro->bonus_percentage > 0) {
                                    $current_calculated_bonus = $transfer->amount * ($pro->bonus_percentage / 100);
                                }
                                if ($pro->turnover_percentage !== null && $pro->turnover_percentage > 0) {
                                    $current_turnover_value = $pro->turnover_percentage;
                                }
                            } else {
                                $message .= "Does not meet Deposit amount requirements, ";
                            }
                        } else { // ถ้าโปรโมชั่นนี้ใช้ระบบค่าคงที่ (จำนวนเงิน/เท่า)
                            error_log("Calculating bonus based on fixed amount (selected promo).");
                            if ($transfer->amount >= $pro->deposit) {
                                $current_calculated_bonus = $pro->bonus;
                                $current_turnover_value = $pro->turnover;
                            } else {
                                $message .= "Does not meet Deposit amount requirements, ";
                            }
                        }
                        // *** จบการกำหนดค่าโบนัสและ turnover สำหรับโปรที่เลือก ***

                        if ($pro->is_newuser == 1) { // โปรโมชั่นแรกสำหรับสมาชิกใหม่ที่เลือก
                            error_log("เป็นโปรโมชั่นแรกสำหรับสมาชิกใหม่ (เลือก)");
                            if ($user_transfer_count == 0) { // ต้องเป็นการฝากครั้งแรกจริงๆ
                                if ($transfer->amount >= $pro->deposit) {
                                    error_log("Meet first-time new member conditions (selected promo)");
                                    $message .= "Meet first-time new member conditions, ";

                                    // กำหนดค่าโบนัสและเทิร์นโอเวอร์

                                    $bonus_to_apply = $current_calculated_bonus;
                                    $bonus = $bonus_to_apply; // อัปเดตตัวแปร $bonus สำหรับ Telegram log

                                    $base_amount_for_turnover = $transfer->amount + $bonus_to_apply;
                                    if ($pro->is_percentage_based) {
                                        $calculated_required_turnover = $base_amount_for_turnover * ($current_turnover_value / 100);
                                    } else {
                                        $calculated_required_turnover = $base_amount_for_turnover * $current_turnover_value;
                                    }

                                    $applied_promotion_name = $pro->name;
                                    $promotion_found_and_applied = true;
                                } else {
                                    $message .= "Does not meet Deposit amount requirements, ";
                                }
                            } else {
                                error_log("Does not meet first-time new member requirements (already made first deposit), no bonus from selected promo.");
                                $message .= "Does not meet first-time new member requirements, ";
                                // ไม่เข้าเงื่อนไข (ไม่ใช่ครั้งแรก), ไม่มีโบนัสจากโปรนี้
                                // $bonus_to_apply และ $calculated_required_turnover จะยังคงเป็น 0.0 ตามค่าเริ่มต้น
                            }
                        } else { // โปรโมชั่นสำหรับสมาชิกทุกคน (เลือก)
                            if ($transfer->amount >= $pro->deposit) {
                                error_log("All member promotions (selected promo)");
                                $message .= "All member promotions, ";

                                // กำหนดค่าโบนัสและเทิร์นโอเวอร์
                                $bonus_to_apply = $current_calculated_bonus;
                                $bonus = $bonus_to_apply; // อัปเดตตัวแปร $bonus สำหรับ Telegram log

                                $base_amount_for_turnover = $transfer->amount + $bonus_to_apply;
                                if ($pro->is_percentage_based) {
                                    $calculated_required_turnover = $base_amount_for_turnover * ($current_turnover_value / 100);
                                } else {
                                    $calculated_required_turnover = $base_amount_for_turnover * $current_turnover_value;
                                }

                                $applied_promotion_name = $pro->name;
                                $promotion_found_and_applied = true;
                            } else {
                                $message .= "Does not meet Deposit amount requirements, ";
                            }
                        }
                    } else { // turnover_on == 0 สำหรับโปรโมชั่นที่เลือก
                        error_log("Turnover off for selected promotion. No bonus from this promo.");
                        $message .= "Turnover off for selected promo, ";
                        // ในกรณีนี้จะไม่มีโบนัสจากโปรโมชั่นที่เลือก แต่ยอดฝากจะยังเข้า
                        // $bonus_to_apply และ $calculated_required_turnover จะยังคงเป็น 0.0 ตามค่าเริ่มต้น
                    }
                }
            } else { // promotion_id == 0 (ไม่ได้เลือกโปรโมชั่น)
                error_log("No promotion selected.");
                $message .= "No promotion selected, ";
                // ไม่มีโปรโมชั่นที่ถูกเลือก ไม่มีโบนัส ไม่มี turnover
                // $bonus_to_apply และ $calculated_required_turnover จะยังคงเป็น 0.0 ตามค่าเริ่มต้น
            }
        }


        // --------------------------------------------------------------------------------------
        // *** สรุปผลลัพธ์และอัปเดต Wallet / Transfer ***
        // --------------------------------------------------------------------------------------

        // ถ้ามีโปรโมชั่นถูก apply (ไม่ว่าจะ auto หรือเลือก)
        if ($promotion_found_and_applied) {
            error_log("Applying bonus: {$bonus_to_apply} with total required turnover: {$calculated_required_turnover}");
            $member->wallet_balance = (float) $member->wallet_balance + $transfer->amount + $bonus_to_apply;
            $amount_betflix = $transfer->amount + $bonus_to_apply;
            $transfer->promotion = $applied_promotion_name;
            // *** NEW: บันทึกยอด Turnover ที่ต้องทำจริง ***
            // $transfer->required_turnover_amount = $calculated_required_turnover; // สมมติว่ามี column นี้ในตาราง transfers
            // $transfer->bonus_applied = $bonus_to_apply; // บันทึกโบนัสที่ให้ด้วย

            $transfer->turnover_on = 1;

            // เพิ่มการบันทึก promotion_id ที่ถูกใช้ (ถ้ามีใน $pro)
            $promotion_id_used_for_transfer = null;
            if (isset($pro) && $pro instanceof Promotion) {
                $promotion_id_used_for_transfer = $pro->id;
            } elseif (isset($pro_recurring) && $pro_recurring instanceof Promotion) {
                $promotion_id_used_for_transfer = $pro_recurring->id;
            }
            $transfer->promotion_id = $promotion_id_used_for_transfer;

            $bonus = $bonus_to_apply; // อัปเดตตัวแปร $bonus สำหรับ Telegram log

        } else { // ไม่มีโปรโมชั่นใดๆ เข้าเงื่อนไข หรือไม่ถูกเลือก
            error_log("No applicable promotion found or selected. Only deposit amount will be added.");
            $message .= "No applicable promo, ";
            $member->wallet_balance = (float) $member->wallet_balance + (float) $transfer->amount;
            $amount_betflix = $transfer->amount;
            $transfer->promotion = "ไม่มีโปรโมชั่น"; // หรือค่า default อื่นๆ
            // $transfer->required_turnover_amount = 0.0; // ไม่มีโปรโมชั่นก็ไม่มีเทิร์น
            // $transfer->bonus_applied = 0.0;
            $transfer->promotion_id = 0; // ไม่มีโปรโมชั่นก็เป็น null
        }

        error_log("Bonus for Telegram = " . $bonus); // ตัวแปร $bonus นี้จะถูกใช้ใน Telegram

        $bf_deposit = app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username, ($amount_betflix));

        $log = new \App\Models\Logs;
        $log->log = 'Deposit Betflix ' . $bf_deposit . ' ' . ($amount_betflix) . ' User = ' . $member->username . ' Bonus =' . $bonus;
        $log->save();

        // $bf_deposit = "success";
        if ($bf_deposit == "success") {
            error_log("lineNotify_deposit bf_deposit success");

            $wheel_setting = WheelSpin::first();
            if ($wheel_setting && $wheel_setting->ticket_condition > 0) {
                if ((float) $transfer->amount >= (float) $wheel_setting->ticket_condition) {
                    $total_spin = floor((float) $transfer->amount / (float) $wheel_setting->ticket_condition);
                    $member->remaining_spin = (float) $member->remaining_spin + (float) $total_spin;
                }
            }

            $member->save();
            $transfer->new_balance = $member->wallet_balance;
            // $transfer->status และ $transfer->old_balance ถูกตั้งค่าไว้ด้านบนแล้ว
            $transfer->save();

            // **หมายเหตุ:** ส่วนนี้ (`$bank->balance = ...`) ดูเหมือนจะซ้ำซ้อน
            // หากยอดเงินเข้าบัญชีธนาคารถูกบันทึกไปแล้วเมื่อมีการรับเงิน
            // แต่ถ้า Logic ของคุณคือการอัปเดตยอดเงินในบัญชีธนาคารของระบบเมื่อเงินถูกโอนไปให้ Betflix
            // ก็สามารถเก็บไว้ได้ แต่ควรพิจารณาความถูกต้องของ Flow
            $bank = Bank::where('account_no', $transfer->deposit_to_bank_no)->first();
            if ($bank) {
                // หากคุณเคยเพิ่ม $transfer->amount เข้า bank->balance ในบล็อก success
                // ก็ควรหักออกในบล็อก failure นี้
                $bank->balance = (float) $bank->balance + (float) $transfer->amount;
                $bank->save();
            }

            // บันทึก PromotionUsed ก็ต่อเมื่อมีการใช้โปรโมชั่นจริง
            if ($promotion_found_and_applied) {
                // ดึง promotion_id และ promotion_name ที่ถูกใช้จริง
                $promotion_id_to_log = null;
                $promotion_name_to_log = $applied_promotion_name;

                if (isset($pro) && $pro instanceof Promotion) {
                    $promotion_id_to_log = $pro->id;
                } elseif (isset($pro_recurring) && $pro_recurring instanceof Promotion) {
                    $promotion_id_to_log = $pro_recurring->id;
                }

                if ($promotion_id_to_log) { // ตรวจสอบว่ามี promotion_id ที่จะบันทึก
                    PromotionUsed::create([
                        'member_id' => $member->id,
                        'promotion_id' => $promotion_id_to_log,
                        'promotion_name' => $promotion_name_to_log,
                        'amount' => $bonus_to_apply, // ใช้ $bonus_to_apply ที่คำนวณได้
                    ]);
                }
            }
            $bonus = $bonus_to_apply; // อัปเดตตัวแปร $bonus สำหรับ Telegram log
            try {
                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                    ->line('BOT-LINE ' . env('APP_NAME'))
                    ->line('Transaction completed, credit transferred ' . $member->username)
                    ->line('Amount :' . $transfer->amount)
                    ->line('Bonus :' . $bonus) // ใช้ floor() กับ bonus ด้วย
                    ->line('Promotion : ' . $applied_promotion_name) // แสดงชื่อโปรโมชั่นที่ถูกใช้
                    ->line('Message : ' . $message) // แสดง message จาก logic
                    ->send();
            } catch (\Exception $e) {
                error_log("Error sending Telegram message (success path): " . $e->getMessage());
            }
            return 200;
        } else {
            error_log("lineNotify_deposit bf_deposit failed for user: " . $member->username . " with response: " . $bf_deposit);
            // กรณี Betflix Deposit ไม่สำเร็จ ควร Rollback Bank Balance และ Member Wallet Balance ด้วย
            // เนื่องจากยอดเงินถูกเพิ่มเข้า wallet_balance แล้ว
            $member->wallet_balance = $old_balance; // คืนยอดเงินใน wallet ของสมาชิก
            $member->save();

            // **หมายเหตุ:** การคืนเงินเข้าบัญชีธนาคารของระบบ (Bank)
            // ควรพิจารณาว่ายอดเงินนี้ถูกเพิ่มเข้าไปใน Bank Balance ตอนไหน
            // หากถูกเพิ่มไปแล้วตอนรับเงิน (ก่อนเรียกฟังก์ชันนี้) ก็ไม่ควรเพิ่มซ้ำ
            // หากถูกเพิ่มในฟังก์ชันนี้ (ซึ่งไม่น่าเป็นไปได้) ก็ต้องหักออก
            // แต่จากโค้ดเดิมของคุณใน if($bf_deposit == "success") มีการเพิ่ม Bank Balance
            // ดังนั้นในกรณีที่ล้มเหลว ก็ควรหักออก (หากคุณต้องการให้ Bank Balance สะท้อนยอดเงินที่โอนไป Betflix)
            $bank = Bank::where('account_no', $transfer->deposit_to_bank_no)->first();
            if ($bank) {
                // หากคุณเคยเพิ่ม $transfer->amount เข้า bank->balance ในบล็อก success
                // ก็ควรหักออกในบล็อก failure นี้
                // $bank->balance = (float) $bank->balance - (float) $transfer->amount;
                // $bank->save();
            }

            // อัปเดตสถานะ Transfer เป็น Failed
            $transfer->status = 3; // หรือสถานะสำหรับ Failed
            $transfer->status_code = "BOT.ไม่สำเร็จ: " . $bf_deposit;
            $transfer->new_balance = $member->wallet_balance; // ยอดเงินใหม่ควรเป็นยอดเงินเก่าที่ rollback แล้ว
            $transfer->save();

            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT-LINE ' . env('APP_NAME'))
                ->line('Deposit Betflix failed for user ' . $member->username)
                ->line('Amount :' . $transfer->amount)
                ->line('Response :' . $bf_deposit)
                ->send();
            return 500;
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
        $data->is_turnover_cleared = 1;
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
        // Log::info('Deposit Betflix ' . $bf_deposit . ' amount : ' . floor($amount_betflix) . ' User =  ' . $member->username);

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

    public function crypto_deposit($id, $cryptoAmount)
    {
        error_log("crypto_deposit id = " . $id);
        $transfer = Transfer::where('id', $id)->first();

        if (!$transfer) {
            error_log("crypto_deposit: Transfer not found for ID: " . $id);
            return 404; // หรือ return response ที่เหมาะสม
        }

        error_log("crypto_deposit transfer found id = " . $transfer->id);
        $member = Members::find($transfer->member_id);

        if (!$member) {
            error_log("crypto_deposit: Member not found for transfer ID: " . $transfer->id);
            return 404; // หรือ return response ที่เหมาะสม
        }

        $amount_betflix = 0;
        $old_balance = (float) $member->wallet_balance; // เก็บยอดเงินเก่าก่อนคำนวณ

        // ตั้งค่าสถานะ Transfer เบื้องต้น (จะถูกบันทึกเมื่อ Betflix สำเร็จ)
        $transfer->status = 2;
        $transfer->status_code = "BOT.อนุมัติ";
        $transfer->old_balance = $old_balance;

        $message = ""; // ใช้สำหรับเก็บข้อความ log/แจ้งเตือน
        $bonus = 0.0; // ตั้งค่าเริ่มต้นสำหรับ bonus ที่จะใช้ใน log/telegram (จะถูกอัปเดตภายหลัง)

        // ** กำหนดค่าเริ่มต้นสำหรับผลลัพธ์การคำนวณโบนัสและ turnover **
        $bonus_to_apply = 0.0;
        $calculated_required_turnover = 0.0; // จะเก็บยอด turnover ที่ต้องทำจริง (บาท)
        $applied_promotion_name = "ไม่มีโปรโมชั่น"; // Default value
        $promotion_found_and_applied = false; // Flag เพื่อติดตามว่าได้มีการใช้โปรโมชั่นหรือไม่

        // ดึงข้อมูลโปรโมชั่นต่อเนื่องทั้งหมดที่ active และเป็นของ store_id นี้
        // และเป็นโปรโมชั่นสำหรับสมาชิกใหม่แบบต่อเนื่อง
        $recurring_new_user_promotions = Promotion::where('enable', 1)
            ->where('active', 1)
            ->where('is_newuser', 1)
            ->where('is_recurring_promotion', 1)
            ->get();

        // ตรวจสอบจำนวนการฝากของสมาชิก (เพื่อดูว่าเป็นการฝากครั้งแรกหรือไม่)
        $user_transfer_count = Transfer::where('member_id', $member->id)
            ->where('status', 2)
            ->where('type', 'deposit')
            ->count();
        error_log("user transfer count = " . $user_transfer_count);


        // --------------------------------------------------------------------------------------
        // *** Logic สำหรับโบนัสต่อเนื่องสำหรับสมาชิกใหม่ (ไม่ต้องรับโปรเข้ามา) ***
        // --------------------------------------------------------------------------------------
        // เงื่อนไข: ต้องไม่ใช่การฝากครั้งแรก (user_transfer_count > 0) และมีวันที่สมัคร
        if ($user_transfer_count > 0 && $member->created_at) {
            $registrationDate = Carbon::parse($member->created_at);
            $now = Carbon::now();

            foreach ($recurring_new_user_promotions as $pro_recurring) {
                $promotionEndDate = $registrationDate->copy()->addDays($pro_recurring->recurring_promotion_days);

                // ถ้ายังอยู่ในช่วงเวลาโปรโมชั่นต่อเนื่อง
                if ($now->lt($promotionEndDate)) {
                    error_log("Auto-applying recurring new member promotion: " . $pro_recurring->name);
                    $message .= "Auto recurring promo applied, ";

                    // คำนวณโบนัสจาก recurring_bonus_percentage
                    if ($pro_recurring->recurring_bonus_percentage !== null && $pro_recurring->recurring_bonus_percentage > 0) {
                        $bonus_to_apply = $cryptoAmount * ($pro_recurring->recurring_bonus_percentage / 100);
                        error_log("Recurring Bonus Percentage: {$pro_recurring->recurring_bonus_percentage}%, Calculated Recurring Bonus: {$bonus_to_apply}");
                    } else {
                        error_log("Recurring bonus percentage is null or zero for auto-applied promo.");
                        $bonus_to_apply = 0.0;
                    }

                    // กำหนด turnover สำหรับโปรต่อเนื่อง - ใช้แค่ recurring_turnover_percentage เท่านั้น
                    $base_amount_for_turnover = $cryptoAmount + $bonus_to_apply;

                    if ($pro_recurring->recurring_turnover_percentage !== null && $pro_recurring->recurring_turnover_percentage > 0) {
                        $calculated_required_turnover = $base_amount_for_turnover * ($pro_recurring->recurring_turnover_percentage / 100);
                        error_log("Recurring Turnover (Percentage Only): {$pro_recurring->recurring_turnover_percentage}%, Calculated Turnover Amount: {$calculated_required_turnover}");
                    } else {
                        $calculated_required_turnover = 0.0;
                        error_log("No recurring turnover percentage defined or is zero for auto-applied promo.");
                    }

                    $applied_promotion_name = $pro_recurring->name . " (Recurring)";
                    $promotion_found_and_applied = true; // ตั้งค่า flag ว่าได้ใช้โปรโมชั่นแล้ว
                    $bonus = $bonus_to_apply; // อัปเดตตัวแปร $bonus สำหรับ Telegram log
                    break; // เจอโปรต่อเนื่องที่เข้าเงื่อนไขแล้ว ออกจาก loop
                }
            }
        }


        // --------------------------------------------------------------------------------------
        // *** Logic สำหรับโปรโมชั่นที่ลูกค้าเลือก (promotion_id != 0) หรือ โปรโมชั่นแรกของสมาชิกใหม่ ***
        // *** จะทำงานก็ต่อเมื่อยังไม่มีโปรโมชั่นต่อเนื่องถูก apply อัตโนมัติ ***
        // --------------------------------------------------------------------------------------
        if (!$promotion_found_and_applied) { // ถ้ายังไม่มีโปรโมชั่นใดๆ ถูก apply
            if ($transfer->promotion_id != 0) {
                error_log("promotion id = " . $transfer->promotion_id);
                // ดึงโปรโมชั่นที่ผู้ใช้เลือก
                $pro = Promotion::find($transfer->promotion_id);

                if (!$pro) {
                    error_log("Promotion not found for ID: " . $transfer->promotion_id);
                    $message .= "Promotion not found, ";
                    // ในกรณีนี้จะไม่มีโบนัสจากโปรโมชั่นที่เลือก
                } else {
                    error_log("Pro is_newuser = " . $pro->is_newuser);
                    error_log("Pro is_percentage_based = " . $pro->is_percentage_based);
                    error_log("turnover on = " . $transfer->turnover_on);

                    if ($transfer->turnover_on == 1) {
                        // *** คำนวณโบนัสและ turnover ตามประเภทโปรโมชั่นที่เลือก ***
                        $current_calculated_bonus = 0.0;
                        $current_turnover_value = 0.0; // เก็บค่าจากโปรโมชั่นก่อนคำนวณเป็นยอดจริง (จะเป็นเปอร์เซ็นต์หรือเท่า)

                        if ($pro->is_percentage_based) { // ถ้าโปรโมชั่นนี้ใช้ระบบเปอร์เซ็นต์
                            error_log("Calculating bonus based on percentage (selected promo).");
                            if ($pro->bonus_percentage !== null && $pro->bonus_percentage > 0) {
                                $current_calculated_bonus = $cryptoAmount * ($pro->bonus_percentage / 100);
                            }
                            if ($pro->turnover_percentage !== null && $pro->turnover_percentage > 0) {
                                $current_turnover_value = $pro->turnover_percentage;
                            }
                        } else { // ถ้าโปรโมชั่นนี้ใช้ระบบค่าคงที่ (จำนวนเงิน/เท่า)
                            error_log("Calculating bonus based on fixed amount (selected promo).");
                            $current_calculated_bonus = $pro->bonus;
                            $current_turnover_value = $pro->turnover;
                        }
                        // *** จบการกำหนดค่าโบนัสและ turnover สำหรับโปรที่เลือก ***

                        if ($pro->is_newuser == 1) { // โปรโมชั่นแรกสำหรับสมาชิกใหม่ที่เลือก
                            error_log("เป็นโปรโมชั่นแรกสำหรับสมาชิกใหม่ (เลือก)");
                            if ($user_transfer_count == 0) { // ต้องเป็นการฝากครั้งแรกจริงๆ
                                error_log("Meet first-time new member conditions (selected promo)");
                                $message .= "Meet first-time new member conditions, ";

                                // กำหนดค่าโบนัสและเทิร์นโอเวอร์
                                $bonus_to_apply = $current_calculated_bonus;
                                $bonus = $bonus_to_apply; // อัปเดตตัวแปร $bonus สำหรับ Telegram log

                                $base_amount_for_turnover = $cryptoAmount + $bonus_to_apply;
                                if ($pro->is_percentage_based) {
                                    $calculated_required_turnover = $base_amount_for_turnover * ($current_turnover_value / 100);
                                } else {
                                    $calculated_required_turnover = $base_amount_for_turnover * $current_turnover_value;
                                }

                                $applied_promotion_name = $pro->name;
                                $promotion_found_and_applied = true;
                            } else {
                                error_log("Does not meet first-time new member requirements (already made first deposit), no bonus from selected promo.");
                                $message .= "Does not meet first-time new member requirements, ";
                                // ไม่เข้าเงื่อนไข (ไม่ใช่ครั้งแรก), ไม่มีโบนัสจากโปรนี้
                                // $bonus_to_apply และ $calculated_required_turnover จะยังคงเป็น 0.0 ตามค่าเริ่มต้น
                            }
                        } else { // โปรโมชั่นสำหรับสมาชิกทุกคน (เลือก)
                            error_log("All member promotions (selected promo)");
                            $message .= "All member promotions, ";

                            // กำหนดค่าโบนัสและเทิร์นโอเวอร์
                            $bonus_to_apply = $current_calculated_bonus;
                            $bonus = $bonus_to_apply; // อัปเดตตัวแปร $bonus สำหรับ Telegram log

                            $base_amount_for_turnover = $cryptoAmount + $bonus_to_apply;
                            if ($pro->is_percentage_based) {
                                $calculated_required_turnover = $base_amount_for_turnover * ($current_turnover_value / 100);
                            } else {
                                $calculated_required_turnover = $base_amount_for_turnover * $current_turnover_value;
                            }

                            $applied_promotion_name = $pro->name;
                            $promotion_found_and_applied = true;
                        }
                    } else { // turnover_on == 0 สำหรับโปรโมชั่นที่เลือก
                        error_log("Turnover off for selected promotion. No bonus from this promo.");
                        $message .= "Turnover off for selected promo, ";
                        // ในกรณีนี้จะไม่มีโบนัสจากโปรโมชั่นที่เลือก แต่ยอดฝากจะยังเข้า
                        // $bonus_to_apply และ $calculated_required_turnover จะยังคงเป็น 0.0 ตามค่าเริ่มต้น
                    }
                }
            } else { // promotion_id == 0 (ไม่ได้เลือกโปรโมชั่น)
                error_log("No promotion selected.");
                $message .= "No promotion selected, ";
                // ไม่มีโปรโมชั่นที่ถูกเลือก ไม่มีโบนัส ไม่มี turnover
                // $bonus_to_apply และ $calculated_required_turnover จะยังคงเป็น 0.0 ตามค่าเริ่มต้น
            }
        }


        // --------------------------------------------------------------------------------------
        // *** สรุปผลลัพธ์และอัปเดต Wallet / Transfer ***
        // --------------------------------------------------------------------------------------

        // ถ้ามีโปรโมชั่นถูก apply (ไม่ว่าจะ auto หรือเลือก)
        if ($promotion_found_and_applied) {
            error_log("Applying bonus: {$bonus_to_apply} with total required turnover: {$calculated_required_turnover}");
            $member->wallet_balance = (float) $member->wallet_balance + $cryptoAmount + $bonus_to_apply;
            $amount_betflix = $cryptoAmount + $bonus_to_apply;
            $transfer->promotion = $applied_promotion_name;
            // *** NEW: บันทึกยอด Turnover ที่ต้องทำจริง ***
            // $transfer->required_turnover_amount = $calculated_required_turnover; // สมมติว่ามี column นี้ในตาราง transfers
            // $transfer->bonus_applied = $bonus_to_apply; // บันทึกโบนัสที่ให้ด้วย

            $transfer->turnover_on = 1;

            // เพิ่มการบันทึก promotion_id ที่ถูกใช้ (ถ้ามีใน $pro)
            $promotion_id_used_for_transfer = null;
            if (isset($pro) && $pro instanceof Promotion) {
                $promotion_id_used_for_transfer = $pro->id;
            } elseif (isset($pro_recurring) && $pro_recurring instanceof Promotion) {
                $promotion_id_used_for_transfer = $pro_recurring->id;
            }
            $transfer->promotion_id = $promotion_id_used_for_transfer;

            $bonus = $bonus_to_apply; // อัปเดตตัวแปร $bonus สำหรับ Telegram log

        } else { // ไม่มีโปรโมชั่นใดๆ เข้าเงื่อนไข หรือไม่ถูกเลือก
            error_log("No applicable promotion found or selected. Only deposit amount will be added.");
            $message .= "No applicable promo, ";
            $member->wallet_balance = (float) $member->wallet_balance + (float) $cryptoAmount;
            $amount_betflix = $cryptoAmount;
            $transfer->promotion = "ไม่มีโปรโมชั่น"; // หรือค่า default อื่นๆ
            // $transfer->required_turnover_amount = 0.0; // ไม่มีโปรโมชั่นก็ไม่มีเทิร์น
            // $transfer->bonus_applied = 0.0;
            $transfer->promotion_id = 0; // ไม่มีโปรโมชั่นก็เป็น null
        }

        error_log("Bonus for Telegram = " . $bonus); // ตัวแปร $bonus นี้จะถูกใช้ใน Telegram

        $bf_deposit = app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username, ($amount_betflix));
        Log::info('Deposit Betflix ' . $bf_deposit . ' ' . ($amount_betflix) . ' User = ' . $member->username);
        error_log('Deposit Betflix ' . $bf_deposit . ' ' . ($amount_betflix) . ' User = ' . $member->username);
        // $bf_deposit = "success";
        if ($bf_deposit == "success") {
            error_log("crypto_deposit bf_deposit success");

            $wheel_setting = WheelSpin::first();
            if ($wheel_setting && $wheel_setting->ticket_condition > 0) {
                if ((float) $cryptoAmount >= (float) $wheel_setting->ticket_condition) {
                    $total_spin = floor((float) $cryptoAmount / (float) $wheel_setting->ticket_condition);
                    $member->remaining_spin = (float) $member->remaining_spin + (float) $total_spin;
                }
            }

            $member->save();
            $transfer->new_balance = $member->wallet_balance;
            // $transfer->status และ $transfer->old_balance ถูกตั้งค่าไว้ด้านบนแล้ว
            $transfer->save();

            // **หมายเหตุ:** ส่วนนี้ (`$bank->balance = ...`) ดูเหมือนจะซ้ำซ้อน
            // หากยอดเงินเข้าบัญชีธนาคารถูกบันทึกไปแล้วเมื่อมีการรับเงิน
            // แต่ถ้า Logic ของคุณคือการอัปเดตยอดเงินในบัญชีธนาคารของระบบเมื่อเงินถูกโอนไปให้ Betflix
            // ก็สามารถเก็บไว้ได้ แต่ควรพิจารณาความถูกต้องของ Flow
            $bank = Bank::where('account_no', $transfer->deposit_to_bank_no)->first();
            if ($bank) {
                // หากคุณเคยเพิ่ม $cryptoAmount เข้า bank->balance ในบล็อก success
                // ก็ควรหักออกในบล็อก failure นี้
                // $bank->balance = (float) $bank->balance + (float) $cryptoAmount;
                // $bank->save();
            }

            // บันทึก PromotionUsed ก็ต่อเมื่อมีการใช้โปรโมชั่นจริง
            if ($promotion_found_and_applied) {
                // ดึง promotion_id และ promotion_name ที่ถูกใช้จริง
                $promotion_id_to_log = null;
                $promotion_name_to_log = $applied_promotion_name;

                if (isset($pro) && $pro instanceof Promotion) {
                    $promotion_id_to_log = $pro->id;
                } elseif (isset($pro_recurring) && $pro_recurring instanceof Promotion) {
                    $promotion_id_to_log = $pro_recurring->id;
                }

                if ($promotion_id_to_log) { // ตรวจสอบว่ามี promotion_id ที่จะบันทึก
                    PromotionUsed::create([
                        'member_id' => $member->id,
                        'promotion_id' => $promotion_id_to_log,
                        'promotion_name' => $promotion_name_to_log,
                        'amount' => $bonus_to_apply, // ใช้ $bonus_to_apply ที่คำนวณได้
                    ]);
                }
            }
            $bonus = $bonus_to_apply; // อัปเดตตัวแปร $bonus สำหรับ Telegram log
            try {
                TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                    ->line('BOT ' . env('APP_NAME'))
                    ->line('Transaction completed, credit transferred ' . $member->username)
                    ->line('Amount :' . $cryptoAmount)
                    ->line('Bonus :' . $bonus) // ใช้ floor() กับ bonus ด้วย
                    ->line('Promotion : ' . $applied_promotion_name) // แสดงชื่อโปรโมชั่นที่ถูกใช้
                    ->line('Message : ' . $message) // แสดง message จาก logic
                    ->send();
            } catch (\Exception $e) {
                error_log("Error sending Telegram message (success path): " . $e->getMessage());
            }
            return 200;
        } else {
            error_log("crypto_deposit bf_deposit failed for user: " . $member->username . " with response: " . $bf_deposit);
            // กรณี Betflix Deposit ไม่สำเร็จ ควร Rollback Bank Balance และ Member Wallet Balance ด้วย
            // เนื่องจากยอดเงินถูกเพิ่มเข้า wallet_balance แล้ว
            $member->wallet_balance = $old_balance; // คืนยอดเงินใน wallet ของสมาชิก
            $member->save();

            // **หมายเหตุ:** การคืนเงินเข้าบัญชีธนาคารของระบบ (Bank)
            // ควรพิจารณาว่ายอดเงินนี้ถูกเพิ่มเข้าไปใน Bank Balance ตอนไหน
            // หากถูกเพิ่มไปแล้วตอนรับเงิน (ก่อนเรียกฟังก์ชันนี้) ก็ไม่ควรเพิ่มซ้ำ
            // หากถูกเพิ่มในฟังก์ชันนี้ (ซึ่งไม่น่าเป็นไปได้) ก็ต้องหักออก
            // แต่จากโค้ดเดิมของคุณใน if($bf_deposit == "success") มีการเพิ่ม Bank Balance
            // ดังนั้นในกรณีที่ล้มเหลว ก็ควรหักออก (หากคุณต้องการให้ Bank Balance สะท้อนยอดเงินที่โอนไป Betflix)
            $bank = Bank::where('account_no', $transfer->deposit_to_bank_no)->first();
            if ($bank) {
                // หากคุณเคยเพิ่ม $cryptoAmount เข้า bank->balance ในบล็อก success
                // ก็ควรหักออกในบล็อก failure นี้
                // $bank->balance = (float) $bank->balance - (float) $cryptoAmount;
                // $bank->save();
            }

            // อัปเดตสถานะ Transfer เป็น Failed
            $transfer->status = 3; // หรือสถานะสำหรับ Failed
            $transfer->status_code = "BOT.ไม่สำเร็จ: " . $bf_deposit;
            $transfer->new_balance = $member->wallet_balance; // ยอดเงินใหม่ควรเป็นยอดเงินเก่าที่ rollback แล้ว
            $transfer->save();

            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line('BOT ' . env('APP_NAME'))
                ->line('Deposit Betflix failed for user ' . $member->username)
                ->line('Amount :' . $cryptoAmount)
                ->line('Response :' . $bf_deposit)
                ->send();
            return 500;
        }
    }
}
