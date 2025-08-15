<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cypto extends Model
{
    use HasFactory;
    protected $table = 'cypto';
    protected $fillable = ['name', 'symbol', 'price'];
}
