<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmbCategory extends Model
{
    use HasFactory;

    protected $table = 'amb_categories';

    protected $fillable = ['code', 'name', 'name_th', 'order_no', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function games()
    {
        return $this->hasMany(AmbGame::class, 'amb_category_id');
    }

    public function products()
    {
        return $this->hasMany(AmbProduct::class, 'amb_category_id');
    }
}
