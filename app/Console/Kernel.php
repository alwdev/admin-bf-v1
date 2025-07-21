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
        // $schedule->command('inspire')->hourly();

        // แก้ไขตรงนี้: เพิ่ม ->weekly()
        // โดยค่าเริ่มต้น weekly() จะรันในวันอาทิตย์ เวลา 00:00 (เที่ยงคืน)
        $schedule->call('App\Http\Controllers\ManageMemberController@cash_back')->weekly();

        // หากต้องการระบุวันและเวลาที่แน่นอน (เช่น ทุกวันจันทร์ เวลา 9 โมงเช้า)
        // $schedule->call('App\Http\Controllers\ManageMemberController@cash_back')->weeklyOn(1, '09:00'); // 1 = Monday

        // หากต้องการระบุวันและเวลาที่แน่นอน (เช่น ทุกวันศุกร์ เวลา 17:30 น.)
        // $schedule->call('App\Http\Controllers\ManageMemberController@cash_back')->weeklyOn(5, '17:30'); // 5 = Friday

        // $schedule->call('App\Http\Controllers\HistoryController@get_supergame');
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
