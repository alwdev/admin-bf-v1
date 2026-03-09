<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SboGameList;
use App\Models\SboHomepageItem;

class SboHomepageController extends Controller
{
    public function index(Request $request)
    {
        $hotSelected = SboHomepageItem::where('category', 'hot')->orderBy('position')->get();
        $slotSelected = SboHomepageItem::where('category', 'slot')->orderBy('position')->get();
        $liveSelected = SboHomepageItem::where('category', 'livecasino')->orderBy('position')->get();

        $allActiveGames = SboGameList::where('active', 1)->orderBy('provider_name')->orderBy('game_name')->get();
        $slotGames = SboGameList::where('active', 1)->where('provider_type', 'EGAMES')->orderBy('provider_name')->orderBy('game_name')->get();
        $liveGames = SboGameList::where('active', 1)->where('provider_type', 'LIVECASINO')->orderBy('provider_name')->orderBy('game_name')->get();

        return view('sbo.homepage.index', compact(
            'hotSelected', 'slotSelected', 'liveSelected',
            'allActiveGames', 'slotGames', 'liveGames'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'hot' => 'array',
            'slot' => 'array',
            'livecasino' => 'array',
        ]);

        $hot = array_slice(array_unique($request->input('hot', [])), 0, 10);
        $slot = array_slice(array_unique($request->input('slot', [])), 0, 10);
        $live = array_slice(array_unique($request->input('livecasino', [])), 0, 10);

        DB::transaction(function () use ($hot, $slot, $live) {
            SboHomepageItem::where('category', 'hot')->delete();
            SboHomepageItem::where('category', 'slot')->delete();
            SboHomepageItem::where('category', 'livecasino')->delete();

            foreach ($hot as $i => $gid) {
                SboHomepageItem::create([
                    'category' => 'hot',
                    'position' => $i + 1,
                    'game_list_id' => (int)$gid,
                ]);
            }
            foreach ($slot as $i => $gid) {
                SboHomepageItem::create([
                    'category' => 'slot',
                    'position' => $i + 1,
                    'game_list_id' => (int)$gid,
                ]);
            }
            foreach ($live as $i => $gid) {
                SboHomepageItem::create([
                    'category' => 'livecasino',
                    'position' => $i + 1,
                    'game_list_id' => (int)$gid,
                ]);
            }
        });

        return redirect()->route('sbo.homepage.index')->with('success', 'อัปเดตการตั้งค่าหน้าแรกสำเร็จ');
    }
}

