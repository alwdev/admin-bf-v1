<?php

namespace App\Http\Controllers;

use App\Models\UfaTransaction;
use Illuminate\Http\Request;

class UfaTransactionReportController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) ($request->get('per_page', 50));
        if ($perPage < 10) { $perPage = 10; }
        if ($perPage > 200) { $perPage = 200; }

        $q = UfaTransaction::query()->orderByDesc('id');

        if ($request->filled('username')) {
            $q->where('username', 'like', '%' . $request->get('username') . '%');
        }
        if ($request->filled('bet_id')) {
            $q->where('bet_id', 'like', '%' . $request->get('bet_id') . '%');
        }
        if ($request->filled('type')) {
            $q->where('type', $request->get('type'));
        }
        if ($request->filled('bet_type')) {
            $q->where('bet_type', $request->get('bet_type'));
        }
        if ($request->filled('status')) {
            $q->where('status', $request->get('status'));
        }
        if ($request->filled('date_from')) {
            $q->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $q->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $transactions = $q->paginate($perPage)->appends($request->query());

        $typeOptions = UfaTransaction::query()
            ->select('type')
            ->distinct()
            ->pluck('type')
            ->filter()
            ->values()
            ->toArray();

        $betTypeOptions = UfaTransaction::query()
            ->select('bet_type')
            ->distinct()
            ->pluck('bet_type')
            ->filter()
            ->values()
            ->toArray();

        $statusOptions = UfaTransaction::query()
            ->select('status')
            ->distinct()
            ->pluck('status')
            ->filter()
            ->values()
            ->toArray();

        return view('report.ufa_transactions', compact(
            'transactions',
            'perPage',
            'typeOptions',
            'betTypeOptions',
            'statusOptions'
        ));
    }
}

