<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WithdrawalRequestController extends Controller
{
    /**
     * List withdrawal requests with optional status filter.
     */
    public function index(Request $request): View
    {
        $query = WithdrawalRequest::with('member')->orderByDesc('id');
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        $requests = $query->paginate(25)->appends($request->only('status'));
        $statuses = ['pending','approved','rejected','paid'];

        return view('withdrawals.index', compact('requests','statuses'));
    }

    /**
     * Show a single withdrawal request.
     */
    public function show(int $id): View
    {
        $requestItem = WithdrawalRequest::with('member')->findOrFail($id);
        return view('withdrawals.show', compact('requestItem'));
    }

    /**
     * Mark a withdrawal as paid with tx hash.
     */
    public function markPaid(int $id, Request $request): RedirectResponse
    {
        $requestItem = WithdrawalRequest::findOrFail($id);
        if ($requestItem->status === 'paid') {
            return redirect()->route('withdrawals.show', $id)->with('error', 'รายการนี้ถูกโอนไปแล้ว');
        }

        $validated = $request->validate([
            'tx_hash' => 'required|string|max:255',
        ]);

        $requestItem->tx_hash = $validated['tx_hash'];
        $requestItem->status = 'paid';
        $requestItem->save();

        return redirect()->route('withdrawals.show', $id)->with('success', 'บันทึก hash และเปลี่ยนสถานะเป็นโอนแล้วเรียบร้อย');
    }
}
