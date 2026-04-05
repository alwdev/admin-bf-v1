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
        // หา record แรก (ไม่สร้างใหม่)
        $homePage = HomePage::first();

        if (!$homePage) {
            // ถ้าไม่มี record → redirect หรือแจ้ง admin
            return redirect()->back()->with('error', 'HomePage record not found. Please create it first.');
        }

        return view('homepage.edit', compact('homePage'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:65535',
            'meta_keywords' => 'nullable|string|max:65535',
            'content_html' => 'required|string',
            'active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $validator->errors()->all()));
        }

        // 2. หา record แรก
        $homePage = HomePage::first();

        if (!$homePage) {
            // ถ้าไม่มี record แสดง error
            return redirect()->back()->with('error', 'HomePage record not found.');
        }

        // 3. Assign ทีละฟิลด์
        $homePage->meta_title = $request->meta_title;
        $homePage->meta_description = $request->meta_description;
        $homePage->meta_keywords = $request->meta_keywords;
        $homePage->active = $request->active;

        $currentContent = is_array($homePage->content) ? $homePage->content : [];
        $currentContent['main_html'] = $request->content_html;
        $homePage->content = $currentContent;

        // 4. เพิ่ม revision
        $homePage->revision += 1;

        // 5. Save
        $homePage->save();

        return redirect()->route('homepage.edit')->with('success', 'HomePage content updated successfully.');
    }
}
