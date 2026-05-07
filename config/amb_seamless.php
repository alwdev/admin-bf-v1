<?php

/**
 * ซิงค์ข้อมูล AMB จาก Seamless API (ฝั่งเกม / Nest ฯลฯ)
 *
 * ตั้งค่าใน .env:
 * - AMB_SEAMLESS_BASE_URL=https://your-api.example.com
 * - AMB_SEAMLESS_BEARER_TOKEN=... (ถ้ามี)
 *
 * ถ้า endpoint หมวดยัง 404 ให้แก้ API ก่อน — ระบบจะข้ามการซิงค์หมวดและใช้หมวดที่มีใน DB อยู่แล้ว
 */
return [
    'base_url' => rtrim((string) env('AMB_SEAMLESS_BASE_URL', ''), '/'),

    'timeout' => (int) env('AMB_SEAMLESS_TIMEOUT', 120),

    'bearer_token' => env('AMB_SEAMLESS_BEARER_TOKEN'),

    'paths' => [
        'categories' => env('AMB_SEAMLESS_PATH_CATEGORIES', '/seamless/categories'),
        'products' => env('AMB_SEAMLESS_PATH_PRODUCTS', '/seamless/products'),
        'games' => env('AMB_SEAMLESS_PATH_GAMES', '/seamless/games'),
    ],

    /** query: GET .../games?product=PGSOFT | path: GET .../games/PGSOFT */
    'games_style' => env('AMB_SEAMLESS_GAMES_STYLE', 'query'),
    'games_query_param' => env('AMB_SEAMLESS_GAMES_QUERY_PARAM', 'product'),

    /** ชื่อฟิลด์ที่ยอมรับได้ในแต่ละแถว JSON (ลองตามลำดับ) */
    'keys' => [
        'category_code' => ['code', 'category_code', 'categoryCode', 'id'],
        'category_name' => ['name', 'title', 'label'],
        'product_code' => ['product_code', 'code', 'productCode', 'provider', 'provider_code'],
        'product_name' => ['product_name', 'name', 'title', 'label'],
        'product_category' => ['category_code', 'category', 'categoryCode', 'amb_category'],
        'game_code' => ['game_code', 'code', 'gameCode', 'launchCode'],
        'game_name' => ['game_name', 'name', 'title', 'gameName'],
        'game_type' => ['game_type', 'type', 'gameType'],
        'img' => ['img', 'image', 'bannerUrl', 'banner', 'icon'],
        'provider_code' => ['provider_code', 'provider', 'providerCode'],
    ],

    'pause_ms_between_products' => (int) env('AMB_SEAMLESS_PAUSE_MS', 0),
];
