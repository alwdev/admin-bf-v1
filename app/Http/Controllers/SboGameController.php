<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\SboGameList;
use App\Models\SboProvider;

class SboGameController extends Controller
{
    public function index(Request $request)
    {
        $providers = SboProvider::orderBy('name')->get();
        $perPage = (int)($request->get('per_page', 50));
        if ($perPage < 10) { $perPage = 10; }
        if ($perPage > 200) { $perPage = 200; }

        $q = SboGameList::query();
        if ($request->filled('provider_name')) {
            $q->where('provider_name', 'like', '%' . $request->get('provider_name') . '%');
        }
        if ($request->filled('provider_type')) {
            $q->where('provider_type', 'like', '%' . $request->get('provider_type') . '%');
        }
        if ($request->filled('game_name')) {
            $q->where('game_name', 'like', '%' . $request->get('game_name') . '%');
        }
        if ($request->filled('game_code')) {
            $q->where('game_code', 'like', '%' . $request->get('game_code') . '%');
        }
        if ($request->filled('active') && $request->get('active') !== 'all') {
            $q->where('active', (int)$request->get('active'));
        }
        if ($request->filled('q')) {
            $keyword = $request->get('q');
            $q->where(function($sub) use ($keyword) {
                $sub->where('game_name', 'like', "%$keyword%")
                    ->orWhere('game_code', 'like', "%$keyword%")
                    ->orWhere('provider_name', 'like', "%$keyword%");
            });
        }

        $games = $q->orderBy('provider_name')->orderBy('game_name')->paginate($perPage)->appends($request->query());
        return view('sbo.games.index', compact('games','providers','perPage'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider_name' => 'required|string|max:255',
            'provider_type' => 'nullable|string|max:255',
            'game_id' => 'nullable|string|max:255',
            'game_name' => 'required|string|max:255',
            'game_code' => 'nullable|string|max:255',
            'gpid' => 'nullable|integer',
            'active' => 'nullable|boolean',
            'payload' => 'nullable|string',
            'img' => 'nullable|image|max:2048',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('images/sbo/games', 'public');
            $data['img'] = asset('storage/' . $path);
        }
        $data['active'] = $request->boolean('active');
        SboGameList::create($data);
        return redirect()->route('sbo.games.index')->with('success', 'Created');
    }

    public function update(Request $request, $id)
    {
        $game = SboGameList::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'provider_name' => 'required|string|max:255',
            'provider_type' => 'nullable|string|max:255',
            'game_id' => 'nullable|string|max:255',
            'game_name' => 'required|string|max:255',
            'game_code' => 'nullable|string|max:255',
            'gpid' => 'nullable|integer',
            'active' => 'nullable|boolean',
            'payload' => 'nullable|string',
            'img' => 'nullable|image|max:2048',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('images/sbo/games', 'public');
            $data['img'] = asset('storage/' . $path);
        }
        $data['active'] = $request->boolean('active');
        $game->update($data);
        return redirect()->route('sbo.games.index')->with('success', 'Updated');
    }

    public function destroy($id)
    {
        $game = SboGameList::findOrFail($id);
        $game->delete();
        return redirect()->route('sbo.games.index')->with('success', 'Deleted');
    }

    public function toggle(Request $request, $id = null, $checked = null)
    {
        if ($id === null) {
            $request->validate([
                'id' => 'required|integer',
                'checked' => 'required|boolean',
            ]);
            $id = (int)$request->id;
            $checked = (int)$request->checked;
        }
        $game = SboGameList::findOrFail($id);
        $game->active = ((int)$checked) ? 1 : 0;
        $game->save();
        return response()->json(true);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'imgupload' => 'required|image|max:4096',
        ]);
        $game = SboGameList::findOrFail($request->id);
        $path = $request->file('imgupload')->store('images/sbo/games', 'public');
        $game->img = asset('storage/' . $path);
        $game->save();
        return response()->json(['img' => $game->img]);
    }
}
