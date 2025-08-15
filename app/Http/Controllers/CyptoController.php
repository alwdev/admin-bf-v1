<?php

namespace App\Http\Controllers;

use App\Models\Cypto;
use Illuminate\Http\Request;

class CyptoController extends Controller
{
    public function index()
    {
        $coins = Cypto::get();
        return view('crypto.index', compact('coins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'chain' => 'required|string|max:50',
        ]);

        Cypto::create($request->all());

        return redirect()->route('crypto.index')->with('success', 'Cryptocurrency added successfully.');
    }
    public function update(Request $request)
    {

        // $request->validate([
        //     'id' => 'required|integer|exists:cryptos,id',
        //     'price' => 'required|numeric|min:0',
        // ]);
        error_log($request->id);
        error_log($request->price);

        $crypto = Cypto::where('id', $request->id)->first();
        error_log($crypto->price);
        try {
            if (!$crypto) {
                return response()->json(['error' => 'Cryptocurrency not found.'], 404);
            }else{
                $crypto->price = $request->price;
                $crypto->save();
            }
        } catch (\Exception $e) {
            error_log('Error updating cryptocurrency: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching cryptocurrency: ' . $e->getMessage()], 500);
        }
        error_log($crypto->price);
        return response()->json(['success' => true, 'message' => 'Cryptocurrency updated successfully.'], 200);
    }
}
