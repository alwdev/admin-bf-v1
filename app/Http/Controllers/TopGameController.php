<?php

namespace App\Http\Controllers;

use App\Models\TopGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // Import File facade for public disk operations
use Illuminate\Support\Facades\Validator; // ต้องเพิ่ม
use Illuminate\Validation\ValidationException; // ต้องเพิ่ม

class TopGameController extends Controller
{
    private $imagePath = 'top_game_images'; // Sub-folder inside public directory

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topGames = TopGame::orderBy('order_by', 'asc')->get();

        return view('top_games.index', compact('topGames'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'link' => 'required|max:255',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'order_by' => 'nullable|integer',
            'active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            // ดึงข้อความ Error ทั้งหมดมารวมกันในรูปแบบรายการ HTML
            $errorsHtml = '<ul>';
            foreach ($validator->errors()->all() as $error) {
                dd($error);
                $errorsHtml .= '<li>' . $error . '</li>';
            }
            $errorsHtml .= '</ul>';

            // Redirect กลับไปพร้อม Session 'error' ที่มีรายการข้อความ HTML
            return redirect()->back()->withInput()->with('error', $errorsHtml);
        }

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');

            // Generate a unique file name
            $filename = time() . '_' . $imageFile->getClientOriginalName();

            // Move file to public/top_game_images
            $imageFile->move(public_path($this->imagePath), $filename);

            // Store the path relative to the public directory
            $data['image'] = $this->imagePath . '/' . $filename;
        }

        TopGame::create($data);

        return redirect()->route('top_games.index')->with('success', 'Top Game ได้ถูกเพิ่มเรียบร้อยแล้ว');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // 1. ค้นหา Model ที่ต้องการอัปเดต
        $game = TopGame::findOrFail($id);

        // 2. กำหนดกฎ Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'link' => 'required|max:255',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'order_by' => 'nullable|integer',
            'active' => 'required|boolean',
        ]);

        // 3. จัดการเมื่อ Validation ไม่ผ่าน
        if ($validator->fails()) {
            // ดึงข้อความ Error ทั้งหมดมารวมกันในรูปแบบรายการ HTML
            $errorsHtml = '<ul>';
            foreach ($validator->errors()->all() as $error) {
                $errorsHtml .= '<li>' . $error . '</li>';
            }
            $errorsHtml .= '</ul>';

            // Redirect กลับไปพร้อม Session 'error' ที่มีรายการข้อความ HTML
            return redirect()->back()->withInput()->with('error', $errorsHtml);
        }
        $data = $request->except(['_token', '_method', 'id', 'image']);

        if ($request->hasFile('image')) {
            // ลบรูปเก่าออกก่อน
            if ($game->image && File::exists(public_path($game->image))) {
                File::delete(public_path($game->image));
            }

            $imageFile = $request->file('image');
            $filename = time() . '_' . $imageFile->getClientOriginalName();

            // Move file to public/top_game_images
            $imageFile->move(public_path($this->imagePath), $filename);

            // Store the path
            $data['image'] = $this->imagePath . '/' . $filename;
        }

        $game->update($data);

        return redirect()->route('top_games.index')->with('success', 'Top Game ได้ถูกแก้ไขเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $game = TopGame::findOrFail($id);

        // ลบรูปภาพออกจาก public folder
        if ($game->image && File::exists(public_path($game->image))) {
            File::delete(public_path($game->image));
        }

        $game->delete();

        return redirect()->route('top_games.index')->with('success', 'Top Game ได้ถูกลบเรียบร้อยแล้ว');
    }

    /**
     * Change the active status of the specified resource.
     */
    public function changeStatus($id)
    {
        $game = TopGame::findOrFail($id);
        $game->active = !$game->active;
        $game->save();

        return redirect()->route('top_games.index')->with('success', 'สถานะถูกเปลี่ยนเรียบร้อยแล้ว');
    }
}
