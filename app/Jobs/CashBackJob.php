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
use NotificationChannels\Telegram\TelegramMessage;

class CashBackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        // ถ้าต้องส่งค่าอะไรเข้ามา job สามารถใส่ใน constructor
    }

    public function handle()
    {
        set_time_limit(300000000);
        Log::info('Run cash_back');
        TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
            ->line(env('APP_NAME'))
            ->line('BOT เริ่มทำการ Cashback')
            ->send();

        $members = Members::get();

        foreach ($members as $member) {
            sleep(1);
            $last_deposit = Transfer::where('member_id', $member->id)
                ->where('status', 2)
                ->where('promotion_id', '>', 0)
                ->where('type', 'deposit')
                ->whereDate('created_at', Carbon::now()->subDays(7))
                ->get();

            if ($last_deposit->count() > 0) {
                Log::info('Cashback !! member  = ' . $member->username . ' มียอดฝากก่อนหน้ารับโปร');
                continue;
            }

            $last_withdraw = Transfer::where('member_id', $member->id)
                ->where('status', 2)
                ->where('type', 'withdraw')
                ->whereDate('created_at', Carbon::now()->subDays(7))
                ->get();

            if ($last_withdraw->count() > 0) {
                Log::info('Cashback !! member  = ' . $member->username . ' มียอดถอนก่อนหน้า');
                continue;
            }

            if ($member->wallet_balance >= 1) {
                Log::info('Cashback !! member  = ' . $member->username . ' มียอดคงเหลือมากกว่า 1');
                continue;
            }

            $total_lose = 0;
            $cash_back = 0;
            try {
                $winlose = app(BetflixController::class)->Single_Member_Report_all_Provider($member->username, -1, -1)->winloss;

                $total_lose = $winlose ?? 0;
            } catch (\Exception $e) {
                Log::error('Error Betflix API : ' . $e->getMessage());
                $total_lose = 0;
            }

            if (abs($total_lose) > 0) {
                $setting = Setting::first();
                $cash_back = $setting ? (abs($total_lose) * ($setting->cashback_percent / 100)) : 0;
            }

            $cash_back = min($cash_back, 20000);

            Logs::create([
                'username' => $member->username,
                'log' => 'total_lose: ' . number_format($total_lose, 2) . ' cash back: ' . number_format($cash_back, 2),
            ]);

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
            }
        }

        TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
            ->line(env('APP_NAME'))
            ->line('BOT สิ้นสุดการ Cashback')
            ->send();

        Log::info('End Cashback');
    }
}
