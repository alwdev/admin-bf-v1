<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionUsed extends Model
{
    use HasFactory;
    protected $table = 'promotion_useds';
    protected $fillable = [
        'member_id',
        'promotion_id',
        'promotion_name',
        'amount',
    ];
}
