<?php

namespace App\Jobs;

use App\Models\Members;
use App\Models\Affiliate;
use App\Models\Transfer;
use App\Models\Logs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Telegram\TelegramMessage;
use Illuminate\Support\Facades\DB;

class CashBackLottoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $memberId;
    public $timeout = 3600;
    public $tries = 3;

    public function __construct($memberId)
    {
        $this->memberId = $memberId;
    }

    public function handle()
    {
        $member = Members::find($this->memberId);
        if (!$member) return;

        set_time_limit(3600);

        $com_ = 8;


        $child_ids = json_decode($member->ref_user);
        if (!$child_ids) return;

        foreach ($child_ids as $child_id) {
            $child = Members::find($child_id);
            if (!$child) continue;

            $winlose = $this->getWinLose($child);

            if ($winlose < 0) {
                $commission_level2 = abs($winlose) * ($com_ / 100);
                $this->createTransfer($member, $commission_level2);
            }

            $parents = Members::whereHasChild($member->id)->get();
            foreach ($parents as $parent) {
                if ($winlose < 0) {
                    $commission_level3 = abs($winlose) * ($com_ / 100);
                    $this->createTransfer($parent, $commission_level3);
                }
            }
        }
    }

    /**
     * ฟังก์ชันคำนวณยอดเล่น/ยอดเสียของสมาชิก
     */
    private function getWinLose($member)
    {
        try {


            $username = $member->username;
            $yesterdayStart = now()->subDay()->startOfDay();
            $yesterdayEnd = now()->subDay()->endOfDay();

            $totalAmount_bet = Logs::where('log', 'like', 'Lotto Bet placed%')
                ->where('log', 'like', '%' . $username . '%')
                ->whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])
                ->sum(DB::raw("CAST(SUBSTRING_INDEX(log, 'amount ', -1) AS DECIMAL(10, 2))"));

            $totalAmount_win = Logs::where('log', 'like', 'Lotto Win%')
                ->where('log', 'like', '%' . $username . '%')
                ->whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])
                ->sum(DB::raw("CAST(SUBSTRING_INDEX(log, 'amount ', -1) AS DECIMAL(10, 2))"));

            $winlose =  $totalAmount_win - $totalAmount_bet;

            return $winlose;
        } catch (\Exception $e) {
            Log::info('API Error for ' . $member->username . ': ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ฟังก์ชันสร้าง Transfer และอัพเดต wallet
     */
    private function createTransfer($member, $amount)
    {
        if ($amount <= 0) return;

        Transfer::create([
            'member_id' => $member->id,
            'amount' => $amount,
            'status' => 1,
            'status_code' => 'รออนุมัติ',
            'type' => 'commission',
            'promotion' => 'commission lotto',
            'old_balance' => $member->wallet_balance,
            'new_balance' => $member->wallet_balance + $amount,
            'transfer_date' => strtotime(now()),
        ]);

        // อัพเดต wallet balance ของสมาชิก
        // $member->wallet_balance += $amount;
        // $member->save();
    }
}
