<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\PartnerCommissionJob;

class PartnerCommission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partner:commission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch partner commission job';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // // Dispatch job ไป queue
        // set_time_limit(0);

        // Log::info("Run Check Partner Commission");

        // $partners = Partner::all();
        // Log::info("Total Partner: ".count($partners));

        // foreach ($partners as $partner) {
        //     ProcessPartnerCommission::dispatch($partner);
        // }

        // Log::info('Dispatched all partner commissions');

        PartnerCommissionJob::dispatch();

        $this->info('Partner commission job dispatched!');
    }
}
