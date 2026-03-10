<?php

namespace App\Listeners;

use App\Models\AdminLoginLog;
use Illuminate\Auth\Events\Failed;

class LogAdminFailedLogin
{
    public function handle(Failed $event): void
    {
        if (!function_exists('request')) {
            return;
        }

        $req = request();

        $identifier = null;
        if (!empty($event->credentials) && is_array($event->credentials)) {
            $identifier = $event->credentials['email']
                ?? $event->credentials['username']
                ?? $event->credentials['name']
                ?? null;
        }

        AdminLoginLog::create([
            'user_id' => $event->user?->id,
            'guard' => $event->guard ?? null,
            'action' => 'failed',
            'succeeded' => false,
            'identifier' => $identifier,
            'failure_reason' => 'invalid_credentials',
            'ip' => $req->ip(),
            'user_agent' => $req->userAgent(),
            'session_id' => method_exists($req, 'session') ? $req->session()->getId() : null,
            'remember' => false,
            'method' => $req->method(),
            'path' => $req->fullUrl(),
        ]);
    }
}

