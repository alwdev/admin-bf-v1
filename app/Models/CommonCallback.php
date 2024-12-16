<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonCallback extends Model
{
    use HasFactory;
    protected $table = 'common_callback';
    protected $fillable = [
        "request_id",
        "timestampMillis",
        "productId",
        "currency",
        "username",
        "sessionToken",
        "_id",
        "status",
        "roundId",
        "betAmount",
        "payoutAmount",
        "payinAmount",
        "txnId",
        "gameCode",
        "playInfo",
        "isEndround",
        "type",
        "details",
        "sync"
    ];
}
