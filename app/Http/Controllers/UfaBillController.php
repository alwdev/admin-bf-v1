<?php

namespace App\Http\Controllers;

use App\Models\UfaTransaction;
use Illuminate\Http\Request;

class UfaBillController extends Controller
{
    private function extractSafeBodyHtml(string $html): ?string
    {
        if (trim($html) === '') {
            return null;
        }

        if (!class_exists(\DOMDocument::class)) {
            return null;
        }

        $prev = libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR);

        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query('//script|//link|//style') as $node) {
            $node->parentNode?->removeChild($node);
        }
        foreach ($xpath->query('//*[@onload or @onclick or @onerror or @onmouseover or @onfocus or @onsubmit]') as $node) {
            foreach (['onload', 'onclick', 'onerror', 'onmouseover', 'onfocus', 'onsubmit'] as $attr) {
                if ($node->hasAttribute($attr)) {
                    $node->removeAttribute($attr);
                }
            }
        }

        $body = $dom->getElementsByTagName('body')->item(0);
        if (!$body) {
            libxml_use_internal_errors($prev);
            return null;
        }

        $safe = '';
        foreach ($body->childNodes as $child) {
            $safe .= $dom->saveHTML($child);
        }

        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        return trim($safe) !== '' ? $safe : null;
    }

    public function show(Request $request, string $betId)
    {
        $tx = UfaTransaction::where('bet_id', $betId)->orderByDesc('id')->firstOrFail();

        $apiUrl = rtrim((string) env('UFABET_BILL_API_URL', 'https://api.ax928.com'), '/');
        $apiKey = (string) env('UFABET_BILL_APIKEY', '');

        $apiEnabled = $apiKey !== '';
        $apiError = null;
        $apiResponseRaw = null;
        $apiResponseJson = null;
        $apiResponseHtml = null;

        if ($apiEnabled) {
            try {
                $client = new \GuzzleHttp\Client([
                    'timeout' => 12,
                    'connect_timeout' => 6,
                ]);

                $resp = $client->request('GET', $apiUrl . '/bill', [
                    'query' => [
                        'apikey' => md5($apiKey),
                        'user' => $tx->username,
                        'bill_id' => $betId,
                    ],
                ]);

                $apiResponseRaw = (string) $resp->getBody();
                $decoded = json_decode($apiResponseRaw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $apiResponseJson = $decoded;
                } else {
                    $apiResponseHtml = $this->extractSafeBodyHtml($apiResponseRaw);
                }
            } catch (\Throwable $e) {
                $apiError = $e->getMessage();
            }
        } else {
            $apiError = 'UFABET_BILL_APIKEY is not configured';
        }

        return view('report.ufa_bill_detail', compact(
            'tx',
            'betId',
            'apiUrl',
            'apiEnabled',
            'apiError',
            'apiResponseRaw',
            'apiResponseJson',
            'apiResponseHtml'
        ));
    }
}
