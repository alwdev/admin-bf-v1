<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AmbGame extends Model
{
    use HasFactory;

    protected $table = 'amb_games';

    protected $fillable = [
        'amb_product_id',
        'amb_category_id',
        'game_code',
        'game_name',
        'game_type',
        'img',
        'rank',
        'provider_code',
        'locale',
        'active',
    ];

    protected $casts = [
        'locale' => 'array',
        'active' => 'boolean',
    ];

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
        if (str_starts_with($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        return Storage::disk('public')->url($path);
    }

    public function product()
    {
        return $this->belongsTo(AmbProduct::class, 'amb_product_id');
    }

    public function category()
    {
        return $this->belongsTo(AmbCategory::class, 'amb_category_id');
    }

    public function homepageItems()
    {
        return $this->hasMany(AmbHomepageItem::class, 'amb_game_id');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query
            ->where('amb_games.active', true)
            ->whereHas('product', fn ($q) => $q->where('active', true))
            ->whereHas('category', fn ($q) => $q->where('active', true));
    }

    public function scopeSlotOfProduct($query, string $productCode)
    {
        return $query->whereHas('product', fn ($q) => $q->where('product_code', $productCode)->where('active', true))
            ->whereHas('category', fn ($q) => $q->where('code', 'SLOT')->where('active', true))
            ->where('active', true)
            ->orderBy('rank');
    }

    public function scopeOfCategory($query, string $categoryCode)
    {
        return $query->whereHas('category', fn ($q) => $q->where('code', $categoryCode)->where('active', true))
            ->where('active', true)
            ->orderBy('rank');
    }

    public function scopeOfProduct($query, string $productCode)
    {
        return $query->whereHas('product', fn ($q) => $q->where('product_code', $productCode)->where('active', true))
            ->where('active', true)
            ->orderBy('rank');
    }
}
