<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;
    protected $table = 'payouts';
    protected $fillable = [
        "username",
        "agent",
        "game",
        "product",
        "roundId",
        "amount",
        "winlose",
        "currency",
        "betAmount",
        "refId",
        "timestamp",
        "endRound",
        "isOnlyPayout",
        "refRoundId",
        "startBalance",
        "endBalance "
    ];
}
