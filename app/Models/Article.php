<?php

// app/Models/Article.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'category', 'description', 'enable'];

    public function hashtags()
    {
        return $this->belongsToMany(Hashtag::class, 'article_hashtag');
    }
}
