<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function index(){
        // Show all articles
        $articles = Article::orderby('created_at', 'desc')->get();
        return view('article.index', compact('articles'));
    }

    public function create(){
        // Show the form to create a new article
        $categories = ['บอล', 'หวย', 'ดูหนังออนไลน์', '18+','การพนัน','ข่าวในประเทศ'];
        return view('article.create',compact('categories'));
    }

    public function store(Request $request){
        // Validate the input
        $request->validate([
            'title' => ['required','string','max:255'],
            'content' => ['required','string'],
        ]);

        // Create a new article
        $article = new Article();
        $article->author_id = auth::user()->id;
        $article->title = $request->title;
        $article->content = $request->content;
        $article->description = $request->description;
        $article->category = $request->category;
        if($request->image){
            $fileName = time().'.'.$request->image->extension();
            $request->image->move('_image', $fileName);  ////  server public_html path
            $article->image = "http://" . $_SERVER['HTTP_HOST'].'/_image/'.$fileName;
        }
        if(isset($request->enable)){
            $article->status = $request->enable;
        }else{
            $article->status = 0;
        }

        $article->save();

        // Redirect to the articles index page
        return redirect()->route('article.index')->with('status','200');
    }

    public function edit($id){
        // Show the form to edit an article
        $article = Article::find($id);
        $categories = ['บอล', 'หวย', 'ดูหนังออนไลน์', '18+','การพนัน','ข่าวในประเทศ'];
        return view('article.edit', compact('article','categories'));
    }
    public function update(Request $request, $id){
        // Validate the input
        $request->validate([
            'title' => ['required','string','max:255'],
            'content' => ['required','string'],
        ]);

        // Update the article
        $article = Article::find($id);
        $article->title = $request->title;
        $article->content = $request->content;
        $article->description = $request->description;
        $article->category = $request->category;
        if($request->image){
            $fileName = time().'.'.$request->image->extension();
            $request->image->move('_image', $fileName);  ////  server public_html path
            $article->image = "http://" . $_SERVER['HTTP_HOST'].'/_image/'.$fileName;
        }
        if(isset($request->enable)){
            $article->status = $request->enable;
        } else{
            $article->status = 0;
        }

        $article->save();

        return redirect()->route('article.index')->with('status','200');
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

}
