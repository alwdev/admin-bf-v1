<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberEditBalance extends Model
{
    use HasFactory;
    protected $table ='member_edit_balance';
    protected $fillable = [
        "member_id",
        "balance",
        "edit_balance",
        "user_id",
        "type",
    ];
}
