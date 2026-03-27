<?php

namespace App\Http\Controllers;

use App\Models\AmbCategory;
use App\Models\AmbGame;
use App\Models\AmbProduct;
use App\Support\AmbImageUpload;
use Illuminate\Http\Request;

class AmbProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 50);
        $perPage = $perPage > 0 ? min($perPage, 200) : 50;

        $q = AmbProduct::query()->with('category')->orderBy('order_no')->orderBy('id');

        if ($request->filled('category_id')) {
            $cid = $request->input('category_id');
            if ($cid === 'unassigned') {
                $q->whereNull('amb_category_id');
            } elseif ((int) $cid > 0) {
                $q->where('amb_category_id', (int) $cid);
            }
        }

        if ($request->filled('s')) {
            $s = $request->string('s')->trim();
            $q->where(function ($qq) use ($s) {
                $qq->where('product_code', 'like', '%' . $s . '%')
                    ->orWhere('product_name', 'like', '%' . $s . '%');
            });
        }

        $products = $q->paginate($perPage)->withQueryString();
        $categories = AmbCategory::orderBy('order_no')->orderBy('name')->get();

        return view('amb.products.index', compact('products', 'perPage', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_code' => 'required|string|max:64|unique:amb_products,product_code',
            'product_name' => 'required|string|max:255',
            'amb_category_id' => 'required|integer|exists:amb_categories,id',
            'order_no' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
            'img' => 'nullable|image|max:2048',
        ]);
        unset($data['img']);
        $data['order_no'] = $data['order_no'] ?? 0;
        $data['active'] = $request->boolean('active');

        $product = AmbProduct::create($data);

        if ($request->hasFile('img')) {
            $product->update([
                'img' => AmbImageUpload::saveProductImage($request->file('img'), $product->id),
            ]);
        }

        return redirect()->route('amb.products.index')->with('success', 'สร้าง provider แล้ว');
    }

    public function update(Request $request, int $id)
    {
        $product = AmbProduct::findOrFail($id);
        $prevCategoryId = $product->amb_category_id;
        $data = $request->validate([
            'product_code' => 'required|string|max:64|unique:amb_products,product_code,' . $product->id,
            'product_name' => 'required|string|max:255',
            'amb_category_id' => 'required|integer|exists:amb_categories,id',
            'order_no' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
            'img' => 'nullable|image|max:2048',
        ]);
        unset($data['img']);
        $data['order_no'] = $data['order_no'] ?? 0;
        $data['active'] = $request->boolean('active');

        $product->update($data);

        if ($request->hasFile('img')) {
            $product->update([
                'img' => AmbImageUpload::saveProductImage($request->file('img'), $product->id),
            ]);
        }

        $newCategoryId = (int) $data['amb_category_id'];
        if ($prevCategoryId !== $newCategoryId) {
            AmbGame::where('amb_product_id', $product->id)->update(['amb_category_id' => $newCategoryId]);
        }

        return redirect()->route('amb.products.index')->with('success', 'อัปเดต provider แล้ว');
    }

    public function destroy(int $id)
    {
        $product = AmbProduct::findOrFail($id);
        if ($product->games()->exists()) {
            return redirect()->route('amb.products.index')->with('error', 'ลบไม่ได้: มีเกมอ้างอิง provider นี้');
        }
        $product->delete();

        return redirect()->route('amb.products.index')->with('success', 'ลบ provider แล้ว');
    }

    public function toggle(Request $request)
    {
        $request->validate(['id' => 'required|integer', 'active' => 'required|boolean']);
        $p = AmbProduct::findOrFail((int) $request->id);
        $p->active = $request->boolean('active');
        $p->save();

        return response()->json(['ok' => true]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:amb_products,id',
            'imgupload' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);
        $product = AmbProduct::findOrFail((int) $request->id);
        $path = AmbImageUpload::saveProductImage($request->file('imgupload'), $product->id);
        $product->img = $path;
        $product->save();

        return response()->json($path);
    }
}
