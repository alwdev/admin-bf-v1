<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Transfer;
use App\Models\Members;
use App\Models\Payout;
use App\Models\Bank;
use App\Models\PromotionUsed;
use App\Models\MemberEditBalance;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $member = Members::get();
        $member_new = Members::whereDate('created_at', Carbon::today())->orderby('id', 'desc')->get();
        $transfer = Transfer::where('status', 2)->whereDate('created_at', Carbon::today())->where('type', '!=', 'cashback')->get();
        $total_deposit = 0;
        $total_withdraw = 0;
        $total_member = 0;
        $total_online = 0;
        $new_member = 0;
        $total_bonus = 0;
        if ($member_new) {
            foreach ($member_new as $key => $value) {
                $new_member++;
            }
        }
        $total_member = count($member);

        $total_deposit_abc = 0;
        $total_deposit_fnx = 0;
        $total_deposit_usdt = 0;
        $total_deposit_ktx = 0;

        $total_withdraw_usdt = 0;
        $total_withdraw_usdf = 0;
        $total_withdraw_abc = 0; // เพิ่มตัวแปรสำหรับยอดถอน ABC


        if ($transfer) {
            foreach ($transfer as $t) {
                if ($t->type == 'deposit') {
                    if ($t->deposit_from_bank_type == 'ABC') {
                        $total_deposit_abc += $t->amount;
                    } elseif ($t->deposit_from_bank_type == 'FNX') {
                        $total_deposit_fnx += $t->amount;
                    } elseif ($t->deposit_from_bank_type == 'USDT') {
                        $total_deposit_usdt += $t->amount;
                    }else if ($t->deposit_from_bank_type == 'KTX') {
                        $total_deposit_ktx += $t->amount;
                    }
                    $total_deposit += $t->amount;
                } else {
                    // โค้ดสำหรับคำนวณยอดถอน
                    if ($t->withdraw_bank_name == 'USDT') {
                        $total_withdraw_usdt += $t->amount;
                    } elseif ($t->withdraw_bank_name == 'USDF') {
                        $total_withdraw_usdf += $t->amount;
                    }
                    $total_withdraw += $t->amount;
                }
            }
        }

        $withdraw_amount = $total_withdraw;

        $withdraw_fee = 6.5 / 100;    // 0.065
        $thb_usd_price = 33;        // อัตราแลกเปลี่ยน

        // สมมติว่า $request->amount คือ "ยอดเงินที่ได้รับสุทธิ" ที่เราต้องการคำนวณย้อนกลับ
        $final_usd_amount = (float) $withdraw_amount;

        // คำนวณหายอดเงินก่อนหักค่าธรรมเนียม
        $before_fee_usd = $final_usd_amount / (1 - $withdraw_fee);

        // คำนวณหายอดเงินตั้งต้นในหน่วยบาท
        $original_amount = $before_fee_usd * $thb_usd_price;

        $total_withdraw_abc = $original_amount;


        $topgame = [];
        $players = app(\App\Http\Controllers\BetflixController::class)->Multiple_Member_Report(now());
        if ($players != 'error') {
            $total_online = count($players);
        } else {
            $total_online = 0;
        }
        // foreach($players as $p){
        //    $playersgame = app(\App\Http\Controllers\BetflixController::class)->Single_ReportTimeProvider($p->username,now(),now());
        //   foreach($playersgame as $pp){
        //     $topgame[] = ['provider'=>$pp->provider];
        //   }

        // }

        $transfer = Transfer::join('members', function ($join) {
            $join->on('members.id', '=', 'transfer.member_id');
        })
            ->select(DB::raw('transfer.*,members.bank_number,members.account_name,members.bank_name,members.username'))
            ->orderby('transfer.created_at', 'desc')
            ->limit(5)
            ->get();

        $total_bonus = PromotionUsed::whereDate('created_at', Carbon::today())->sum('amount');

        $manual_topup = MemberEditBalance::whereDate('created_at', Carbon::today())->where('type', 'เติมมือ')->sum('amount');
        $manual_cashback = MemberEditBalance::whereDate('created_at', Carbon::today())->where('type', 'คืนลูกค้า')->sum('amount');

        $banks = Bank::where('enable', 1)->where('active', 1)->get();
        return view('welcome', compact('total_deposit_abc','total_deposit_ktx', 'total_deposit_fnx', 'total_deposit_usdt', 'total_withdraw_usdt', 'total_withdraw_usdf', 'total_withdraw_abc', 'manual_topup', 'manual_cashback', 'banks', 'total_deposit', 'total_withdraw', 'new_member', 'total_member', 'players', 'total_online', 'topgame', 'transfer', 'member_new', 'total_bonus'));
    }

    public function dashboard_date(Request $request)
    {
        $d = explode('-', $request->date_);
        $dateS = Carbon::parse($d[0]);
        $dateE = Carbon::parse($d[1]);

        $member = Members::get();
        $member_new = Members::whereBetween('created_at', [$dateS->format('Y-m-d') . ' 00:00:00', $dateE->format('Y-m-d') . ' 23:59:59'])
            ->orderby('id', 'desc')
            ->get();
        $transfer = Transfer::where('status', 2)
            ->whereBetween('created_at', [$dateS->format('Y-m-d') . ' 00:00:00', $dateE->format('Y-m-d') . ' 23:59:59'])
            ->where('type', '!=', 'cashback')
            ->get();
        $total_deposit = 0;
        $total_withdraw = 0;
        $total_member = 0;
        $total_online = 0;
        $new_member = 0;
        $total_bonus = 0;
        if ($member_new) {
            foreach ($member_new as $key => $value) {
                $new_member++;
            }
        }
        $total_member = count($member);
        $total_deposit_abc = 0;
        $total_deposit_fnx = 0;
        $total_deposit_usdt = 0;

        $total_withdraw_usdt = 0;
        $total_withdraw_usdf = 0;
        $total_withdraw_abc = 0; // เพิ่มตัวแปรสำหรับยอดถอน ABC

        if ($transfer) {
            foreach ($transfer as $t) {
                if ($t->type == 'deposit') {
                    if ($t->deposit_from_bank_type == 'ABC') {
                        $total_deposit_abc += $t->amount;
                    } elseif ($t->deposit_from_bank_type == 'FNX') {
                        $total_deposit_fnx += $t->amount;
                    } elseif ($t->deposit_from_bank_type == 'USDT') {
                        $total_deposit_usdt += $t->amount;
                    }
                    $total_deposit += $t->amount;
                } else {
                    // โค้ดสำหรับคำนวณยอดถอน
                    if ($t->withdraw_bank_name == 'USDT') {
                        $total_withdraw_usdt += $t->amount;
                    } elseif ($t->withdraw_bank_name == 'USDF') {
                        $total_withdraw_usdf += $t->amount;
                    }
                    $total_withdraw += $t->amount;
                }
            }
        }

        $withdraw_amount = $total_withdraw;

        $withdraw_fee = 6.5 / 100;    // 0.065
        $thb_usd_price = 33;        // อัตราแลกเปลี่ยน

        // สมมติว่า $request->amount คือ "ยอดเงินที่ได้รับสุทธิ" ที่เราต้องการคำนวณย้อนกลับ
        $final_usd_amount = (float) $withdraw_amount;

        // คำนวณหายอดเงินก่อนหักค่าธรรมเนียม
        $before_fee_usd = $final_usd_amount / (1 - $withdraw_fee);

        // คำนวณหายอดเงินตั้งต้นในหน่วยบาท
        $original_amount = $before_fee_usd * $thb_usd_price;

        $total_withdraw_abc = $original_amount;

        $players = app(\App\Http\Controllers\BetflixController::class)->Multiple_Member_Report($dateS);
        if ($players) {
            $total_online = count($players);
        }
        $topgame = [];
        // foreach($players as $p){
        //     $playersgame = app(\App\Http\Controllers\BetflixController::class)->Single_ReportTimeProvider($p->username,now(),now());
        //    foreach($playersgame as $pp){
        //      $topgame[] = ['provider'=>$pp->provider];
        //    }

        //  }

        $transfer = Transfer::join('members', function ($join) {
            $join->on('members.id', '=', 'transfer.member_id');
        })
            ->select(DB::raw('transfer.*,members.bank_number,members.account_name,members.bank_name,members.username'))
            ->whereBetween('transfer.created_at', [$dateS->format('Y-m-d') . ' 00:00:00', $dateE->format('Y-m-d') . ' 23:59:59'])
            ->orderby('transfer.created_at', 'desc')
            ->limit(5)
            ->get();

        $total_bonus = PromotionUsed::whereBetween('created_at', [$dateS->format('Y-m-d') . ' 00:00:00', $dateE->format('Y-m-d') . ' 23:59:59'])->sum('amount');

        $manual_topup = MemberEditBalance::whereDate('created_at', [$dateS->format('Y-m-d') . ' 00:00:00', $dateE->format('Y-m-d') . ' 23:59:59'])
            ->where('type', 'เติมมือ')
            ->sum('amount');
        $manual_cashback = MemberEditBalance::whereDate('created_at', [$dateS->format('Y-m-d') . ' 00:00:00', $dateE->format('Y-m-d') . ' 23:59:59'])
            ->where('type', 'คืนลูกค้า')
            ->sum('amount');

        $banks = Bank::where('enable', 1)->where('active', 1)->get();
        return view('welcome', compact('total_deposit_abc', 'total_deposit_fnx', 'total_deposit_usdt', 'total_withdraw_usdt', 'total_withdraw_usdf', 'total_withdraw_abc','manual_topup', 'manual_cashback', 'total_bonus', 'banks', 'total_deposit', 'total_withdraw', 'new_member', 'total_member', 'players', 'total_online', 'topgame', 'transfer', 'member_new'));
    }
}
