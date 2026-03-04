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

class RunAffiliate implements ShouldQueue
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

        $affiliate = Affiliate::first();
        $child_ids = json_decode($member->ref_user);
        if (!$child_ids) return;

        foreach ($child_ids as $child_id) {
            $child = Members::find($child_id);
            if (!$child) continue;

            $winlose = $this->getWinLose($child);

            if ($affiliate->is_enable_af_winlose == 1 && $winlose < 0) {
                $commission_level2 = abs($winlose) * ($affiliate->af_receive_percent_winlose_2 / 100);
                $this->createTransfer($member, $commission_level2);
            }

            $parents = Members::whereHasChild($member->id)->get();
            foreach ($parents as $parent) {
                if ($affiliate->is_enable_af_winlose == 1 && $winlose < 0) {
                    $commission_level3 = abs($winlose) * ($affiliate->af_receive_percent_winlose_3 / 100);
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
        $winlose = 0;
        try {
            $pg_total = app(\App\Http\Controllers\PgHardController::class)
                ->pg_get_spin_summaryby_user($member->username, -1, -1);
        } catch (\Exception $e) {
        }
        return $winlose;
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
            'promotion' => 'commission',
            'old_balance' => $member->wallet_balance,
            'new_balance' => $member->wallet_balance + $amount,
            'transfer_date' => strtotime(now()),
        ]);

        Logs::create([
            'username' => $member->username,
            'log' => 'commission: ' . number_format($amount, 2)
        ]);

        // อัพเดต wallet balance ของสมาชิก
        // $member->wallet_balance += $amount;
        // $member->save();
    }
}
