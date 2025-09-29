<?php

namespace App\Http\Controllers;

use App\Models\HomePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomePageController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     * จะทำหน้าที่เป็นทั้งหน้า "แสดง" และ "แก้ไข"
     */
    public function edit()
    {
        // ค้นหา record ที่ active ถ้าไม่มี ให้สร้าง instance ใหม่
        $homePage = HomePage::firstOrNew(['active' => true]);

        // หากเป็น record ใหม่ (เพิ่งสร้าง instance) ให้กำหนดค่าเริ่มต้นและบันทึก
        if (!$homePage->exists) {
            $homePage->meta_title = 'Default Home Page Title';
            // ... กำหนดค่าเริ่มต้นอื่นๆ
            $homePage->content = ['main_html' => '<p>Welcome to our website! Edit this content.</p>'];
            $homePage->save(); // **นี่คือส่วนที่สร้างแถวใน DB**
        }

        return view('homepage.edit', compact('homePage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // 1. กำหนดกฎ Validation
        $validator = Validator::make($request->all(), [
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:65535',
            'meta_keywords' => 'nullable|string|max:65535',
            'content_html' => 'required|string', // ใช้ชื่อ 'content_html' ตาม input field ใน form
            'active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            $errorsHtml = '<ul>';
            foreach ($validator->errors()->all() as $error) {
                $errorsHtml .= '<li>' . $error . '</li>';
            }
            $errorsHtml .= '</ul>';

            return redirect()->back()->withInput()->with('error', $errorsHtml);
        }

        // 2. ค้นหา record ที่ active
        $homePage = HomePage::firstOrFail();

        // 3. จัดเตรียมข้อมูลสำหรับอัปเดต
        $data = $request->only(['meta_title', 'meta_description', 'meta_keywords', 'active']);

        // เนื่องจาก content ถูก cast เป็น JSON เราต้องจัดเก็บ HTML เข้าไปในโครงสร้าง JSON
        $currentContent = is_array($homePage->content) ? $homePage->content : [];
        $currentContent['main_html'] = $request->content_html;

        $data['content'] = $currentContent;
        $data['revision'] = $homePage->revision + 1; // อัปเดต Revision

        // 4. อัปเดตข้อมูล
        $homePage->update($data);

        return redirect()->route('homepage.edit')->with('success', 'HomePage content updated successfully.');
    }
}
