<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CkeditorController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $uploadedFile = $request->file('upload');
            $originName = $uploadedFile->getClientOriginalName();

            // 1. สร้างชื่อไฟล์ใหม่ที่ไม่ซ้ำกัน
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $uploadedFile->getClientOriginalExtension();
            $fileNameToStore = $fileName . '_' . time() . '.' . $extension;

            // 2. กำหนด Folder ปลายทางใน Public
            $destinationPath = public_path('uploads/ckeditor');

            // 3. ย้ายไฟล์ไปที่ Public Folder โดยตรง
            // Note: ต้องมีสิทธิ์ในการเขียนไฟล์ (write permission) ในโฟลเดอร์นี้
            $uploadedFile->move($destinationPath, $fileNameToStore);

            // 4. สร้าง URL ที่เข้าถึงได้
            $url = asset('uploads/ckeditor/' . $fileNameToStore);

            // 5. ส่ง JSON Response กลับไปให้ CKEditor
            return response()->json([
                'uploaded' => 1,
                'fileName' => $fileNameToStore,
                'url' => $url
            ]);
        }

        // กรณีอัปโหลดล้มเหลว
        return response()->json([
            'uploaded' => 0,
            'error' => ['message' => 'File upload failed.']
        ]);
    }
}
