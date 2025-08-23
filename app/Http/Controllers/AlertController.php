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

        // ค้นหาข้อมูลแจ้งเตือนที่ active เป็น 1
        // และวันที่แจ้งเตือนน้อยกว่าหรือเท่ากับวันที่ปัจจุบัน
        // ใช้ get() เพื่อดึงข้อมูลทั้งหมดที่ตรงกับเงื่อนไข
        $alerts = DB::table('system_alert')
                    ->where('date', '<=', $today)
                    ->where('active', 1)
                    ->get();

        // ถ้าพบข้อมูลแจ้งเตือนมากกว่า 0 รายการ
        if ($alerts->count() > 0) {
            // สร้าง array เพื่อเก็บข้อความแจ้งเตือนทั้งหมด
            $messages = [];
            foreach ($alerts as $alert) {
                $messages[] = $alert->message;
            }

            return response()->json([
                'status' => 'success',
                'messages' => $messages
            ]);
        }

        // ถ้าไม่พบข้อมูลแจ้งเตือน
        return response()->json([
            'status' => 'no_alert'
        ]);
    }
}
