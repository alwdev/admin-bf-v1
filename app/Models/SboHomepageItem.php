<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SboHomepageItem extends Model
{
    protected $table = 'sbo_homepage_items';
    protected $fillable = [
        'category',
        'position',
        'game_list_id',
    ];

    public function game()
    {
        return $this->belongsTo(\App\Models\SboGameList::class, 'game_list_id');
    }
}

