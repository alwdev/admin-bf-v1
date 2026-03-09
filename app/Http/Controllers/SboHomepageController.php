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
        $hotSelected = SboHomepageItem::where('category', 'hot')->orderBy('position')->with('game')->get();
        $slotSelected = SboHomepageItem::where('category', 'slot')->orderBy('position')->with('game')->get();
        $liveSelected = SboHomepageItem::where('category', 'livecasino')->orderBy('position')->with('game')->get();

        // ดึงเฉพาะที่เลือกไว้ก่อนเพื่อความเร็วในการโหลดครั้งแรก
        // ส่วนรายการเกมทั้งหมดจะใช้การค้นหาผ่าน Ajax แทนเพื่อลด Memory Usage
        return view('sbo.homepage.index', compact(
            'hotSelected', 'slotSelected', 'liveSelected'
        ));
    }

    public function searchGames(Request $request)
    {
        $q = $request->input('q');
        $type = $request->input('type'); // EGAMES, LIVECASINO or all

        $query = SboGameList::where('active', 1);

        if ($type === 'EGAMES') {
            // ค้นหาแบบยืดหยุ่นสำหรับ Slot
            $query->where(function($sub) {
                $sub->where('provider_type', 'EGAMES')
                    ->orWhere('provider_type', 'like', '%slot%')
                    ->orWhere('provider_type', 'like', '%game%');
            });
        } elseif ($type === 'LIVECASINO') {
            // ค้นหาแบบยืดหยุ่นสำหรับ Live Casino
            $query->where(function($sub) {
                $sub->where('provider_type', 'LIVECASINO')
                    ->orWhere('provider_type', 'like', '%casino%')
                    ->orWhere('provider_type', 'like', '%live%');
            });
        }

        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('game_name', 'like', "%$q%")
                    ->orWhere('provider_name', 'like', "%$q%")
                    ->orWhere('game_code', 'like', "%$q%");
            });
        }

        $games = $query->orderBy('provider_name')->orderBy('game_name')->limit(50)->get();

        // หากไม่พบผลลัพธ์ด้วยการกรองประเภท ให้ลองค้นหาแบบไม่กรองประเภทเป็นทางเลือกสำรอง
        if ($games->isEmpty() && $q && $type !== 'all') {
            $games = SboGameList::where('active', 1)
                ->where(function($sub) use ($q) {
                    $sub->where('game_name', 'like', "%$q%")
                        ->orWhere('provider_name', 'like', "%$q%")
                        ->orWhere('game_code', 'like', "%$q%");
                })
                ->orderBy('provider_name')->orderBy('game_name')
                ->limit(50)->get();
        }

        return response()->json($games);
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

