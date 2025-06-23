<?php
// app/Models/Link.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = ['link', 'hashtags'];

    protected $casts = [
        'hashtags' => 'array', // แปลง field 'hashtags' เป็น array
    ];
}
