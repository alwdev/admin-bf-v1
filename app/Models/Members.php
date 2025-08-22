<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Members extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'members';

    protected $fillable = [
        'member_id',
        'username',
        'password',
        'source',
        'role',
        'nickname',
        'wallet_balance',
        'wallet_specialBuyIn',
        'wallet_lastUpdate',
        'level',
        'parent',
        'type',
        'playId',
        'currency',
        'fullname',
        'bank_name',
        'bank_number',
        'bank_code',
        'birth_date',
        'account_name',
        'phone',
        'enable',
        'active',
        'update_by',
        'ref_click_link',
        'ref_user',
        'ref_commission',
        'token',
        'ranking',
        'store_id',
        'remaining_spin',
        'google_id',
        'email',
        'facebook_id',
        'wallet_address',
        'session_id',
        'remember_token',
    ];

    /**
     * Scope หา parent ของสมาชิกที่มี $childId อยู่ใน ref_user
     */
    public function scopeWhereHasChild($query, $childId)
    {
        $childId = (string) $childId;
        return $query->where(function ($q) use ($childId) {
            $q->where('ref_user', 'like', "[$childId,%")
              ->orWhere('ref_user', 'like', "%,$childId,%")
              ->orWhere('ref_user', 'like', "%,$childId]")
              ->orWhere('ref_user', $childId);
        });
    }
    ];

    public function conversations(): BelongsToMany {
        return $this->belongsToMany(Conversation::class, 'conversation_member', 'member_id', 'conversation_id')
            ->withPivot('joined_at');
    }
    public function messages(): HasMany {
        return $this->hasMany(Message::class, 'member_id');
    }
}
