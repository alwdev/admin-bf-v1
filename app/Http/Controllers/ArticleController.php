<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Hashtag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(){
        // Show all articles
        $articles = Article::orderby('created_at', 'desc')->get();
        return view('article.index', compact('articles'));
    }

    public function create(){
        // Show the form to create a new article
        $hashtags = Hashtag::all();
        $categories = ['บอล', 'หวย', 'ดูหนังออนไลน์', '18+','การพนัน','ข่าวในประเทศ'];
        return view('article.create',compact('categories','hashtags'));
    }

    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        // บันทึก Tags (ถ้ามี)
        $tags = null;
        if (!empty($request->tags)) {
            $tags = is_array($request->tags) ? implode(',', $request->tags) : $request->tags;
        }

        // บันทึกบทความ
        $article = new Article();
        $article->author_id = auth::user()->id;
        $article->title = $request->title;
        $article->content = $request->content;
        $article->description = $request->description;
        $article->category = $request->category;
        $article->tags = $tags;

        // จัดการกับรูปภาพ
        if ($request->image) {
            $fileName = time() . '.' . $request->image->extension();
            $request->image->move('_image', $fileName);
            $article->image = "http://" . $_SERVER['HTTP_HOST'] . '/_image/' . $fileName;
        }

        if ($request->image_end) {
            $fileName = time() . 'END.' . $request->image_end->extension();
            $request->image_end->move('_image', $fileName);
            $article->image_end = "http://" . $_SERVER['HTTP_HOST'] . '/_image/' . $fileName;
        }

        // ตรวจสอบสถานะ
        $article->status = $request->enable ? $request->enable : 0;
        $article->save();

        // เชื่อมโยง Hashtags กับบทความ
        if ($request->hashtags) {
            $article->hashtags()->attach($request->hashtags);  // เชื่อมโยง Hashtags
        }

        // Redirect to the articles index page
        return redirect()->route('article.index')->with('status', '200');
    }


    public function edit($id){
        // Show the form to edit an article
        $article = Article::find($id);
        $hashtags = Hashtag::all();
        $categories = ['บอล', 'หวย', 'ดูหนังออนไลน์', '18+','การพนัน','ข่าวในประเทศ'];
        return view('article.edit', compact('article','categories','hashtags'));
    }
    public function update(Request $request, $id)
{
    // Validate the input
    $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'content' => ['required', 'string'],
    ]);

    // บันทึก Tags (ถ้ามี)
    $tags = null;
    if (!empty($request->tags)) {
        $tags = is_array($request->tags) ? implode(',', $request->tags) : $request->tags;
    }

    // Update the article
    $article = Article::find($id);
    $article->title = $request->title;
    $article->content = $request->content;
    $article->description = $request->description;
    $article->category = $request->category;
    $article->tags = $tags;

    // จัดการกับรูปภาพ
    if ($request->image) {
        $fileName = time() . '.' . $request->image->extension();
        $request->image->move('_image', $fileName);
        $article->image = "http://" . $_SERVER['HTTP_HOST'] . '/_image/' . $fileName;
    }

    if ($request->image_end) {
        $fileName = time() . 'END.' . $request->image_end->extension();
        $request->image_end->move('_image', $fileName);
        $article->image_end = "http://" . $_SERVER['HTTP_HOST'] . '/_image/' . $fileName;
    }

    $article->status = $request->enable ? $request->enable : 0;
    $article->save();

    // อัปเดต Hashtags
    if ($request->hashtags) {
        $article->hashtags()->sync($request->hashtags);  // ใช้ sync สำหรับอัปเดต Hashtags
    }

    return redirect()->route('article.index')->with('status', '200');
}


    public function destroy($id)
    {
        // ค้นหาบทความตาม id
        $article = Article::findOrFail($id);
        
        if ($article->image) {
            try {
                 // ลบภาพจาก storage หรือ server ถ้ามี
            // ปรับ path ให้ตรงกับตำแหน่งที่เก็บภาพใน _image
            $imagePath = public_path('_image/' . basename($article->image));
            
            if (file_exists($imagePath)) {
                unlink($imagePath);  // ใช้ unlink() เพื่อลบไฟล์จาก server
            }
            } catch (\Throwable $th) {
                //throw $th;
            }

        }

        // ลบบทความจากฐานข้อมูล
        $article->delete();

        // แสดงข้อความแจ้งเตือนหลังจากลบสำเร็จ
        return redirect()->route('article.index')->with('success', 'บทความถูกลบเรียบร้อยแล้ว');
    }

     public function upload(Request $request)
    {
        // ตรวจสอบว่ามีไฟล์ที่ถูกอัปโหลดมาหรือไม่
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            // อัปโหลดไฟล์ไปยังโฟลเดอร์ public/images
            $path = $request->file('file')->store('images', 'public');

            // สร้าง URL ที่จะส่งกลับให้กับ Quill editor
            $url = asset('storage/' . $path);

            return response()->json(['url' => $url]);
        }

        return response()->json(['error' => 'ไม่สามารถอัปโหลดไฟล์ได้'], 400);
    }
}
