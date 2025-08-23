<?php

namespace App\Http\Controllers;

use App\Models\Members;
use App\Models\Affiliate;

class AffiliateController extends Controller
{
    /**
     * ดำเนินการคำนวณค่าคอมมิชชั่น Affiliate โดยไม่บันทึกข้อมูล
     *
     * @param int $memberId
     * @return \Illuminate\Http\Response
     */
    public function runAffiliateDryRun($memberId)
    {
        set_time_limit(3600);
        $transfers = [];

        $affiliate = Affiliate::first();
        $member = Members::find($memberId);

        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Member not found.'], 404);
        }

        $child_ids = json_decode($member->ref_user);
        if ($child_ids) {
            foreach ($child_ids as $child_id) {
                $child = Members::find($child_id);
                if (!$child) {
                    continue;
                }

                // --- คำนวณยอดเล่น/เสียของ child ---
                $winlose = $this->getWinLose($child);

                // --- member ได้ commission level2 ถ้า child เล่นเสีย ---
                if ($affiliate->is_enable_af_winlose == 1 && $winlose < 0) {
                    $commission_level2 = abs($winlose) * ($affiliate->af_receive_percent_winlose_2 / 100);
                    $transfers[] = $this->createTransferData($member, $commission_level2, 'Level 2 commission');
                }

                // --- ตรวจสอบว่า member เป็น ref_user ของใครอีก (ผู้แนะนำชั้นบน) ---
                $parents = Members::whereHasChild($member->id)->get();
                foreach ($parents as $parent) {
                    if ($affiliate->is_enable_af_winlose == 1 && $winlose < 0) {
                        $commission_level3 = abs($winlose) * ($affiliate->af_receive_percent_winlose_3 / 100);
                        $transfers[] = $this->createTransferData($parent, $commission_level3, 'Level 3 commission');
                    }
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Affiliate data calculated without saving or logging.',
            'transfers' => $transfers,
        ], 200);
    }

    /**
     * ฟังก์ชันคำนวณยอดเล่น/ยอดเสียของสมาชิก (จำลอง)
     */
    private function getWinLose($member)
    {
        // โค้ดส่วนนี้ยังคงเรียกใช้ API เพื่อดึงข้อมูลจริงมาคำนวณ
        try {
            $bf_total = app(\App\Http\Controllers\BetflixController::class)
                ->Single_Member_Report_all_Provider($member->username, -1, -1);
            $winlose = $bf_total->winloss ?? 0;

            $pg_total = app(\App\Http\Controllers\PgHardController::class)
                ->pg_get_spin_summaryby_user($member->username, -1, -1);
            if (isset($pg_total['data'][0]['winloseAmount'])) {
                 $winlose += $pg_total['data'][0]['winloseAmount'];
            }
            return $winlose;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * ฟังก์ชันสร้างข้อมูล Transfer สำหรับการแสดงผล (ไม่บันทึก)
     */
    private function createTransferData($member, $amount, $description)
    {
        if ($amount <= 0) {
            return null;
        }

        return [
            'member_id' => $member->id,
            'member_username' => $member->username,
            'amount' => $amount,
            'description' => $description,
        ];
    }
}
