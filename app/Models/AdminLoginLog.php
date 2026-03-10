<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLoginLog extends Model
{
    protected $table = 'admin_login_logs';

    protected $fillable = [
        'user_id',
        'guard',
        'action',
        'succeeded',
        'identifier',
        'failure_reason',
        'ip',
        'user_agent',
        'session_id',
        'remember',
        'method',
        'path',
        'logged_in_at',
        'logged_out_at',
        'user_level',
        'user_permissions',
    ];

    protected $casts = [
        'succeeded' => 'boolean',
        'remember' => 'boolean',
        'logged_in_at' => 'datetime',
        'logged_out_at' => 'datetime',
        'user_permissions' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

