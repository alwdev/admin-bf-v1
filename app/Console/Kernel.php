<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\CashBackJob;
use App\Jobs\RunAffiliate;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Cashback ทุกวันเวลา 00:05
        $schedule->command('cashback:run')->dailyAt('00:05');

        // Affiliate ทุกวันเวลา 00:06
        $schedule->command('affiliate:run')->dailyAt('00:06');
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
