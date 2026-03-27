<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmbHomepageItem extends Model
{
    protected $table = 'amb_homepage_items';

    protected $fillable = ['category', 'position', 'amb_game_id'];

    /** @var list<string> ตรงคีย์เดิม SBO */
    public const SECTIONS = ['hot', 'slot', 'livecasino'];

    public function game(): BelongsTo
    {
        return $this->belongsTo(AmbGame::class, 'amb_game_id');
    }
}
