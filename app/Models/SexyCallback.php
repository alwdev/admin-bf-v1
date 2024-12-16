<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SexyCallback extends Model
{
    use HasFactory;
    protected $table ='sexy_callback';
    protected $fillable = [
        "action",
        "gameType",
        "gameName",
        "gameCode",
        "userId",
        "platform",
        "platformTxId",
        "roundId",
        "betType",
        "currency",
        "betTime",
        "betAmount",
        "winAmount",
        "turnover",
        "roundStartTime",
        "tableId",
        "dealerDomain",
    ];
}
