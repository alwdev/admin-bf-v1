<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\RunAffiliate;

class AffiliateCommand extends Command
{
    protected $signature = 'affiliate:run';
    protected $description = 'Run affiliate process';

    public function handle()
    {
        RunAffiliate::dispatch();
        $this->info('Affiliate Job dispatched!');
    }
}
