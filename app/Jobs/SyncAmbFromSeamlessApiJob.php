<?php

namespace App\Jobs;

use App\Services\AmbSeamlessSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncAmbFromSeamlessApiJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 3600;

    public function handle(AmbSeamlessSyncService $sync): void
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }
        $stats = $sync->syncAll();
        Log::info('AMB seamless sync finished', $stats);
    }
}
