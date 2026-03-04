<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\SboGameList;
use App\Models\SboProvider;

class SboGameController extends Controller
{
    public function index()
    {
        $games = SboGameList::orderBy('provider_name')->orderBy('game_name')->get();
        $providers = SboProvider::orderBy('name')->get();
        return view('sbo.games.index', compact('games','providers'));
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
            $data['img'] = '/storage/' . $path;
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
            $data['img'] = '/storage/' . $path;
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

    public function toggle(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'checked' => 'required|boolean',
        ]);
        $game = SboGameList::findOrFail($request->id);
        $game->active = $request->checked ? 1 : 0;
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
        $game->img = '/storage/' . $path;
        $game->save();
        return response()->json(['img' => $game->img]);
    }
}
