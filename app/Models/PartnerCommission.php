<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerCommission extends Model
{
    use HasFactory;
    protected $table = 'partner_commissions';

       protected $fillable = [
        'partner_id', // This line was added or needs to be uncommented
        'amount',
        'payment_type',
        'payment_status',
        'transaction_id',
        'note',
    ];
}
