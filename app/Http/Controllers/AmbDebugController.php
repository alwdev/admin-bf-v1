<?php

namespace App\Http\Controllers;

use App\Models\AmbCategory;
use App\Models\AmbGame;
use App\Models\AmbProduct;

class AmbDebugController extends Controller
{
    public function index()
    {
        $baseUrl = (string) config('amb_seamless.base_url', '');
        $hasBearer = is_string(config('amb_seamless.bearer_token'))
            && trim((string) config('amb_seamless.bearer_token')) !== '';
        $hasAgent = is_string(env('AMB_AGENT')) && trim((string) env('AMB_AGENT')) !== '';
        $hasApiSecret = is_string(env('AMB_API_SECRET')) && trim((string) env('AMB_API_SECRET')) !== '';

        $agentOk = $hasAgent || $hasBearer;
        $secretOk = $hasApiSecret || $hasBearer;
        $ready = $baseUrl !== '' && ($hasBearer || ($hasAgent && $hasApiSecret));

        $catTotal = AmbCategory::count();
        $catActive = AmbCategory::where('active', true)->count();
        $prodTotal = AmbProduct::count();
        $prodActive = AmbProduct::where('active', true)->count();
        $gameTotal = AmbGame::count();
        $gameActive = AmbGame::where('active', true)->count();

        $issues = [
            'categories_no_products' => AmbCategory::query()
                ->where('active', true)
                ->whereDoesntHave('products')
                ->count(),
            'categories_no_games' => AmbCategory::query()
                ->where('active', true)
                ->whereDoesntHave('games')
                ->count(),
            'products_no_games' => AmbProduct::query()
                ->where('active', true)
                ->whereDoesntHave('games')
                ->count(),
        ];

        $checkRows = [
            ['endpoint' => '/seamless/categories', 'product' => null, 'target' => 'categories'],
            ['endpoint' => '/seamless/products', 'product' => null, 'target' => 'products'],
        ];

        foreach (
            AmbProduct::query()
                ->orderBy('product_code')
                ->get(['id', 'product_code']) as $p
        ) {
            $checkRows[] = [
                'endpoint' => '/seamless/games',
                'product' => $p->product_code,
                'target' => 'games',
            ];
        }

        return view('amb.debug.index', compact(
            'baseUrl',
            'agentOk',
            'secretOk',
            'ready',
            'catTotal',
            'catActive',
            'prodTotal',
            'prodActive',
            'gameTotal',
            'gameActive',
            'issues',
            'checkRows'
        ));
    }
}
