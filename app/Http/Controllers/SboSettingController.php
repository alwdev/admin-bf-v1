<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Members;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SboSettingController extends Controller
{
    public function index()
    {
        return view('sbo.settings.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'min' => 'required|integer|min:0',
            'max' => 'required|integer|min:0',
            'max_per_match' => 'required|integer|min:0',
            'casino_table_limit' => 'required|integer|min:1|max:4',
        ]);

        // Increase execution time for bulk updates
        set_time_limit(0);

        $min = $request->min;
        $max = $request->max;
        $maxPerMatch = $request->max_per_match;
        $casinoTableLimit = $request->casino_table_limit;

        $companyKey = env('SBO_SEAMLESS_COMPANY_KEY');
        $serverId = env('SBO_SERVER_ID');
        $prefix = env('SBO_AGENT_USERNAME_PREFIX', 'tau_');
        $baseUrl = env('SBO_BASE_URL');
        // Ensure base URL doesn't end with slash if we append one, or handle it properly.
        // Assuming env variable doesn't have trailing slash based on previous check.
        $url = rtrim($baseUrl, '/') . '/web-root/restricted/agent/update-agent-preset-bet-settings.aspx';

        $successCount = 0;
        $failCount = 0;

        // Process in chunks to manage memory
        Members::chunk(100, function ($members) use ($min, $max, $maxPerMatch, $casinoTableLimit, $companyKey, $serverId, $prefix, $url, &$successCount, &$failCount) {
            foreach ($members as $member) {
                // Construct username with prefix
                $username = $prefix . $member->username;

                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post($url, [
                        'Username' => $username,
                        'Min' => $min,
                        'Max' => $max,
                        'MaxPerMatch' => $maxPerMatch,
                        'CasinoTableLimit' => $casinoTableLimit,
                        'CompanyKey' => $companyKey,
                        'ServerId' => $serverId,
                    ]);

                    $data = $response->json();

                    // Check for success response: {"error": {"id": 0, "msg": "No Error"}}
                    if (isset($data['error']) && $data['error']['id'] === 0) {
                        $successCount++;
                    } else {
                        Log::error("SBO Update Failed for $username: " . json_encode($data));
                        $failCount++;
                    }
                } catch (\Exception $e) {
                    Log::error("SBO Update Exception for $username: " . $e->getMessage());
                    $failCount++;
                }
            }
        });

        return redirect()->back()->with('success', "อัปเดตการตั้งค่าสำเร็จ ($successCount รายการ) ผิดพลาด ($failCount รายการ)");
    }
}
