<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
}
