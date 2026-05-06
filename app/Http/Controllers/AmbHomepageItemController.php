<?php

namespace App\Http\Controllers;

use App\Models\AmbGame;
use App\Models\AmbHomepageItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AmbHomepageItemController extends Controller
{
    public function index(Request $request)
    {
        $category = $this->normalizedCategory($request->string('category'));
        $items = AmbHomepageItem::query()
            ->where('category', $category)
            ->with(['game.product'])
            ->orderBy('position')
            ->get()
            ->keyBy('position');

        $games = AmbGame::query()
            ->with('product')
            ->where('active', true)
            ->orderBy('game_name')
            ->get();

        $sections = [
            'hot' => 'Hot',
            'slot' => 'Slot',
            'livecasino' => 'Live Casino',
        ];

        return view('amb.homepage.index', compact('category', 'items', 'games', 'sections'));
    }

    public function store(Request $request)
    {
        $category = $this->normalizedCategory($request->string('category'));
        $data = $request->validate([
            'position' => [
                'required',
                'integer',
                'min:1',
                'max:10',
                Rule::unique('amb_homepage_items', 'position')->where('category', $category),
            ],
            'amb_game_id' => [
                'required',
                'integer',
                'exists:amb_games,id',
                Rule::unique('amb_homepage_items', 'amb_game_id')->where('category', $category),
            ],
        ]);
        $data['category'] = $category;

        AmbHomepageItem::create($data);

        return redirect()
            ->route('amb.homepage.index', ['category' => $category])
            ->with('success', 'เพิ่มเกมในตำแหน่งนี้แล้ว');
    }

    public function update(Request $request, int $id)
    {
        $item = AmbHomepageItem::findOrFail($id);
        $category = $item->category;

        $data = $request->validate([
            'amb_game_id' => [
                'required',
                'integer',
                'exists:amb_games,id',
                Rule::unique('amb_homepage_items', 'amb_game_id')
                    ->where('category', $category)
                    ->ignore($item->id),
            ],
        ]);

        $item->update($data);

        return redirect()
            ->route('amb.homepage.index', ['category' => $category])
            ->with('success', 'เปลี่ยนเกมแล้ว');
    }

    public function destroy(Request $request, int $id)
    {
        $item = AmbHomepageItem::findOrFail($id);
        $category = $item->category;
        $item->delete();

        return redirect()
            ->route('amb.homepage.index', ['category' => $category])
            ->with('success', 'ลบออกจากหน้าแรกแล้ว');
    }

    private function normalizedCategory(\Illuminate\Support\Stringable $s): string
    {
        $c = $s->trim()->lower()->value();
        if (! in_array($c, AmbHomepageItem::SECTIONS, true)) {
            return AmbHomepageItem::SECTIONS[0];
        }

        return $c;
    }
}
