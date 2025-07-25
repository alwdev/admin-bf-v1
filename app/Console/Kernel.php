<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // กำหนดให้ 'cash_back' function ใน ManageMemberController ทำงานทุกวัน
        // โดยค่าเริ่มต้น daily() จะรันในเวลา 00:00 (เที่ยงคืน)
        $schedule->call('App\Http\Controllers\ManageMemberController@cash_back')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
