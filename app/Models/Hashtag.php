<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    use HasFactory;

    protected $fillable = ['hashtag'];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_hashtag');
    }
}