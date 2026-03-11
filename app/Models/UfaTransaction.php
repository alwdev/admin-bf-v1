<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UfaTransaction extends Model
{
    use HasFactory;

    protected $table = 'ufa_transactions';

    protected $fillable = [
        'username',
        'bet_id',
        'bet_type',
        'amount',
        'bonus',
        'status',
        'balance_before',
        'balance_after',
        'request_data',
        'type',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'bonus' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'request_data' => 'array',
    ];
}
