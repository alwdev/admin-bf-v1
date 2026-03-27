<?php

namespace App\Http\Controllers;

use App\Models\AmbCategory;
use App\Models\AmbGame;
use App\Models\AmbProduct;
use App\Support\AmbImageUpload;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
class AmbGameController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 50);
        $perPage = $perPage > 0 ? min($perPage, 200) : 50;

        $products = AmbProduct::orderBy('order_no')->orderBy('product_name')->get();
        $categories = AmbCategory::orderBy('order_no')->orderBy('name')->get();

        $q = AmbGame::query()->with(['product.category', 'category'])->orderBy('rank')->orderBy('id');

        if ($request->filled('product_id')) {
            $q->where('amb_product_id', (int) $request->product_id);
        }
        if ($request->filled('category_id')) {
            $q->where('amb_category_id', (int) $request->category_id);
        }
        if ($request->filled('active') && $request->active !== '') {
            $q->where('active', $request->boolean('active'));
        }
        if ($request->filled('s')) {
            $s = $request->string('s')->trim();
            $q->where(function ($qq) use ($s) {
                $qq->where('game_code', 'like', '%' . $s . '%')
                    ->orWhere('game_name', 'like', '%' . $s . '%')
                    ->orWhere('provider_code', 'like', '%' . $s . '%');
            });
        }

        $games = $q->paginate($perPage)->withQueryString();

        return view('amb.games.index', compact('games', 'perPage', 'products', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $game = AmbGame::create($data);
        if ($request->hasFile('img')) {
            $game->update([
                'img' => AmbImageUpload::saveGameImage($request->file('img'), $game->id),
            ]);
        }

        return redirect()->route('amb.games.index', $request->only(['product_id', 'category_id', 's', 'per_page', 'active']))->with('success', 'สร้างเกมแล้ว');
    }

    public function update(Request $request, int $id)
    {
        $game = AmbGame::findOrFail($id);
        $data = $this->validated($request, $game->id);
        $game->update($data);
        if ($request->hasFile('img')) {
            $game->update([
                'img' => AmbImageUpload::saveGameImage($request->file('img'), $game->id),
            ]);
        }

        return redirect()->route('amb.games.index', $request->only(['product_id', 'category_id', 's', 'per_page', 'active']))->with('success', 'อัปเดตเกมแล้ว');
    }

    public function destroy(Request $request, int $id)
    {
        AmbGame::findOrFail($id)->delete();

        return redirect()
            ->route('amb.games.index', $request->only(['product_id', 'category_id', 's', 'per_page', 'active']))
            ->with('success', 'ลบเกมแล้ว');
    }

    public function toggle(Request $request)
    {
        $request->validate(['id' => 'required|integer', 'active' => 'required|boolean']);
        $g = AmbGame::findOrFail((int) $request->id);
        $g->active = $request->boolean('active');
        $g->save();

        return response()->json(['ok' => true]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:amb_games,id',
            'imgupload' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);
        $game = AmbGame::findOrFail((int) $request->id);
        $path = AmbImageUpload::saveGameImage($request->file('imgupload'), $game->id);
        $game->img = $path;
        $game->save();

        return response()->json($path);
    }

    private function validated(Request $request, ?int $gameId = null): array
    {
        $productId = (int) $request->input('amb_product_id');

        $data = $request->validate([
            'amb_product_id' => 'required|integer|exists:amb_products,id',
            'game_code' => [
                'required',
                'string',
                'max:128',
                Rule::unique('amb_games', 'game_code')
                    ->where(fn ($q) => $q->where('amb_product_id', $productId))
                    ->ignore($gameId),
            ],
            'game_name' => 'required|string|max:255',
            'game_type' => 'nullable|string|max:64',
            'provider_code' => 'nullable|string|max:64',
            'rank' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
            'locale_json' => 'nullable|string',
            'img' => 'nullable|image|max:2048',
        ]);
        unset($data['img']);

        $data['rank'] = $data['rank'] ?? 0;
        $data['active'] = $request->boolean('active');

        $product = AmbProduct::findOrFail($data['amb_product_id']);
        if ($product->amb_category_id === null) {
            throw ValidationException::withMessages([
                'amb_product_id' => 'Provider นี้ยังไม่ได้กำหนดหมวดหมู่ — แก้ไขที่หน้า AMB Providers ก่อน',
            ]);
        }
        $data['amb_category_id'] = $product->amb_category_id;

        $locale = null;
        if (!empty($data['locale_json'])) {
            $decoded = json_decode($data['locale_json'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw ValidationException::withMessages(['locale_json' => 'รูปแบบ JSON ไม่ถูกต้อง']);
            }
            $locale = $decoded;
        }
        unset($data['locale_json']);
        $data['locale'] = $locale;

        return $data;
    }
}
