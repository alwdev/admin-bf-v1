<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(){
        // Show all articles
        $articles = Article::all();
        return view('article.index', compact('articles'));
    }

    public function create(){
        // Show the form to create a new article
        return view('article.create');
    }
}
