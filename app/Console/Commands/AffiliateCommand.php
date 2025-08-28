<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\RunAffiliate;
use App\Models\Members;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Telegram\TelegramMessage;

class AffiliateCommand extends Command
{
    protected $signature = 'affiliate:run';
    protected $description = 'Run affiliate process for all members';

    public function handle()
    {
        // แบ่งสมาชิกเป็น batch 100 คน
            // แจ้งเริ่มงาน
        // Log::info('Run affiliate Job started');
        Logs::create(['log' => 'Run affiliate Job started']);
        TelegramMessage::create()
            ->to(env('TELEGRAM_G_ID'))
            ->line(env('APP_NAME'))
            ->line('BOT เริ่มทำการ affiliate')
            ->send();

        Members::whereNotNull('ref_user')->chunk(100, function ($members) {
            foreach ($members as $member) {
                RunAffiliate::dispatch($member->id);
            }
        });

        $this->info('Affiliate Jobs dispatched for all members!');

                // แจ้งสิ้นสุด
        // Log::info('Run affiliate Job finished');
        Logs::create(['log' => 'Run affiliate Job finished']);
        TelegramMessage::create()
            ->to(env('TELEGRAM_G_ID'))
            ->line(env('APP_NAME'))
            ->line('BOT สิ้นสุดการ Run affiliate')
            ->send();
    }
}
