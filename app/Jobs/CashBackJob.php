<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Models\Members;
use App\Models\Transfer;
use App\Models\Setting;
use App\Models\Logs;
use App\Http\Controllers\BetflixController;

class CashBackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $memberId;

    public function __construct($memberId)
    {
        $this->memberId = $memberId;
    }

    public function handle()
    {
        $member = Members::find($this->memberId);
        if (!$member) {
            return;
        }

        Log::info("Run cashback for member: {$member->username}");

        $last_transfer = Transfer::where('member_id', $member->id)
            ->where('status', 2) // เฉพาะรายการอนุมัติ
            ->latest('created_at')
            ->first();

        if ($last_transfer) {
            if ($last_transfer->type === 'deposit') {
                // ถ้า deposit แต่มีโปรโมชั่น applied
                if ($last_transfer->promotion_id > 0) {
                    Log::info("Cashback !! {$member->username} มียอดฝากก่อนหน้ารับโปร");
                    return;
                }
            } elseif ($last_transfer->type === 'withdraw') {
                // ถ้า withdraw ให้ return เลย
                Log::info("Cashback !! {$member->username} มียอดถอนก่อนหน้า");
                return;
            }
        }

        // if ($member->wallet_balance >= 1) {
        //     Log::info("Cashback !! {$member->username} มียอดคงเหลือมากกว่า 1");
        //     return;
        // }

        $total_lose = 0;
        try {
            $winlose = app(BetflixController::class)
                ->Single_Member_Report_all_Provider($member->username, -1, -1)
                ->winloss ?? 0;

            $total_lose = $winlose;
        } catch (\Exception $e) {
            Log::error('Error Betflix API : ' . $e->getMessage());
        }

        $cash_back = 0;
        if (abs($total_lose) > 0) {
            $setting = Setting::first();
            $cash_back = $setting ? (abs($total_lose) * ($setting->cashback_percent / 100)) : 0;
        }

        $cash_back = min($cash_back, 20000);

        Logs::create([
            'username' => $member->username,
            'log' => 'total_lose: ' . number_format($total_lose, 2) .
                     ' cash back: ' . number_format($cash_back, 2),
        ]);

        if ($cash_back > 0) {
            Log::info("Cashback ++ {$member->username} total_lose: " . number_format($total_lose, 2) . " cash back: " . number_format($cash_back, 2));

            Transfer::create([
                'member_id'     => $member->id,
                'amount'        => $cash_back,
                'status'        => 1,
                'status_code'   => 'รออนุมัติ',
                'type'          => 'cashback',
                'promotion'     => 'cashback',
                'old_balance'   => $member->wallet_balance,
                'new_balance'   => $member->wallet_balance + $cash_back,
                'transfer_date' => strtotime(now()),
            ]);
        }
    }
}
