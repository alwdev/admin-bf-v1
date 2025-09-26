<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    use HasFactory;

    // ตั้งชื่อตาราง (ถ้าใช้ convention ที่ชื่อ Model เป็นเอกพจน์ ตารางเป็นพหูพจน์ก็ไม่จำเป็นต้องระบุ)
    protected $table = 'home_pages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'active',
        'revision',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // กำหนดให้ Laravel แปลงฟิลด์ 'content' เป็น Array หรือ Object อัตโนมัติเมื่อดึงจากฐานข้อมูล
        'content' => 'array',
        'active' => 'boolean',
    ];

    /**
     * Scope for getting the currently active home page content.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
