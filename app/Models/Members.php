<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Members  extends Authenticatable
{
    use HasFactory;
    protected $table ='members';
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
        'bank_name',
        'bank_number',
        'account_name',
        'fullname',
        'phone',
        'enable',
        'active',
        'update_by',
    ];

    public function conversations(): BelongsToMany {
        return $this->belongsToMany(Conversation::class, 'conversation_member', 'member_id', 'conversation_id')
            ->withPivot('joined_at');
    }
    public function messages(): HasMany {
        return $this->hasMany(Message::class, 'member_id');
    }
}
