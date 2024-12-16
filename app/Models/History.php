<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
    protected $table = 'history';
    protected $fillable = [
        "request_id",
        "roundId",
        "username",
        "game",
        "provider",
        "amount",
        "winlose",
        "type",
        "playtime",
        "balanceBefore" ,
        "balanceAfter",
    ];
}
