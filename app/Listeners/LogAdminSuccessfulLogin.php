<?php

namespace App\Listeners;

use App\Models\AdminLoginLog;
use Illuminate\Auth\Events\Login;

class LogAdminSuccessfulLogin
{
    public function handle(Login $event): void
    {
        if (!function_exists('request')) {
            return;
        }

        $req = request();
        $user = $event->user;

        $permissions = null;
        if (isset($user->permissions)) {
            $decoded = json_decode($user->permissions, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $permissions = $decoded;
            }
        }

        AdminLoginLog::create([
            'user_id' => $user->id ?? null,
            'guard' => $event->guard ?? null,
            'action' => 'login',
            'succeeded' => true,
            'identifier' => $user->email ?? ($user->name ?? null),
            'ip' => $req->ip(),
            'user_agent' => $req->userAgent(),
            'session_id' => method_exists($req, 'session') ? $req->session()->getId() : null,
            'remember' => (bool) ($event->remember ?? false),
            'method' => $req->method(),
            'path' => $req->fullUrl(),
            'logged_in_at' => now(),
            'user_level' => isset($user->level) ? (int) $user->level : null,
            'user_permissions' => $permissions,
        ]);
    }
}

