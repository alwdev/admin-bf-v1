<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SboProvider extends Model
{
    protected $table = 'sbo_providers';
    protected $fillable = [
        'gpid',
        'name',
        'type',
        'lobby_game_id',
        'name_zh',
        'supports_game_id_login',
        'devices',
        'img',
        'active',
    ];
}
