<?php

namespace App\Http\Controllers;

use App\Jobs\SyncAmbFromSeamlessApiJob;
use Illuminate\Http\Request;

class AmbSeamlessSyncController extends Controller
{
    public function dispatchSync(Request $request)
    {
        SyncAmbFromSeamlessApiJob::dispatch();

        $queue = config('queue.default');
        $msg = 'เริ่มซิงค์จาก Seamless API แล้ว (categories → products → games ตามทุก provider)';
        if ($queue === 'sync') {
            return redirect()
                ->back()
                ->with('success', $msg)
                ->with(
                    'warning',
                    'QUEUE_CONNECTION=sync จะรันซิงค์ในคำขอเดียวกันและอาจใช้เวลานาน — แนะนำตั้งเป็น database หรือ redis แล้วรัน queue worker'
                );
        }

        return redirect()->back()->with(
            'success',
            $msg . ' — รัน `php artisan queue:work` บนเซิร์ฟเพื่อประมวลผล'
        );
    }
}
