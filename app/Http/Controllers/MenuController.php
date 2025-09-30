<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the menus.
     */
    public function index()
    {
        $menus = Menu::orderBy('sort_order')->get();
        return view('menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new menu.
     */
    public function create()
    {
        return view('menus.create');
    }

    /**
     * Store a newly created menu in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'url'        => 'nullable|string|max:255',
            'icon'       => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        Menu::create([
            'title'      => $request->title,
            'url'        => $request->url,
            'icon'       => $request->icon,
            'is_active'  => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('menus.index')->with('success', 'Menu created successfully.');
    }

    /**
     * Show the form for editing the specified menu.
     */
    public function edit(Menu $menu)
    {
        return view('menus.edit', compact('menu'));
    }

    /**
     * Update the specified menu in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url'   => 'nullable|string|max:255',
            'icon'  => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $menu->update([
            'title' => $request->title,
            'url' => $request->url,
            'icon' => $request->icon,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('menus.index')->with('success', 'Menu updated successfully.');
    }

    /**
     * Remove the specified menu from storage.
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menu deleted successfully.');
    }

    /**
     * Toggle menu status (Active/Inactive).
     */
    public function toggle(Menu $menu)
    {
        $menu->is_active = !$menu->is_active;
        $menu->save();

        return redirect()->route('menus.index')->with('success', 'Menu status updated successfully.');
    }
}
