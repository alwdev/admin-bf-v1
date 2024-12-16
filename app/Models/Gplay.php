<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gplay extends Model
{
    use HasFactory;
    protected $table = 'gplay';
    protected $fillable = [
        "id",
        "operatorToken",
        "seamlessKey",
        "agentUsername",
        "playerUsername",
        "currencyCode",
        "productName",
        "productId",
        "productCode",
        "categoryId",
        "categoryName",
        "gameName",
        "gameCode",
        "txnId",
        "eventType",
        "eventName",
        "roundId",
        "txnStatus",
        "betInfo",
        "amount",
        "turnover",
        "isFeatureBuy",
        "requestUid",
        "requestTime",
    ];
}
