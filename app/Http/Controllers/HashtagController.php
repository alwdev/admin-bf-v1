<?php

// app/Http/Controllers/HashtagController.php

namespace App\Http\Controllers;

use App\Models\Hashtag;
use Illuminate\Http\Request;

class HashtagController extends Controller
{

    public function index()
    {
        $links = Hashtag::all();  // ดึงรายการ Links ทั้งหมดจากฐานข้อมูล
        return view('links.index', compact('links')); // ส่งข้อมูลไปที่หน้า index
    }

    // app/Http/Controllers/HashtagController.php

    public function create()
    {
        return view('links.create');  // โหลดหน้า create
    }

    public function store(Request $request)
    {
        $request->validate([
            'link' => 'required|url',
            'hashtags' => 'required|string', // ต้องเป็น string
        ]);
        // สร้างลิงก์พร้อมกับ Hashtags
        Hashtag::create([
            'link' => $request->link,
            'hashtag' => $request->hashtags, // เก็บ Hashtags เป็น array ในรูปแบบ JSON
        ]);

        return redirect()->route('links.index')->with('success', 'Link and Hashtags added successfully');
    }


    public function edit($id)
    {
        $link = Hashtag::findOrFail($id);  // ค้นหาข้อมูลที่ต้องการแก้ไขจากฐานข้อมูล
        return view('links.edit', compact('link'));  // ส่งข้อมูลไปที่หน้าฟอร์มแก้ไข
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'link' => 'required|url',
            'hashtags' => 'required|string',  // ตรวจสอบให้ hashtags เป็น string
        ]);

        $link = Hashtag::findOrFail($id);  // ค้นหาข้อมูลที่ต้องการแก้ไขจากฐานข้อมูล

        // อัปเดตข้อมูลในฐานข้อมูล
        $link->update([
            'link' => $request->link,
            'hashtag' => $request->hashtags,  // แปลง array ของ hashtags กลับเป็น JSON
        ]);

        return redirect()->route('links.index')->with('success', 'Link and Hashtags updated successfully');
    }

    public function destroy($id)
    {
        // ค้นหาข้อมูลที่ต้องการลบ
        $link = Hashtag::findOrFail($id);

        // ลบข้อมูล
        $link->delete();

        // ส่งข้อความสำเร็จกลับไปยังหน้ารายการ
        return redirect()->route('links.index')->with('success', 'Link and Hashtags deleted successfully');
    }


}
