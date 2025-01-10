<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $table = 'transfer';
    protected $fillable = [
        'member_id',
        "ref_id",
        'amount',
        'status',
        'status_code',
        'type',
        'promotion',
        'promotion_id',
        'old_balance',
        'new_balance',
        'transfer_date'
    ];
}
