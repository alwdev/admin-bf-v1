<?php

namespace App\Services;

use App\Models\AmbCategory;
use App\Models\AmbGame;
use App\Models\AmbProduct;
use Illuminate\Support\Arr;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AmbSeamlessSyncService
{
    /**
     * @return array{categories:int,products:int,games:int,warnings:array<int,string>}
     */
    public function syncAll(): array
    {
        $stats = ['categories' => 0, 'products' => 0, 'games' => 0, 'warnings' => []];
        $base = config('amb_seamless.base_url');
        if ($base === '') {
            $stats['warnings'][] = 'ยังไม่ได้ตั้ง AMB_SEAMLESS_BASE_URL ใน .env';

            return $stats;
        }

        $paths = config('amb_seamless.paths');
        $timeout = (int) config('amb_seamless.timeout', 120);

        $catResult = $this->syncCategories($base . ($paths['categories'] ?? '/seamless/categories'), $timeout);
        $stats['categories'] = $catResult['count'];
        foreach ($catResult['warnings'] as $w) {
            $stats['warnings'][] = $w;
        }

        $prodResult = $this->syncProducts($base . ($paths['products'] ?? '/seamless/products'), $timeout);
        $stats['products'] = $prodResult['count'];
        foreach ($prodResult['warnings'] as $w) {
            $stats['warnings'][] = $w;
        }

        $productsForGames = AmbProduct::query()->orderBy('order_no')->orderBy('id')->get();
        if ($productsForGames->isEmpty()) {
            $stats['warnings'][] = 'ไม่มี provider ในระบบ — เพิ่มจาก API products หรือสร้างด้วยมือก่อนซิงค์เกม';

            return $stats;
        }

        $gamesPath = $base . ($paths['games'] ?? '/seamless/games');
        $pauseMs = (int) config('amb_seamless.pause_ms_between_products', 0);

        foreach ($productsForGames as $product) {
            try {
                $n = $this->syncGamesForProduct($gamesPath, $product, $timeout);
                $stats['games'] += $n;
            } catch (Throwable $e) {
                $msg = $product->product_code . ': ' . $e->getMessage();
                $stats['warnings'][] = $msg;
                Log::warning('AMB seamless sync games failed', ['product' => $product->product_code, 'e' => $e]);
            }
            if ($pauseMs > 0) {
                usleep($pauseMs * 1000);
            }
        }

        return $stats;
    }

    /**
     * @return array{count:int,warnings:array<int,string>}
     */
    private function syncCategories(string $url, int $timeout): array
    {
        $warnings = [];
        try {
            $response = $this->http()->timeout($timeout)->get($url);
            if ($response->status() === 404) {
                $warnings[] = 'GET categories ได้ 404 — แก้ API /seamless/categories หรือสร้างหมวดในแอดมินเอง';

                return ['count' => 0, 'warnings' => $warnings];
            }
            $response->throw();
            $rows = $this->extractList($response->json());
            $count = 0;
            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $code = $this->pickString($row, config('amb_seamless.keys.category_code'));
                $name = $this->pickString($row, config('amb_seamless.keys.category_name'));
                if ($code === null || $name === null) {
                    continue;
                }
                $code = strtoupper(trim($code));
                $existingCat = AmbCategory::where('code', $code)->first();
                AmbCategory::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $name,
                        'active' => true,
                        'order_no' => $existingCat?->order_no ?? 0,
                    ]
                );
                $count++;
            }

            return ['count' => $count, 'warnings' => $warnings];
        } catch (Throwable $e) {
            $warnings[] = 'categories: ' . $e->getMessage();

            return ['count' => 0, 'warnings' => $warnings];
        }
    }

    /**
     * @return array{count:int,warnings:array<int,string>}
     */
    private function syncProducts(string $url, int $timeout): array
    {
        $warnings = [];
        try {
            $response = $this->http()->timeout($timeout)->get($url);
            $response->throw();
            $rows = $this->extractList($response->json());
            $count = 0;
            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $pCode = $this->pickString($row, config('amb_seamless.keys.product_code'));
                $pName = $this->pickString($row, config('amb_seamless.keys.product_name'));
                if ($pCode === null || $pName === null) {
                    continue;
                }
                $pCode = trim($pCode);
                $catCode = $this->pickString($row, config('amb_seamless.keys.product_category'));
                $categoryId = null;
                if ($catCode !== null) {
                    $categoryId = AmbCategory::where('code', strtoupper(trim($catCode)))->value('id');
                }
                if ($categoryId === null) {
                    $existing = AmbProduct::where('product_code', $pCode)->first();
                    $categoryId = $existing?->amb_category_id;
                }
                if ($categoryId === null) {
                    $warnings[] = "ข้าม provider {$pCode}: ไม่พบหมวดในระบบ — กำหนด category ใน API หรือผูกในแอดมินก่อน";

                    continue;
                }
                $existingProd = AmbProduct::where('product_code', $pCode)->first();
                AmbProduct::updateOrCreate(
                    ['product_code' => $pCode],
                    [
                        'product_name' => $pName,
                        'amb_category_id' => $categoryId,
                        'active' => true,
                        'order_no' => $existingProd?->order_no ?? 0,
                    ]
                );
                $count++;
            }

            return ['count' => $count, 'warnings' => $warnings];
        } catch (Throwable $e) {
            $warnings[] = 'products: ' . $e->getMessage();

            return ['count' => 0, 'warnings' => $warnings];
        }
    }

    private function syncGamesForProduct(string $gamesBaseUrl, AmbProduct $product, int $timeout): int
    {
        $style = config('amb_seamless.games_style', 'query');
        $param = config('amb_seamless.games_query_param', 'product');
        $url = $gamesBaseUrl;
        if ($style === 'path') {
            $url = rtrim($gamesBaseUrl, '/') . '/' . rawurlencode($product->product_code);
        } else {
            $url = $gamesBaseUrl . (str_contains($gamesBaseUrl, '?') ? '&' : '?') . $param . '=' . rawurlencode($product->product_code);
        }

        $response = $this->http()->timeout($timeout)->get($url);
        $response->throw();
        $rows = $this->extractList($response->json());
        $count = 0;
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $gCode = $this->pickString($row, config('amb_seamless.keys.game_code'));
            $gName = $this->pickString($row, config('amb_seamless.keys.game_name'));
            if ($gCode === null || $gName === null) {
                continue;
            }
            $gCode = trim($gCode);
            $gType = $this->pickString($row, config('amb_seamless.keys.game_type'));
            $img = $this->pickString($row, config('amb_seamless.keys.img'));
            $prov = $this->pickString($row, config('amb_seamless.keys.provider_code')) ?? $product->product_code;

            AmbGame::updateOrCreate(
                [
                    'amb_product_id' => $product->id,
                    'game_code' => mb_substr($gCode, 0, 150),
                ],
                [
                    'amb_category_id' => $product->amb_category_id,
                    'game_name' => mb_substr($gName, 0, 255),
                    'game_type' => $gType !== null ? mb_substr($gType, 0, 64) : null,
                    'img' => $img,
                    'provider_code' => $prov !== null ? mb_substr($prov, 0, 64) : null,
                    'active' => true,
                ]
            );
            $count++;
        }

        return $count;
    }

    private function http(): PendingRequest
    {
        $req = Http::acceptJson();
        $token = config('amb_seamless.bearer_token');
        if (is_string($token) && $token !== '') {
            $req = $req->withToken($token);
        }

        return $req;
    }

    /**
     * @param  mixed  $json
     * @return array<int, mixed>
     */
    private function extractList($json): array
    {
        if (! is_array($json)) {
            return [];
        }
        if (array_is_list($json)) {
            return $json;
        }
        foreach (['data', 'items', 'results', 'games', 'products', 'categories'] as $k) {
            if (isset($json[$k]) && is_array($json[$k])) {
                return array_is_list($json[$k]) ? $json[$k] : array_values($json[$k]);
            }
        }

        return [];
    }

    /**
     * @param  array<string,mixed>  $row
     * @param  array<int,string>|mixed  $keys
     */
    private function pickString(array $row, $keys): ?string
    {
        $keys = is_array($keys) ? $keys : [];
        foreach ($keys as $key) {
            if (! is_string($key)) {
                continue;
            }
            if (! Arr::has($row, $key)) {
                continue;
            }
            $v = Arr::get($row, $key);
            if ($v === null || $v === '') {
                continue;
            }
            if (is_scalar($v)) {
                return trim((string) $v);
            }
        }

        return null;
    }
}
