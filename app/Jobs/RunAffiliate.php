<?php

namespace App\Jobs;

use App\Models\Members;
use App\Models\Affiliate;
use App\Models\Transfer;
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

    public $timeout = 3600; // 1 ชั่วโมง
    public $tries = 3;

    public function __construct()
    {
        // ไม่มี parameter
    }

    public function handle()
    {
        set_time_limit(3600);

        // แจ้งเริ่มงาน
        Log::info('Run affiliate Job started');
        TelegramMessage::create()
            ->to(env('TELEGRAM_G_ID'))
            ->line(env('APP_NAME'))
            ->line('BOT เริ่มทำการ affiliate')
            ->send();

        $affiliate = Affiliate::first();
        $members = Members::where('ref_user', '!=', null)->get();
        Log::info('Total Members with ref_user: ' . count($members));

        foreach ($members as $member) {

            $child_ids = json_decode($member->ref_user);
            if (!$child_ids) continue;

            foreach ($child_ids as $child_id) {
                $child = Members::find($child_id);
                if (!$child) continue;

                // --- คำนวณยอดเล่น/เสียของ child ---
                $winlose = $this->getWinLose($child);

                // --- member ได้ commission level2 ถ้า child เล่นเสีย ---
                if ($affiliate->is_enable_af_winlose == 1 && $winlose < 0) {
                    $commission_level2 = abs($winlose) * ($affiliate->af_receive_percent_winlose_2 / 100);
                    $this->createTransfer($member, $commission_level2);
                    Log::info("Level2 commission for member {$member->username} from child {$child->username}: {$commission_level2}");
                }

                // --- ตรวจสอบว่า member เป็น ref_user ของใครอีก (ผู้แนะนำชั้นบน) ---
                $parents = Members::whereHasChild($member->id)->get();
                foreach ($parents as $parent) {
                    if ($affiliate->is_enable_af_winlose == 1 && $winlose < 0) {
                        $commission_level3 = abs($winlose) * ($affiliate->af_receive_percent_winlose_3 / 100);
                        $this->createTransfer($parent, $commission_level3);
                        Log::info("Level3 commission for parent {$parent->username} from member {$member->username}: {$commission_level3}");
                    }
                }
            }
        }

        // แจ้งสิ้นสุด
        Log::info('Run affiliate Job finished');
        TelegramMessage::create()
            ->to(env('TELEGRAM_G_ID'))
            ->line(env('APP_NAME'))
            ->line('BOT สิ้นสุดการ Run affiliate')
            ->send();
    }

    /**
     * ฟังก์ชันคำนวณยอดเล่น/ยอดเสียของสมาชิก
     */
    private function getWinLose($member)
    {
        try {
            $bf_total = app(\App\Http\Controllers\BetflixController::class)
                ->Single_Member_Report_all_Provider($member->username, -1, -1);
            $total_bet = $bf_total->valid_amount ?? 0;
            $winlose = $bf_total->winloss ?? 0;

            $pg_total = app(\App\Http\Controllers\PgHardController::class)
                ->pg_get_spin_summaryby_user($member->username, -1, -1);
            if (isset($pg_total['data'][0]['totalAmount'])) {
                $total_bet += $pg_total['data'][0]['totalAmount'];
            }

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
            'promotion' => 'commission',
            'old_balance' => $member->wallet_balance,
            'new_balance' => $member->wallet_balance + $amount,
            'transfer_date' => strtotime(now()),
        ]);

        // อัพเดต wallet balance ของสมาชิก
        $member->wallet_balance += $amount;
        $member->save();
    }
}
