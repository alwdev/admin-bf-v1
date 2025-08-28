<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Partner;
use App\Jobs\ProcessPartnerCommission;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Telegram\TelegramMessage;

class PartnerCommissionCommand extends Command
{
    protected $signature = 'partner:run-commission';
    protected $description = 'Dispatch ProcessPartnerCommission jobs for all partners using chunk';

    public function handle()
    {
        set_time_limit(0);

        // Log::info("Run Check Partner Commission");
        Logs::create(['log' => 'BOT เริ่มทำการ ส่วนแบ่ง Partner']);

        try {
            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line(env('APP_NAME'))
                ->line('BOT เริ่มทำการ ส่วนแบ่ง Partner')
                ->send();
        } catch (\Exception $e) {
            Log::error('Telegram error: '.$e->getMessage());
        }

        Logs::create(['log' => 'BOT เริ่มทำการ ส่วนแบ่ง Partner']);

        Partner::chunk(100, function ($partners) {
            foreach ($partners as $partner) {
                ProcessPartnerCommission::dispatch($partner); // ส่งไป queue ทีละ partner
                // Log::info("Dispatched Partner ID: {$partner->id}");
                Logs::create(['log' => 'Dispatched Partner ID: '.$partner->id]);
            }
        });

        // Log::info('Dispatched all partner commissions');
        Logs::create(['log' => 'Dispatched all partner commissions']);

        try {
            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line(env('APP_NAME'))
                ->line('BOT สิ้นสุดการ Run ส่วนแบ่ง Partner')
                ->send();
        } catch (\Exception $e) {
            Log::error('Telegram error: '.$e->getMessage());
        }

        $this->info('✅ Dispatched all partner commissions successfully');
    }
}
