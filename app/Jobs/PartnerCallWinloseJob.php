<?php

namespace App\Jobs;

use App\Models\Partner;
use App\Models\Members;
use App\Models\PartnerCommission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PartnerCallWinloseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $partner_id;

    /**
     * Create a new job instance.
     */
    public function __construct($partner_id = null)
    {
        $this->partner_id = $partner_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $membersData = [];

        if ($this->partner_id) {
            $partners = Partner::where('id', $this->partner_id)->get();
        } else {
            $partners = Partner::all();
        }

        foreach ($partners as $partner) {
            $total_commission = 0;

            if ($partner->members !== null) {
                $commissions = [];
                foreach (json_decode($partner->members) as $_member) {
                    $under_member = Members::where('id', $_member)->first();
                    if (!$under_member) {
                        continue;
                    }

                    $total_bet = 0;
                    $winlose = 0;

                    $total_bet += 0;
                    $winlose += 0;

                    try {
                        $pg_total_bet = app(\App\Http\Controllers\PgHardController::class)->pg_get_spin_summaryby_user($under_member->username, -1, -1);
                        if (isset($pg_total_bet['data']) && count($pg_total_bet['data']) > 0) {
                            $total_bet += $pg_total_bet['data'][0]['totalAmount'];
                        }
                    } catch (\Exception $e) {
                        // skip
                    }

                    if ($total_bet > 1 && $winlose < 0) {
                        $commission = abs($winlose) * ($partner->rate / 100);
                        $total_commission += $commission;

                        $start_date = date('Y-m-d', strtotime('-1 day'));
                        $end_date = date('Y-m-d', strtotime('-1 day'));

                        $membersData[] = [
                            'total_bet' => $total_bet,
                            'winlose' => $winlose,
                            'rate' => $partner->rate,
                            'partner_id' => $partner->id,
                            'member_id' => $under_member->id,
                            'member_username' => $under_member->username,
                            'date1' => $start_date,
                            'date2' => $end_date,
                            'commission' => $commission,
                            'total_commission' => $total_commission,
                        ];

                        // เก็บ commission ลง array ก่อน batch insert
                        $commissions[] = [
                            'partner_id' => $partner->id,
                            'amount' => $total_commission,
                            'payment_type' => 'Commission',
                            'payment_status' => 'pending',
                            'transaction_id' => '',
                            'note' => $start_date . '-' . $end_date,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                // batch insert
                if (!empty($commissions)) {
                    $partner->total_profit = ($partner->total_profit ?? 0) + $total_commission;
                    $partner->save();
                    PartnerCommission::insert($commissions);
                }
            }
        }
    }
}
