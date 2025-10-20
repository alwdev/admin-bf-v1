<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CashBackLottoJob; // ต้องมี Job ตัวนี้อยู่แล้วตามที่เราสร้างในขั้นตอนก่อนหน้า
use App\Models\Members;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Telegram\TelegramMessage; // หากคุณต้องการใช้ Telegram
use App\Models\Logs; // หากคุณต้องการใช้ Log Model

class CashBackLottoCommand extends Command
{
    /**
     * ชื่อคำสั่งสำหรับรันบน Artisan (e.g. php artisan cashback:lotto)
     *
     * @var string
     */
    protected $signature = 'cashback:lotto';

    /**
     * คำอธิบายของคำสั่ง.
     *
     * @var string
     */
    protected $description = 'Dispatch jobs for calculating and running lotto cashback for all members.';

    /**
     * รัน Command.
     *
     * @return int
     */
    public function handle()
    {
        // ------------------------------------
        // ส่วนที่ 1: การแจ้งเตือนเริ่มต้น
        // ------------------------------------
        $this->info('Starting Lotto Cashback Dispatch...');

        // ตัวอย่างการส่งแจ้งเตือน Telegram (นำมาจากโครงสร้างเดิมของคุณ)
        try {
            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line(env('APP_NAME'))
                ->line('BOT เริ่มทำการ Cashback Lotto')
                ->send();
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram start notification: ' . $e->getMessage());
        }

        // ------------------------------------
        // ส่วนที่ 2: การวนลูปและ Dispatch Jobs
        // ------------------------------------
        $totalDispatched = 0;

        // ใช้ chunk เพื่อดึงสมาชิกออกมาเป็นชุด (Batch) เพื่อประหยัดหน่วยความจำ
        Members::chunk(100, function ($members) use (&$totalDispatched) {
            foreach ($members as $member) {
                // Dispatch Job ไปยัง Queue
                CashBackLottoJob::dispatch($member->id);
                $totalDispatched++;
            }
            // สามารถเพิ่มหน่วงเวลาเล็กน้อยตรงนี้ (usleep) หากต้องการลดภาระฐานข้อมูล
        });

        // ------------------------------------
        // ส่วนที่ 3: การแจ้งเตือนสิ้นสุด
        // ------------------------------------
        $this->info('Lotto Cashback Job dispatched for ' . $totalDispatched . ' members!');

        // ตัวอย่างการส่งแจ้งเตือน Telegram สิ้นสุด
        try {
            TelegramMessage::create()->to(env('TELEGRAM_G_ID'))
                ->line(env('APP_NAME'))
                ->line('BOT สิ้นสุดการ Cashback Lotto')
                ->line("ส่ง Job เข้า Queue ทั้งหมด: {$totalDispatched} งาน")
                ->send();
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram end notification: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
