<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemAlert extends Model
{
    use HasFactory;

    protected $table = 'system_alert'; // ถูกแล้ว

    protected $fillable = [
        "date",
        "time",
        "message",
        "active",
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public $timestamps = false; // ต้องปิด ไม่งั้น save ไม่ผ่าน
}
