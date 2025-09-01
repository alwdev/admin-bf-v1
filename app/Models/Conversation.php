<?php

// app/Models/Conversation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = ['type', 'title'];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Members::class, 'conversation_member', 'conversation_id', 'member_id')
            ->withPivot('joined_at');
    }
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    // ใช้ตอนเปิดห้องใหม่ (แอดมิน ↔ สมาชิก)
    public static function firstOrCreateDirectBetween(int $a, int $b): self
    {
        if ($a === $b) throw new \DomainException('Cannot create a direct conversation with yourself.');
        $conv = static::where('type', 'direct')
            ->whereHas('members', fn($q) => $q->whereKey($a))
            ->whereHas('members', fn($q) => $q->whereKey($b))
            ->first();
        if ($conv) return $conv;
        $conv = static::create(['type' => 'direct', 'title' => null]);
        $conv->members()->attach([$a, $b], ['joined_at' => now()]);
        return $conv;
    }

    public function latestMessage()
    {
        // จะเรียงตาม created_at หรือ id ก็ได้ แล้วแต่ที่คุณเชื่อถือ
        return $this->hasOne(Message::class)->latestOfMany('created_at');
        // หรือ ->latestOfMany('id');
    }


}
