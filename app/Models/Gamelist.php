<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gamelist extends Model
{
    use HasFactory;
    protected $table = 'game_list';
    protected $fillable = [
        "gameId",
        "productId",
        "launchCode",
        "gameCode",
        "categoryId",
        "bannerUrl",
        "gameName",
        "describe",
        "popular",
        "new",
        "vote",
        "active",
    ];
}
