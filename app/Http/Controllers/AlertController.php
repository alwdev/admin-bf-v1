<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AlertController extends Controller
{
    /**
     * ตรวจสอบและดึงข้อมูลแจ้งเตือนสำหรับ SweetAlert
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSystemAlert()
{
    // กำหนดวันที่ปัจจุบัน
    $today = Carbon::today()->toDateString();
    // กำหนดเวลาปัจจุบัน
    $now_time = Carbon::now()->format('H:i:s');

    // ค้นหาข้อมูลแจ้งเตือนที่ active เป็น 1
    // และวันที่แจ้งเตือนน้อยกว่าหรือเท่ากับวันที่ปัจจุบัน
    // และเวลาแจ้งเตือนน้อยกว่าหรือเท่ากับเวลาปัจจุบัน
    $alerts = DB::table('system_alert')
        ->where('date', '<=', $today)
        ->where('active', 1)
        ->whereTime('time', '<=', $now_time) // เพิ่มเงื่อนไขการเช็คเวลา
        ->get();

    if ($alerts->count() > 0) {
        $messages = [];
        foreach ($alerts as $alert) {
            $messages[] = $alert->message;
        }

        return response()->json([
            'status' => 'success',
            'messages' => $messages
        ]);
    }

    return response()->json([
        'status' => 'no_alert'
    ]);
}
}
