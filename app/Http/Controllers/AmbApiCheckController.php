<?php

namespace App\Http\Controllers;

use App\Models\AmbProduct;
use App\Services\AmbSeamlessSyncService;
use Illuminate\Http\Request;

class AmbApiCheckController extends Controller
{
    public function index()
    {
        $products = AmbProduct::query()
            ->with('category')
            ->withCount('games')
            ->orderBy('order_no')
            ->orderBy('product_code')
            ->get();

        return view('amb.api-check.index', compact('products'));
    }

    public function probe(Request $request, AmbSeamlessSyncService $sync)
    {
        $data = $request->validate([
            'target' => 'required|string|in:categories,products,games',
            'product_code' => 'nullable|string|max:191',
        ]);

        $result = $sync->probe(
            $data['target'],
            $data['product_code'] ?? null
        );

        $status = ($result['ok'] ?? false) ? 200 : 422;

        return response()->json($result, $status);
    }
}
