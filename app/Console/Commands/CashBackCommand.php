<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CashBackJob;
use App\Models\Members;

class CashBackCommand extends Command
{
    protected $signature = 'cashback:run';
    protected $description = 'Run cashback process for members';

    public function handle()
    {
        // ตัวสั่งงาน (Command/Controller)
        TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
            ->line(env('APP_NAME'))
            ->line('BOT เริ่มทำการ Cashback')
            ->send();

        Members::chunk(100, function ($members) {
            foreach ($members as $member) {
                CashBackJob::dispatch($member->id);
            }
        });

        TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
            ->line(env('APP_NAME'))
            ->line('BOT สิ้นสุดการ Cashback')
            ->send();

        $this->info('Cashback Job dispatched to queue!');
    }
}
