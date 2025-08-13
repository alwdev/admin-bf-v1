<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;
       protected $table = 'partner';
    protected $fillable = [
        'slug_name',
        'url',
        'contact_name',
        'contact_phonenumber',
        'rate',
    ];
        public function commissions()
    {
        return $this->hasMany(PartnerCommission::class, 'partner_id');
    }
}
