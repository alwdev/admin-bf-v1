<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WmCallback extends Model
{
    use HasFactory;
    protected $table = 'wm_callback';
    protected $fillable = [
        "username",
        "amount",
        "betId",
        "roundId",
        "gameId",
        "type",
        "status"
    ];
}
