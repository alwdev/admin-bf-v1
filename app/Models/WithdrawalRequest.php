<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $table = 'withdrawal_requests';

    protected $fillable = [
        'member_id',
        'amount_usdt',
        'status',
        'destination_address',
        'tx_hash',
    ];

    protected $casts = [
        'amount_usdt' => 'decimal:8',
    ];

    public const FEE_PERCENT = 6.5; // 6.5%

    public function member(): BelongsTo
    {
        return $this->belongsTo(Members::class, 'member_id');
    }

    public function getNetAmountAttribute(): string
    {
        // จำนวนที่จะต้องโอนหลังหักค่าธรรมเนียม 6.5%
        $fee = ($this->amount_usdt * static::FEE_PERCENT) / 100;
        $net = $this->amount_usdt - $fee;
        return number_format($net, 8, '.', '');
    }

    public function getFeeAmountAttribute(): string
    {
        $fee = ($this->amount_usdt * static::FEE_PERCENT) / 100;
        return number_format($fee, 8, '.', '');
    }
}
