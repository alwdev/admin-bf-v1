<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponUse extends Model
{
    use HasFactory;
    protected $table = 'coupon_uses';
    protected $fillable = ['coupon_id', 'member_id', 'code', 'amount'];
}
