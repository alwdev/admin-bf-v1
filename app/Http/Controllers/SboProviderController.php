<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SboProvider;

class SboProviderController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int)($request->get('per_page', 50));
        if ($perPage < 10) {
            $perPage = 10;
        }
        if ($perPage > 200) {
            $perPage = 200;
        }

        $q = SboProvider::query();
        if ($request->filled('name')) {
            $q->where('name', 'like', '%' . $request->get('name') . '%');
        }
        if ($request->filled('type') && $request->get('type') !== '') {
            $q->where('type', $request->get('type'));
        }
        if ($request->filled('active') && $request->get('active') !== 'all') {
            $q->where('active', (int)$request->get('active'));
        }
        $providers = $q->orderBy('name')->paginate($perPage)->appends($request->query());
        $types = ['Games', 'EGAMES', 'LIVECASINO', 'SPORT'];
        return view('sbo.providers.index', compact('providers', 'perPage', 'types'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gpid' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'lobby_game_id' => 'nullable|integer',
            'img' => 'nullable|image|max:2048',
            'supports_game_id_login' => 'nullable|boolean',
            'devices' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('images/sbo/providers', 'public');
            $data['img'] = '/storage/' . $path;
        }
        $data['active'] = $request->boolean('active');
        SboProvider::create($data);
        return redirect()->route('sbo.providers.index')->with('success', 'Created');
    }

    public function update(Request $request, $id)
    {
        $provider = SboProvider::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'gpid' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'lobby_game_id' => 'nullable|integer',
            'img' => 'nullable|image|max:2048',
            'supports_game_id_login' => 'nullable|boolean',
            'devices' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('images/sbo/providers', 'public');
            $data['img'] = '/storage/' . $path;
        }
        $data['active'] = $request->boolean('active');
        $provider->update($data);
        return redirect()->route('sbo.providers.index')->with('success', 'Updated');
    }

    public function destroy($id)
    {
        $provider = SboProvider::findOrFail($id);
        $provider->delete();
        return redirect()->route('sbo.providers.index')->with('success', 'Deleted');
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
        $provider = SboProvider::findOrFail($id);
        $provider->active = ((int)$checked) ? 1 : 0;
        $provider->save();
        return response()->json(true);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'imgupload' => 'required|image|max:4096',
        ]);
        $provider = SboProvider::findOrFail($request->id);
        $path = $request->file('imgupload')->store('images/sbo/providers', 'public');
        $provider->img = '/storage/' . $path;
        $provider->save();
        return response()->json(['img' => $provider->img]);
    }
}
