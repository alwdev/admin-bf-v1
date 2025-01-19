<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PgHard extends Model
{
    use HasFactory;
    protected $table = 'pg_hards';
    protected $fillable = [
        "username",
        "transactionId",
        "payoff",
        "betAmount",
        "userToken",
        "roundId",
        "gameId",
        "gameStringId",
        "gameName"
    ];
}
