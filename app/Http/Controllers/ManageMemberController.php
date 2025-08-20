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
use App\Models\WheelSpin;
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
        $memberlist = Members::where('active', 1)->orderBy('id', 'DESC')->get();
        return view('manage-member.index', compact('memberlist'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function historyTransfer($id)
    {
        //
        $transfer = Transfer::join('members', function ($join) {
            $join->on('members.id', '=', 'transfer.member_id');
        })
            ->select(\DB::raw('transfer.*,members.bank_number,members.account_name,members.bank_name,members.username'))
            ->where('transfer.member_id', $id)->get();
        return view('manage-member.historyTransfer', compact('transfer'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function approveDeposit(Request $request)
    {
        //
        $member = Members::find($request->member_id);
        $transfer = Transfer::find($request->transfer_id);
        Log::info($request->type . ' Admin approve ' . $member->username . ' Balance =  ' . $member->wallet_balance . ' transfer amount =' . $transfer->amount);
        error_log($request->type . ' Admin approve ' . $member->username . ' Balance =  ' . $member->wallet_balance . ' transfer amount =' . $transfer->amount);

        if ($transfer->status == 2 || $transfer->status == 3) {
            return redirect()->back();
        }

        $transfer->old_balance = $member->wallet_balance;
        if ($request->status == 'approve') {
            $old_balance = $member->wallet_balance;
            $bonus = 0;

            // ... โค้ดส่วนบน (น่าจะอยู่ใน Controller หรือ Service Class) ...

            if ($request->type == 'deposit') {
                $amount_betflix = 0; // ตั้งค่าเริ่มต้น
                // เก็บ old_balance ก่อนที่จะมีการปรับปรุง
                $old_balance = (float) $member->wallet_balance;

                $bank = Bank::where('account_no', $transfer->deposit_to_bank_no)->first();
                if ($bank) {
                    $bank->balance = (float) $bank->balance + (float) $transfer->amount;
                    $bank->save();
                }

                $message = ''; // ใช้สำหรับเก็บข้อความ log/แจ้งเตือน
                // $pro_name =""; // ไม่จำเป็นต้องใช้ตัวแปรนี้แล้ว เพราะเรามี $applied_promotion_name
                $bonus = 0.0; // ตั้งค่าเริ่มต้นสำหรับ bonus ที่จะใช้ใน log/telegram

                // ** กำหนดค่าเริ่มต้นสำหรับผลลัพธ์การคำนวณโบนัสและ turnover **
                $bonus_to_apply = 0.0;
                $calculated_required_turnover = 0.0; // จะเก็บยอด turnover ที่ต้องทำจริง (บาท)
                $applied_promotion_name = '';
                $promotion_found_and_applied = false; // Flag เพื่อติดตามว่าได้มีการใช้โปรโมชั่นหรือไม่

                // ดึงข้อมูลโปรโมชั่นต่อเนื่องทั้งหมดที่ active และเป็นของ store_id นี้
                // และเป็นโปรโมชั่นสำหรับสมาชิกใหม่แบบต่อเนื่อง
                $recurring_new_user_promotions = Promotion::where('enable', 1)->where('active', 1)->where('is_newuser', 1)->where('is_recurring_promotion', 1)->get();

                // ตรวจสอบจำนวนการฝากของสมาชิก (เพื่อดูว่าเป็นการฝากครั้งแรกหรือไม่)
                $user_transfer_count = Transfer::where('member_id', $member->id)->where('status', 2)->where('type', 'deposit')->count(); // ใช้ count() โดยตรงจะเร็วกว่า get()->count()
                error_log('user transfer count = ' . $user_transfer_count);

                // --------------------------------------------------------------------------------------
                // *** Logic สำหรับโบนัสต่อเนื่องสำหรับสมาชิกใหม่ (ไม่ต้องรับโปรเข้ามา) ***
                // --------------------------------------------------------------------------------------
                // เงื่อนไข: ต้องไม่ใช่การฝากครั้งแรก (user_transfer_count > 0) และมีวันที่สมัคร
                if ($user_transfer_count > 0 && $member->created_at) {
                    $registrationDate = \Carbon\Carbon::parse($member->created_at);
                    $now = \Carbon\Carbon::now();

                    foreach ($recurring_new_user_promotions as $pro_recurring) {
                        $promotionEndDate = $registrationDate->copy()->addDays($pro_recurring->recurring_promotion_days);

                        // ถ้ายังอยู่ในช่วงเวลาโปรโมชั่นต่อเนื่อง
                        if ($now->lt($promotionEndDate)) {
                            error_log('Auto-applying recurring new member promotion: ' . $pro_recurring->name);
                            $message .= 'Auto recurring promo applied, ';

                            // คำนวณโบนัสจาก recurring_bonus_percentage
                            if ($pro_recurring->recurring_bonus_percentage !== null && $pro_recurring->recurring_bonus_percentage > 0) {
                                $bonus_to_apply = $transfer->amount * ($pro_recurring->recurring_bonus_percentage / 100);
                                error_log("Recurring Bonus Percentage: {$pro_recurring->recurring_bonus_percentage}%, Calculated Recurring Bonus: {$bonus_to_apply}");
                            } else {
                                error_log('Recurring bonus percentage is null or zero for auto-applied promo.');
                                $bonus_to_apply = 0.0;
                            }

                            // กำหนด turnover สำหรับโปรต่อเนื่อง - ใช้แค่ recurring_turnover_percentage เท่านั้น
                            $base_amount_for_turnover = $transfer->amount + $bonus_to_apply;

                            if ($pro_recurring->recurring_turnover_percentage !== null && $pro_recurring->recurring_turnover_percentage > 0) {
                                $calculated_required_turnover = $base_amount_for_turnover * ($pro_recurring->recurring_turnover_percentage / 100);
                                error_log("Recurring Turnover (Percentage Only): {$pro_recurring->recurring_turnover_percentage}%, Calculated Turnover Amount: {$calculated_required_turnover}");
                            } else {
                                $calculated_required_turnover = 0.0;
                                error_log('No recurring turnover percentage defined or is zero for auto-applied promo.');
                            }

                            $applied_promotion_name = $pro_recurring->name . ' (Recurring)';
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
                if (!$promotion_found_and_applied) {
                    // ถ้ายังไม่มีโปรโมชั่นใดๆ ถูก apply
                    if ($transfer->promotion_id != 0) {
                        error_log('promotion id = ' . $transfer->promotion_id);
                        if ($transfer->turnover_on == 1) {
                            // ตรวจสอบ turnover_on สำหรับโปรที่เลือก
                            error_log('turnover on = ' . $transfer->turnover_on);

                            $pro = Promotion::find($transfer->promotion_id);

                            if (!$pro) {
                                error_log('Promotion not found for ID: ' . $transfer->promotion_id);
                                $message .= 'Promotion not found, ';
                                // ในกรณีนี้จะไม่มีโบนัสจากโปรโมชั่นที่เลือก
                            } else {
                                // $pro_name = $pro->name; // ใช้ $applied_promotion_name แทน
                                error_log('Pro is_newuser = ' . $pro->is_newuser);
                                error_log('Pro is_percentage_based = ' . $pro->is_percentage_based);

                                // *** คำนวณโบนัสและ turnover ตามประเภทโปรโมชั่นที่เลือก ***
                                $current_calculated_bonus = 0.0;
                                $current_turnover_value = 0.0;

                                if ($pro->is_percentage_based) {
                                    error_log('Calculating bonus based on percentage (selected promo).');
                                    if ($pro->bonus_percentage !== null && $pro->bonus_percentage > 0) {
                                        $current_calculated_bonus = $transfer->amount * ($pro->bonus_percentage / 100);
                                    }
                                    if ($pro->turnover_percentage !== null && $pro->turnover_percentage > 0) {
                                        $current_turnover_value = $pro->turnover_percentage;
                                    }
                                } else {
                                    error_log('Calculating bonus based on fixed amount (selected promo).');
                                    $current_calculated_bonus = $pro->bonus;
                                    $current_turnover_value = $pro->turnover;
                                }
                                // *** จบการกำหนดค่าโบนัสและ turnover สำหรับโปรที่เลือก ***

                                if ($pro->is_newuser == 1) {
                                    // โปรโมชั่นแรกสำหรับสมาชิกใหม่ที่เลือก
                                    error_log('เป็นโปรโมชั่นแรกสำหรับสมาชิกใหม่ (เลือก)');
                                    if ($user_transfer_count == 0) {
                                        // ต้องเป็นการฝากครั้งแรกจริงๆ
                                        error_log('Meet first-time new member conditions (selected promo)');
                                        $message .= 'Meet first-time new member conditions, ';

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
                                        error_log('Does not meet first-time new member requirements (already made first deposit), no bonus from selected promo.');
                                        $message .= 'Does not meet first-time new member requirements, ';
                                        // ไม่เข้าเงื่อนไข (ไม่ใช่ครั้งแรก), ไม่มีโบนัสจากโปรนี้
                                        // $bonus_to_apply และ $calculated_required_turnover จะยังคงเป็น 0.0 ตามค่าเริ่มต้น
                                    }
                                } else {
                                    // โปรโมชั่นสำหรับสมาชิกทุกคน (เลือก)
                                    error_log('All member promotions (selected promo)');
                                    $message .= 'All member promotions, ';

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
                                }
                            }
                        } else {
                            // turnover_on == 0 สำหรับโปรโมชั่นที่เลือก
                            error_log('Turnover off for selected promotion. No bonus from this promo.');
                            $message .= 'Turnover off for selected promo, ';
                            // ในกรณีนี้จะไม่มีโบนัสจากโปรโมชั่นที่เลือก แต่ยอดฝากจะยังเข้า
                            // $bonus_to_apply และ $calculated_required_turnover จะยังคงเป็น 0.0 ตามค่าเริ่มต้น
                        }
                    } else {
                        // promotion_id == 0 (ไม่ได้เลือกโปรโมชั่น)
                        error_log('No promotion selected.');
                        $message .= 'No promotion selected, ';
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


                    // เพิ่มการบันทึก promotion_id ที่ถูกใช้ (ถ้ามีใน $pro)
                    if (isset($pro) && $pro instanceof Promotion) {
                        $transfer->promotion_id = $pro->id;
                    } elseif (isset($pro_recurring) && $pro_recurring instanceof Promotion) {
                        $transfer->promotion_id = $pro_recurring->id;
                    }
                } else {
                    // ไม่มีโปรโมชั่นใดๆ เข้าเงื่อนไข หรือไม่ถูกเลือก
                    error_log('No applicable promotion found or selected. Only deposit amount will be added.');
                    $message .= 'No applicable promo, ';
                    $member->wallet_balance = (float) $member->wallet_balance + (float) $transfer->amount;
                    $amount_betflix = $transfer->amount;
                    $transfer->promotion = 'ไม่มีโปรโมชั่น'; // หรือค่า default อื่นๆ
                    // $transfer->required_turnover_amount = 0.0; // ไม่มีโปรโมชั่นก็ไม่มีเทิร์น
                    // $transfer->bonus_applied = 0.0;
                    $transfer->promotion_id = 0; // ไม่มีโปรโมชั่นก็เป็น null
                }

                error_log('Bonus = ' . $bonus); // ตัวแปร $bonus นี้จะถูกใช้ใน Telegram

                $bf_deposit = app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username, ($amount_betflix));
                Log::info('Deposit Betflix ' . $bf_deposit . ' ' . $amount_betflix . ' User =  ' . $member->username);
                error_log('Deposit Betflix ' . $bf_deposit . ' ' . $amount_betflix . ' User =  ' . $member->username);
                // $bf_deposit = 'success';
                if ($bf_deposit == 'success') {
                    $wheel_setting = WheelSpin::first();
                    if ($wheel_setting && $wheel_setting->ticket_condition > 0) {
                        // ตรวจสอบว่า $wheel_setting ไม่ใช่ null ก่อน
                        if ((float) $transfer->amount >= (float) $wheel_setting->ticket_condition) {
                            $total_spin = floor((float) $transfer->amount / (float) $wheel_setting->ticket_condition);
                            $member->remaining_spin = (float) $member->remaining_spin + (float) $total_spin;
                        }
                    }

                    $member->save();
                    $transfer->new_balance = $member->wallet_balance;
                    $transfer->status = 2;
                    $transfer->status_code = 'อนุมัติ';
                    $transfer->old_balance = $old_balance; // ใช้ $old_balance ที่เก็บไว้ตอนต้น
                    if ($promotion_found_and_applied) {
                        $transfer->turnover_on = 1;
                    }
                    $transfer->save();

                    // บันทึก PromotionUsed ก็ต่อเมื่อมีการใช้โปรโมชั่นจริง
                    if ($promotion_found_and_applied) {
                        // ต้องหา promotion_id ที่ถูกใช้จริง
                        $promotion_id_used = null;
                        if (isset($pro) && $pro instanceof Promotion) {
                            $promotion_id_used = $pro->id;
                        } elseif (isset($pro_recurring) && $pro_recurring instanceof Promotion) {
                            $promotion_id_used = $pro_recurring->id;
                        }

                        if ($promotion_id_used) {
                            PromotionUsed::create([
                                'member_id' => $member->id,
                                'promotion_id' => $promotion_id_used,
                                'promotion_name' => $applied_promotion_name, // ใช้ชื่อโปรโมชั่นที่ถูก apply
                                'amount' => $bonus_to_apply, // ใช้ $bonus_to_apply ที่คำนวณได้
                            ]);
                        }
                    }
                } else {
                    // กรณี Betflix Deposit ไม่สำเร็จ ควร Rollback Bank Balance ด้วย
                    if ($bank) {
                        $bank->balance = (float) $bank->balance - (float) $transfer->amount; // คืนเงินจาก Bank
                        $bank->save();
                    }
                    return redirect()->back()->with('error', $bf_deposit);
                }

                $bonus = $bonus_to_apply; // อัปเดตตัวแปร $bonus สำหรับ Telegram log

                // แก้ไข Telegram message ให้ใช้ $applied_promotion_name และ $message จาก logic ด้านบน
                TelegramMessage::create()
                    ->to(env('TELEGRAM_G_ID'))
                    ->line(env('APP_NAME'))
                    ->line('Admin has approved the credit. ' . $member->username)
                    ->line('Amount :' . floor($transfer->amount))
                    ->line('Bonus :' .  $bonus) // ใช้ floor() กับ bonus ด้วยเพื่อความสอดคล้อง
                    ->line('Promotion : ' . $applied_promotion_name) // แสดงชื่อโปรโมชั่นที่ถูกใช้
                    ->line('Message : ' . $message) // แสดง message จาก logic
                    ->send();
            } elseif ($request->type == 'withdraw') {
                $bank = Bank::where('account_no', $transfer->deposit_to_bank_no)->first();
                if ($bank) {
                    $bank->balance = (float) $bank->balance - (float) $transfer->amount;
                    $bank->save();
                }

                $member->save();
                $transfer->new_balance = $member->wallet_balance;
                $transfer->status = 2;
                $transfer->status_code = 'อนุมัติ';
                $transfer->old_balance = $old_balance;
                $transfer->save();

                TelegramMessage::create()
                    ->to(env('TELEGRAM_G_ID'))
                    ->line(env('APP_NAME'))
                    ->line('Admin Make a transaction, approve a withdrawal ' . $member->username)
                    ->line('Mount :' . floor($transfer->amount))
                    ->line('Warning: Admin must make the transfer by themselves via the bank app.')
                    ->send();
            }
        } elseif ($request->status == 'pending') {
            $transfer->status = 1;
            $transfer->status_code = 'รอดำเนินการ';
            $transfer->save();
        } elseif ($request->status == 'reject') {
            $transfer->status = 3;
            $transfer->status_code = 'ปฏิเสธ';
            $transfer->turnover_on = 0;
            $transfer->save();

            if ($request->type == 'withdraw') {
                $bf_deposit = app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username, floor($transfer->amount));
                Log::info('rollBack Deposit Betflix ' . $bf_deposit . ' ' . $transfer->amount . ' User =  ' . $member->username);
                if ($bf_deposit == 'success') {
                    $new_balance = (float) $member->wallet_balance + $transfer->amount;
                    $member->update(['wallet_balance' => strval($new_balance)]);
                }
            }
        }
        return redirect()->back()->with('status', '200');
    }

    function changePassword(Request $request)
    {
        $member = Members::find($request->id);
        // $random_pass = $this->strRandom(8);
        $member->password = Hash::make($request->password);
        $member->save();
        return redirect()->back()->with('status', 'success');
    }

    public static function strRandom($length)
    {
        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($str, 5)), 0, $length);
    }

    public function memberlock(Request $request)
    {
        $status = 0;
        if ($request->status == 1) {
            $status = 0;
        } else if ($request->status == 0) {
            $status = 1;
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

        Log::info("edit balance =" . $request->balance . " member_id =" . $request->member_id . " type = " . $request->type." Balance = " . $request->balance);

        $update_balance = 0;
        $member = Members::find($request->member_id);
        if ($member) {
            $old_balance = app(\App\Http\Controllers\BetflixController::class)->Balance($member->username);
            if ($old_balance < $request->balance) {
                $update_balance = $request->balance - $old_balance;
                Log::info(" + Deposit update_balance =" . $update_balance);
                $bf = app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($member->username, $update_balance);
                // Log::info("Betflix Deposit " . $bf . ' ' . $update_balance . ' User =  ' . $member->username);
            } else if ($old_balance > $request->balance) {
                if ($request->type == "แก้เครดิต") {
                    $update_balance = $old_balance - $request->balance;

                    Log::info(" - Withdraw update_balance =" . $update_balance);
                    $bf = app(\App\Http\Controllers\BetflixController::class)->Master_Withdraw($member->username, $update_balance);
                    // Log::info("Betflix Withdraw " . $bf . ' ' . $update_balance . ' User =  ' . $member->username);
                }else{
                    return redirect()->route('managemember.index')->with('error', 'Select type is not correct');
                }
            }

            $new_balance = app(\App\Http\Controllers\BetflixController::class)->Balance($member->username);

            // $new_balance = 2;
            $currentBalance = $member->wallet_balance;

            $amount2 = ((float) $request->balance - (float) $currentBalance);

            $member->wallet_balance = $new_balance;
            $member->update_by = $request->user_id;
            $member->save();

            $d = new MemberEditBalance;
            $d->user_id = $request->user_id;
            $d->member_id = $request->member_id;
            $d->amount = $amount2;
            $d->type = $request->type;
            $d->balance = $currentBalance;
            $d->edit_balance = $new_balance;
            $d->save();

            // MemberEditBalance::create([
            //     'user_id' => $request->user_id,
            //     'member_id' => $request->member_id,
            //     'amount' => $amount2,
            //     'type' => $request->type,
            //     'balance' => $currentBalance,
            //     'edit_balance' => $new_balance,
            // ]);
            Log::info("MemberEditBalance created for  member_id = " . $request->member_id . " amount = " . $amount2 . " type = " . $request->type);
            return redirect()->route('managemember.index')->with('success', 'success');
        }else {
            Log::error("Member not found for member_id = " . $request->member_id);
            return redirect()->route('managemember.index')->with('error', 'Member not found');
        }


    }

    function memberupdateBankAccount(Request $request)
    {
        // dd($request);
        $member_ = Members::find($request->member_id);
        // $member_->bank_name = $request->bank_name;
        // $member_->bank_number = $request->bank_number;
        // $member_->account_name = $request->account_name;
        // $member_->bank_code = $request->bank_code;
        $member_->wallet_address = $request->wallet_address;
        $member_->save();
        return redirect()->route('managemember.index')->with('success', 'success');
    }

    public static function staff_detail($id)
    {
        $name = 'System';
        $user = User::where('id', $id)->first();
        if ($user) {
            $name = $user->name;
        }
        return $name;
    }

    public function cash_back()
    {
        set_time_limit(300000000);
        Log::info("Run cash_back");
        TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
            ->line(env('APP_NAME'))
            ->line('BOT เริ่มทำการ Cashback ')
            ->send();

        $members = Members::get();

        foreach ($members as $member) {
            sleep(1);
            $last_deposit = Transfer::where('member_id', $member->id)
                ->where('status', 2)->where('promotion_id', '>', 0)
                ->where('type', 'deposit')
                ->whereDate('created_at', Carbon::now()->subDays(7))->get();

            if ($last_deposit) {
                Log::info("Cashback !! member  = " . $member->username . " มียอดฝากก่อนหน้ารับโปร");
                continue;
            }

            $last_withdraw = Transfer::where('member_id', $member->id)
                ->where('status', 2)
                ->where('type', 'withdraw')
                ->whereDate('created_at', Carbon::now()->subDays(7))->get();
            if ($last_withdraw) {
                Log::info("Cashback !! member  = " . $member->username . " มียอดถอนก่อนหน้า");
                continue;
            }

            if ($member->wallet_balance >= 1) {
                Log::info("Cashback !! member  = " . $member->username . " มียอดคงเหลือมากกว่า 1");
                continue;
            }

            $total_lose = 0;
            $cash_back = 0;
            try {
                $winlose = app(\App\Http\Controllers\BetflixController::class)->Single_Member_Report_all_Provider($member->username, -1, -1)->winloss;

                if ($winlose) {
                    $total_lose =  $winlose;
                } else {
                    $total_lose = 0;
                }
            } catch (\Exception $e) {
                Log::error('Error Betflix API : ' . $e->getMessage());
                $winlose = 0;
            }


            if (abs($total_lose) > 0) {
                $setting = Setting::get();
                if ($setting) {
                    $cash_back = (float) (abs($total_lose) * ($setting->cashback_percent / 100));
                } else {
                    $cash_back = 0;
                }
            }


            if ($cash_back > 20000) {
                $cash_back = 20000;
            }
            $logs = new Logs;
            $logs->username = $member->username;
            $logs->log = 'total_lose: ' . number_format($total_lose, 2) . ' cash back: ' . number_format($cash_back, 2);
            $logs->save();

            if ($cash_back > 0) {
                Log::info('Cashback ++ Username : ' . $member->username . ' total_lose: ' . number_format($total_lose, 2) . ' cash back: ' . number_format($cash_back, 2));
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
<?php

function affiliate()
{
    // เพิ่มเวลาการทำงานของสคริปต์ เพื่อป้องกันการ Timeout
    set_time_limit(3000000000);

    // บันทึก Log และส่งข้อความ Telegram เพื่อแจ้งการเริ่มต้น
    Log::info("Run affiliate");
    TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
        ->line(env('APP_NAME'))
        ->line('BOT เริ่มทำการ affiliate')
        ->send();

    // ดึงข้อมูลสมาชิกทั้งหมดที่มีผู้แนะนำ
    $members = Members::where('ref_user', '!=', null)->get();
    Log::info("Total Members affiliate : " . count($members));

    // วนลูปเพื่อคำนวณค่าคอมมิชชั่นสำหรับสมาชิกแต่ละคน (Member1)
    foreach ($members as $main_member) {
        sleep(2);
        $total_commission = 0; // ยอดคอมมิชชั่นรวมสำหรับ main_member

        Log::info("Member main (Level 1): " . $main_member->username . ' under member count = ' . count(json_decode($main_member->ref_user)));

        // ตรวจสอบว่า main_member มีผู้แนะนำหรือไม่
        if (json_decode($main_member->ref_user)) {
            set_time_limit(3000000000);

            // วนลูปคำนวณคอมมิชชั่นจากผู้แนะนำตรง (Level 2 หรือ Member2)
            foreach (json_decode($main_member->ref_user) as $_member_level2_id) {
                sleep(3);

                $under_member = Members::where('id', $_member_level2_id)->first();
                if (!$under_member) {
                    continue;
                }

                Log::info("Under member (Level 2): " . $under_member->username);

                // ตรวจสอบยอดเล่น/ยอดเสียของ under_member (Member2)
                try {
                    $bf_total_bet_level2 = app(\App\Http\Controllers\BetflixController::class)->Single_Member_Report_all_Provider($under_member->username, -1, -1);
                    $total_bet_level2 = $bf_total_bet_level2->valid_amount ?? 0;
                    $winlose_level2 = $bf_total_bet_level2->winloss ?? 0;

                    $pg_total_bet_level2 = app(\App\Http\Controllers\PgHardController::class)->pg_get_spin_summaryby_user($under_member->username, -1, -1);
                    if (isset($pg_total_bet_level2['data'][0]['totalAmount'])) {
                        $total_bet_level2 += $pg_total_bet_level2['data'][0]['totalAmount'];
                    }
                } catch (\Exception $e) {
                    Log::info('API Error for Level 2 member: ' . $e->getMessage());
                    $total_bet_level2 = 0;
                    $winlose_level2 = 0;
                }

                // คำนวณคอมมิชชั่นสำหรับ under_member (Member2) ที่แนะนำมาโดยตรง (Level 1 สำหรับ main_member)
                $affiliate = Affiliate::first();
                if ($affiliate->is_enable_af_winlose == 1) {
                    if ($total_bet_level2 > 1) {
                        if ($affiliate->af_receive_percent_winlose_1 == "ยอดเดิมพัน") {
                            $commission_level1 = $total_bet_level2 * ($affiliate->af_receive_percent_winlose_2 / 100);
                            $total_commission += $commission_level1;
                            Log::info("Commission from bet (Level 2): " . $commission_level1);
                        } else if ($affiliate->af_receive_percent_winlose_1 == "ยอดเสีย" && $winlose_level2 < 0) {
                            $commission_level1 = abs($winlose_level2) * ($affiliate->af_receive_percent_winlose_2 / 100);
                            $total_commission += $commission_level1;
                            Log::info("Commission from win/lose (Level 2): " . $commission_level1);
                        }
                    }
                }

                // ---- เริ่มการคำนวณคอมมิชชั่นสำหรับชั้นที่ 2 (Tier 3 หรือ Member3) ----
                // ตรวจสอบว่า under_member (Member2) มีผู้แนะนำต่อหรือไม่
                if (json_decode($under_member->ref_user)) {
                    Log::info("Under member (Level 2) has Level 3 members: " . count(json_decode($under_member->ref_user)));

                    foreach (json_decode($under_member->ref_user) as $_member_level3_id) {
                        sleep(3);
                        $sub_member = Members::where('id', $_member_level3_id)->first();
                        if (!$sub_member) {
                            continue;
                        }

                        Log::info("Sub member (Level 3): " . $sub_member->username);

                        // ตรวจสอบยอดเล่น/ยอดเสียของ sub_member (Member3)
                        try {
                            $bf_total_bet_level3 = app(\App\Http\Controllers\BetflixController::class)->Single_Member_Report_all_Provider($sub_member->username, -1, -1);
                            $total_bet_level3 = $bf_total_bet_level3->valid_amount ?? 0;
                            $winlose_level3 = $bf_total_bet_level3->winloss ?? 0;

                            $pg_total_bet_level3 = app(\App\Http\Controllers\PgHardController::class)->pg_get_spin_summaryby_user($sub_member->username, -1, -1);
                            if (isset($pg_total_bet_level3['data'][0]['totalAmount'])) {
                                $total_bet_level3 += $pg_total_bet_level3['data'][0]['totalAmount'];
                            }
                        } catch (\Exception $e) {
                            Log::info('API Error for Level 3 member: ' . $e->getMessage());
                            $total_bet_level3 = 0;
                            $winlose_level3 = 0;
                        }

                        Log::info("Total bet (Level 3): " . $total_bet_level3);

                        // คำนวณคอมมิชชั่นสำหรับ main_member (Member1) จาก under_member (Member2) ที่แนะนำ sub_member (Member3)
                        // ใช้ af_receive_percent_winlose_3 สำหรับการคำนวณชั้นที่ 2
                        if ($affiliate->is_enable_af_winlose == 1) {
                            if ($total_bet_level3 > 1) {
                                if ($affiliate->af_receive_percent_winlose_1 == "ยอดเดิมพัน") {
                                    $commission_level2 = $total_bet_level3 * ($affiliate->af_receive_percent_winlose_3 / 100);
                                    $total_commission += $commission_level2;
                                    Log::info("Commission from bet (Level 3): " . $commission_level2);
                                } else if ($affiliate->af_receive_percent_winlose_1 == "ยอดเสีย" && $winlose_level3 < 0) {
                                    $commission_level2 = abs($winlose_level3) * ($affiliate->af_receive_percent_winlose_3 / 100);
                                    $total_commission += $commission_level2;
                                    Log::info("Commission from win/lose (Level 3): " . $commission_level2);
                                }
                            }
                        }
                    }
                }
            }

            // บันทึกยอดคอมมิชชั่นรวมสำหรับ main_member (ทั้งจาก Tier 2 และ Tier 3)
            if ($total_commission > 0) {
                Transfer::create([
                    'member_id' => $main_member->id,
                    'amount' => $total_commission,
                    'status' => 1,
                    'status_code' => 'รออนุมัติ',
                    'type' => 'commission',
                    'promotion' => 'commission',
                    'old_balance' => $main_member->wallet_balance,
                    'new_balance' => $main_member->wallet_balance + $total_commission,
                    'transfer_date' => strtotime(now()),
                ]);
            }
        }
    }

    // บันทึก Log และส่งข้อความ Telegram เพื่อแจ้งการสิ้นสุด
    Log::info('success Run affiliate');
    TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
        ->line(env('APP_NAME'))
        ->line('BOT สิ้นสุดการ Run affiliate')
        ->send();

    return 'success';
}


    function affiliate_fixdate($date_start, $date_end)
    {
        $startDate = date('Y-m-d', strtotime($date_start . ' day')) . 'T00:00:00Z';
        $endDate = date('Y-m-d', strtotime($date_end . ' day')) . 'T23:59:59Z';

        set_time_limit(3000000000);
        Log::info("Run affiliate ย้อนหลัง จากวันที่ : " . $startDate . " ถึง " . $endDate);
        TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
            ->line(env('APP_NAME'))
            ->line('BOT เริ่มทำการ affiliate ย้อนหลัง จากวันที่ : ' . $startDate . ' - ' . $endDate)
            ->send();

        $members = Members::where('ref_user', '!=', null)->get();
        Log::info("Total Members affiliate : " . count($members));
        foreach ($members as $main_member) {
            sleep(1);
            $total_commission = 0;
            Log::info("Member main : " . $main_member->username . 'uder member count = ' . count(json_decode($main_member->ref_user)));
            if (json_decode($main_member->ref_user)) {
                set_time_limit(3000000000);
                foreach (json_decode($main_member->ref_user) as $_member) {
                    sleep(2);

                    $under_member = Members::where('id', $_member)->first();
                    Log::info("Under of " . $main_member->username . " member : " . $under_member->username);

                    try {
                        $bf_total_bet = app(\App\Http\Controllers\BetflixController::class)->Single_Member_Report_all_Provider($under_member->username, $date_start, $date_end);
                        if ($bf_total_bet) {
                            $total_bet = $bf_total_bet->valid_amount;
                            $winlose = $bf_total_bet->winloss;
                            Log::info("bf_total_bet : " . $bf_total_bet->valid_amount);
                        } else {
                            Log::info("bf_total_bet : " . $bf_total_bet->msg);
                        }
                    } catch (\Exception $e) {
                        Log::info('Betflix API Error : ' . $e->getMessage());
                        $total_bet = 0;
                        $winlose = 0;
                        continue;
                    }

                    try {
                        $pg_total_bet = app(\App\Http\Controllers\PgHardController::class)->pg_get_spin_summaryby_user($under_member->username, $date_start, $date_end);

                        if (count($pg_total_bet['data']) > 0) {
                            Log::info("pg_total_bet : " . $pg_total_bet['data'][0]['totalAmount']);
                            $total_bet = $total_bet + $pg_total_bet['data'][0]['totalAmount'];
                        } else {
                            Log::info('PgHard API No have User Data ' . $under_member->username);
                        }
                    } catch (\Exception $e) {
                        Log::info('PgHard API Error : ' . $e->getMessage());
                        $pg_total_bet = 0;
                    }

                    Log::info("total_bet : " . $total_bet);

                    $affiliate = Affiliate::first();
                    if ($affiliate->is_enable_af_winlose == 1) {
                        Log::info("is_enable_af_winlose = " . $affiliate->is_enable_af_winlose);
                        if ($total_bet > 1) {

                            if ($affiliate->af_receive_percent_winlose_1 == "ยอดเดิมพัน") {
                                $commission = $total_bet * ($affiliate->af_receive_percent_winlose_2 / 100);
                                $total_commission += $commission;
                                Log::info("commission ยอดเดิมพัน total_bet : " . $total_bet . " commission : " . $commission);
                            } else if ($affiliate->af_receive_percent_winlose_1 == "ยอดเสีย" && $winlose < 0) {
                                $commission = abs($winlose) * ($affiliate->af_receive_percent_winlose_2 / 100);
                                $total_commission += $commission;
                                Log::info("commission ยอด winlose : " . $winlose . " commission : " . $commission);
                            }
                        }
                    }
                }
                if ($total_commission > 0) {
                    Transfer::create([
                        'member_id' => $main_member->id,
                        'amount' => $total_commission,
                        'status' => 1,
                        'status_code' => 'รออนุมัติ',
                        'type' => 'commission',
                        'promotion' => 'commission',
                        'old_balance' => $main_member->wallet_balance,
                        'new_balance' => $main_member->wallet_balance + $total_commission,
                        'transfer_date' => strtotime(now()),
                    ]);
                }

                // $bf_deposit=  app(\App\Http\Controllers\BetflixController::class)->Master_Deposit($main_member->username,floor($commission));
                // Log::info('Deposit commission to Betflix  '.$bf_deposit.' '.floor($commission).' User =  '.$main_member->username);
                // $main_member->wallet_balance = (float) ($main_member->wallet_balance + $to);
                // $main_member->save();
            }
        }
        Log::info('success Run affiliate Fixdete');
        TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
            ->line(env('APP_NAME'))
            ->line('BOT สิ้นสุดการ Run affiliate ย้อนหลัง')
            ->send();
        return 'success';
    }

    public function check_token(Request $request)
    {

        $user = Members::where('token', $request->token)->first();
        if ($user) {
            return 1;
        } else {
            return 0;
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
