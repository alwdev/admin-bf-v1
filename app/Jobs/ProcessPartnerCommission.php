<?php

namespace App\Jobs;

use App\Models\Partner;
use App\Models\Members;
use App\Models\PartnerCommission;
use App\Models\Logs;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPartnerCommission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $partner;

    /**
     * Create a new job instance.
     */
    public function __construct(Partner $partner)
    {
        $this->partner = $partner;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $partner = $this->partner;
        $total_commission = 0;

        $members = json_decode($partner->members, true);
        if (!$members) {
            return;
        }

        Log::info("Process Partner: {$partner->contanct_name} (ID: {$partner->id})");
        Logs::create([
            'log' => "Process Partner: {$partner->contanct_name} (ID: {$partner->id})"
        ]);

        foreach ($members as $_member) {
            $under_member = Members::find($_member);
            if (!$under_member) {
                continue;
            }

            $total_bet = 0;
            $winlose = 0;

            $total_bet = 0;
            $winlose = 0;

            // PG
            try {
                $pg_total_bet = app(\App\Http\Controllers\PgHardController::class)
                    ->pg_get_spin_summaryby_user($under_member->username, -1, -1);

                if (isset($pg_total_bet['data']) && count($pg_total_bet['data']) > 0) {
                    $total_bet += $pg_total_bet['data'][0]['totalAmount'];
                }
            } catch (\Exception $e) {
                Log::warning("PgHard API Error : ".$e->getMessage());
            }

            if ($total_bet > 1 && $winlose < 0) {
                $commission = abs($winlose) * ($partner->rate / 100);
                $total_commission += $commission;

                Log::info("Member {$under_member->username} winlose={$winlose}, commission={$commission}");
            }
        }

        if ($total_commission > 0) {
            $partner->total_profit += $total_commission;
            $partner->save();

            $start_date = date('Y-m-d', strtotime('-1 day'));
            $end_date   = date('Y-m-d', strtotime('-1 day'));

            PartnerCommission::create([
                'partner_id'     => $partner->id,
                'amount'         => $total_commission,
                'payment_type'   => 'Commission',
                'payment_status' => 'pending',
                'transaction_id' => '',
                'note'           => $start_date . '-' . $end_date
            ]);

            Logs::create([
                'log' => "Partner {$partner->contanct_name} ได้ commission {$total_commission}"
            ]);
        }
    }
}
