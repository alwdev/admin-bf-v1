<?php

namespace App\Http\Controllers;

use App\Models\AdminLoginLog;
use Illuminate\Http\Request;

class AdminLoginLogController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) ($request->get('per_page', 50));
        if ($perPage < 10) { $perPage = 10; }
        if ($perPage > 200) { $perPage = 200; }

        $q = AdminLoginLog::query()->with('user')->orderByDesc('id');

        if ($request->filled('date')) {
            $request->merge([
                'date_from' => $request->get('date'),
                'date_to' => $request->get('date'),
            ]);
        }

        if ($request->filled('action')) {
            $q->where('action', $request->get('action'));
        }
        if ($request->filled('ip')) {
            $q->where('ip', 'like', '%' . $request->get('ip') . '%');
        }
        if ($request->filled('identifier')) {
            $kw = $request->get('identifier');
            $q->where(function ($sub) use ($kw) {
                $sub->where('identifier', 'like', '%' . $kw . '%')
                    ->orWhereHas('user', function ($uq) use ($kw) {
                        $uq->where('name', 'like', '%' . $kw . '%')
                            ->orWhere('email', 'like', '%' . $kw . '%');
                    });
            });
        }
        if ($request->filled('date_from')) {
            $q->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $q->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $logs = $q->paginate($perPage)->appends($request->query());

        return view('admin.login-logs.index', compact('logs', 'perPage'));
    }
}
