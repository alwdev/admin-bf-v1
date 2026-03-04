<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SboGameList extends Model
{
    protected $table = 'sbo_game_lists';
    protected $fillable = [
        'gpid',
        'provider_name',
        'provider_type',
        'game_id',
        'game_name',
        'game_code',
        'img',
        'active',
        'payload',
    ];
}
