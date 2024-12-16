<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BigGameCallbaclk extends Model
{
    use HasFactory;
    protected $table = 'big_game_callback';
    protected $fillable = [
        "request_id",
        "tranId",
        "amount",
        "loginId",
        "gameId",
        "playId",
        "orders_amount",
        "issueId",
        "orderTime",
        "orderId",
        "fromIp",
        "orderFrom",
        "noComm",
        "betContent",
        "validAmount",
        "orderAmount",
        "orderStatus",
        "lastUpdateTime",
        "serverTime",
        "moduleId",
        "userId",
        "type",
        "sync"
    ];
}
