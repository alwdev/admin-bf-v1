<?php

namespace App\Http\Controllers;

use App\Models\AmbCategory;
use Illuminate\Http\Request;

class AmbCategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 50);
        $perPage = $perPage > 0 ? min($perPage, 200) : 50;

        $q = AmbCategory::query()->orderBy('order_no')->orderBy('id');
        if ($request->filled('s')) {
            $s = $request->string('s')->trim();
            $q->where(function ($qq) use ($s) {
                $qq->where('code', 'like', '%' . $s . '%')
                    ->orWhere('name', 'like', '%' . $s . '%')
                    ->orWhere('name_th', 'like', '%' . $s . '%');
            });
        }

        $categories = $q->paginate($perPage)->withQueryString();

        return view('amb.categories.index', compact('categories', 'perPage'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:64|unique:amb_categories,code',
            'name' => 'required|string|max:255',
            'name_th' => 'nullable|string|max:255',
            'order_no' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
        ]);
        $data['order_no'] = $data['order_no'] ?? 0;
        $data['active'] = $request->boolean('active');

        AmbCategory::create($data);

        return redirect()->route('amb.categories.index')->with('success', 'สร้างหมวดหมู่แล้ว');
    }

    public function update(Request $request, int $id)
    {
        $category = AmbCategory::findOrFail($id);
        $data = $request->validate([
            'code' => 'required|string|max:64|unique:amb_categories,code,' . $category->id,
            'name' => 'required|string|max:255',
            'name_th' => 'nullable|string|max:255',
            'order_no' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
        ]);
        $data['order_no'] = $data['order_no'] ?? 0;
        $data['active'] = $request->boolean('active');

        $category->update($data);

        return redirect()->route('amb.categories.index')->with('success', 'อัปเดตหมวดหมู่แล้ว');
    }

    public function destroy(int $id)
    {
        $category = AmbCategory::findOrFail($id);
        if ($category->games()->exists()) {
            return redirect()->route('amb.categories.index')->with('error', 'ลบไม่ได้: มีเกมอ้างอิงหมวดหมู่นี้');
        }
        $category->delete();

        return redirect()->route('amb.categories.index')->with('success', 'ลบหมวดหมู่แล้ว');
    }

    public function toggle(Request $request)
    {
        $request->validate(['id' => 'required|integer', 'active' => 'required|boolean']);
        $cat = AmbCategory::findOrFail((int) $request->id);
        $cat->active = $request->boolean('active');
        $cat->save();

        return response()->json(['ok' => true]);
    }
}
