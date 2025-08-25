<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\RunAffiliate;
use App\Models\Members;

class AffiliateCommand extends Command
{
    protected $signature = 'affiliate:run';
    protected $description = 'Run affiliate process for all members';

    public function handle()
    {
        // แบ่งสมาชิกเป็น batch 100 คน
        Members::whereNotNull('ref_user')->chunk(100, function ($members) {
            foreach ($members as $member) {
                RunAffiliate::dispatch($member->id);
            }
        });

        $this->info('Affiliate Jobs dispatched for all members!');
    }
}
