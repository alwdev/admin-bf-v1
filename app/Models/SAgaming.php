<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SAgaming extends Model
{
    use HasFactory;
    protected $table ='sa_gaming_callback';
    protected $fillable = [
        'id',
        'username',
        'currency',
        'amount',
        'txnid',
        'timestamp',
        'ip',
        'gametype',
        'platform',
        'hostid',
        'gameid',
        'betdetails',
        'Payouttime',
        'gamecancel',
        'txn_reverse_id',
        'type',
        'created_at',
        'updated_at',
        "sync"
    ];

}
