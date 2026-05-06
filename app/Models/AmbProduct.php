<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AmbProduct extends Model
{
    use HasFactory;

    protected $table = 'amb_products';

    protected $fillable = ['product_code', 'product_name', 'amb_category_id', 'img', 'order_no', 'active'];

    protected $casts = ['active' => 'boolean'];

    /**
     * URL สำหรับแสดงรูป — รองรับ /images/... (public), URL เต็ม, หรือ key บน storage disk public แบบเก่า
     */
    public function getImgUrlAttribute(): ?string
    {
        $raw = $this->attributes['img'] ?? null;
        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }
        $path = str_replace('\\', '/', trim((string) $raw));
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        // แบบ Provider เดิม: /images/... ใต้ public
        if (str_starts_with($path, '/')) {
            return asset(ltrim($path, '/'));
        }
        // ค่าเก่า: key บน disk public (storage/app/public)
        return Storage::disk('public')->url($path);
    }

    public function category()
    {
        return $this->belongsTo(AmbCategory::class, 'amb_category_id');
    }

    public function games()
    {
        return $this->hasMany(AmbGame::class, 'amb_product_id');
    }

    public function gamesByCategory(string $categoryCode)
    {
        return $this->games()
            ->whereHas('category', fn ($q) => $q->where('code', $categoryCode)->where('active', true))
            ->where('active', true)
            ->orderBy('rank');
    }
}
