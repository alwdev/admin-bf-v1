<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CashBackJob;

class CashBackCommand extends Command
{
    protected $signature = 'cashback:run';
    protected $description = 'Run cashback process for members';

    public function handle()
    {
        // แทนที่จะเรียก controller ให้ dispatch job
        CashBackJob::dispatch();

        $this->info('Cashback Job dispatched to queue!');
    }
}
