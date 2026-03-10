<?php

namespace App\Listeners;

use App\Models\AdminLoginLog;
use Illuminate\Auth\Events\Logout;

class LogAdminLogout
{
    public function handle(Logout $event): void
    {
        if (!function_exists('request')) {
            return;
        }

        $req = request();
        $userId = $event->user?->id;
        $sessionId = method_exists($req, 'session') ? $req->session()->getId() : null;

        if ($userId) {
            $q = AdminLoginLog::query()
                ->where('user_id', $userId)
                ->where('action', 'login')
                ->whereNull('logged_out_at')
                ->orderByDesc('id');

            if ($sessionId) {
                $q->where('session_id', $sessionId);
            }

            $lastLogin = $q->first();
            if ($lastLogin) {
                $lastLogin->logged_out_at = now();
                $lastLogin->save();
            }
        }

        AdminLoginLog::create([
            'user_id' => $userId,
            'guard' => $event->guard ?? null,
            'action' => 'logout',
            'succeeded' => true,
            'identifier' => $event->user?->email ?? ($event->user?->name ?? null),
            'ip' => $req->ip(),
            'user_agent' => $req->userAgent(),
            'session_id' => $sessionId,
            'remember' => false,
            'method' => $req->method(),
            'path' => $req->fullUrl(),
            'logged_out_at' => now(),
            'user_level' => isset($event->user->level) ? (int) $event->user->level : null,
            'user_permissions' => isset($event->user->permissions) ? json_decode($event->user->permissions, true) : null,
        ]);
    }
}

