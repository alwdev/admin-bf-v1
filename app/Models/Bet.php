<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Bet extends Model
{
    use HasFactory;
    protected $table = 'bets';
    protected $fillable = [
        "username",
        "agent",
        "game",
        "gameCode",
        "roundId",
        "amount",
        "currency",
        "refId",
        "timestamp",
        "type"
    ];
}
