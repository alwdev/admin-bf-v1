<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Members;
use App\Models\LottoTransaction; // สมมติว่ามี Model สำหรับรายการซื้อหวย

class CashBackLottoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $memberId;

    /**
     * สร้าง Job Instance ใหม่.
     *
     * @param int $memberId
     * @return void
     */
    public function __construct(int $memberId)
    {
        $this->memberId = $memberId;
    }

    /**
     * รัน Job.
     *
     * @return void
     */
    public function handle()
    {
        $member = Members::find($this->memberId);

        if (!$member) {
            return; // ไม่พบสมาชิก
        }

        // 1. กำหนดช่วงเวลาที่ต้องการคำนวณ (เช่น 1 สัปดาห์ที่ผ่านมา)
        $endDate = now();
        $startDate = now()->subWeek();

        // 2. ดึงยอดซื้อหวยในช่วงเวลาที่กำหนด
        $totalLottoBet = LottoTransaction::where('member_id', $member->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // 3. กำหนดอัตรา Cashback (สมมติ 5%)
        $cashbackRate = 0.05;
        $cashbackAmount = $totalLottoBet * $cashbackRate;

        if ($cashbackAmount > 0) {
            // 4. ทำการเพิ่มยอดเงิน/บันทึกรายการ
            // $member->wallet += $cashbackAmount;
            // $member->save();

            // 5. บันทึก Log หรือ Transaction (สำคัญมาก!)
            \Log::info("Member ID: {$member->id} received lotto cashback: {$cashbackAmount}");
            // Logs::create([...]); // บันทึกในตาราง Logs ของคุณ

            // ... อาจจะส่ง Notification ส่วนตัวหาสมาชิกด้วย
        }
    }
}
