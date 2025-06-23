<?php

// app/Http/Controllers/HashtagController.php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;

class HashtagController extends Controller
{

    public function index()
    {
        $links = Link::all();  // ดึงรายการ Links ทั้งหมดจากฐานข้อมูล
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
            'hashtags' => 'required|array', // รับค่าจากฟอร์มที่เป็น array
            'hashtags.*' => 'string', // ตรวจสอบให้แน่ใจว่าแต่ละค่าใน array เป็น string
        ]);

        // สร้างลิงก์พร้อมกับ Hashtags
        Link::create([
            'link' => $request->link,
            'hashtags' => $request->hashtags, // เก็บ Hashtags เป็น array
        ]);

        return redirect()->route('links.index')->with('success', 'Link and Hashtags added successfully');
    }

    public function edit($id)
    {
        $link = Link::findOrFail($id);
        return view('links.edit', compact('link'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'link' => 'required|url',
            'hashtags' => 'required|array',
            'hashtags.*' => 'string',
        ]);

        $link = Link::findOrFail($id);
        $link->update([
            'link' => $request->link,
            'hashtags' => $request->hashtags,
        ]);

        return redirect()->route('links.index')->with('success', 'Link and Hashtags updated successfully');
    }
}
