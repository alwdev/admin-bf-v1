<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SboSettingController extends Controller
{
    public function index()
    {
        return view('sbo.settings.index');
    }

    private function pickFirst(array $arr, array $keys)
    {
        foreach ($keys as $k) {
            if (array_key_exists($k, $arr)) {
                return $arr[$k];
            }
        }
        return null;
    }

    public function update(Request $request)
    {
        $request->validate([
            'min' => 'required|integer|min:0',
            'max' => 'required|integer|min:0',
            'max_per_match' => 'required|integer|min:0',
            'casino_table_limit' => 'required|integer|min:1|max:4',
        ]);

        $min = $request->min;
        $max = $request->max;
        $maxPerMatch = $request->max_per_match;
        $casinoTableLimit = $request->casino_table_limit;

        $companyKey = env('SBO_COMPANY_KEY');
        $serverId = env('SBO_SERVER_ID');
        $agentUsername = env('SBO_AGENT'); // Get Agent Username from ENV
        $baseUrl = env('SBO_BASE_URL');
        $url = rtrim($baseUrl, '/') . '/web-root/restricted/agent/update-agent-preset-bet-settings.aspx';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'Username' => $agentUsername,
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
                return redirect()->back()->with('success', "อัปเดตการตั้งค่า Agent ($agentUsername) สำเร็จ");
            } else {
                Log::error("SBO Update Agent Settings Failed: " . json_encode($data));
                $errorMsg = isset($data['error']['msg']) ? $data['error']['msg'] : 'Unknown Error';
                return redirect()->back()->withErrors(['api_error' => "อัปเดตไม่สำเร็จ: $errorMsg"]);
            }
        } catch (\Exception $e) {
            Log::error("SBO Update Agent Settings Exception: " . $e->getMessage());
            return redirect()->back()->withErrors(['api_error' => "เกิดข้อผิดพลาดในการเชื่อมต่อ: " . $e->getMessage()]);
        }
    }

    public function balance(Request $request)
    {
        $username = $request->get('username', env('SBO_AGENT'));
        $companyKey = env('SBO_COMPANY_KEY');
        $serverId = env('SBO_SERVER_ID');
        $baseUrl = env('SBO_BASE_URL');
        $endpoint = rtrim($baseUrl, '/') . '/web-root/restricted/player/get-player-balance.aspx';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($endpoint, [
                'Username' => $username,
                'CompanyKey' => $companyKey,
                'ServerId' => $serverId,
            ]);
            $data = $response->json();
            if (!is_array($data)) {
                return response()->json(['success' => false, 'message' => 'Invalid response', 'raw' => $response->body()], 502);
            }
            $balance = (float) ($this->pickFirst($data, ['balance', 'Balance']) ?? 0);
            $outstanding = (float) ($this->pickFirst($data, ['outstanding', 'Outstanding']) ?? 0);
            $currency = (string) ($this->pickFirst($data, ['currency', 'Currency']) ?? '');
            $server = (string) ($this->pickFirst($data, ['serverId', 'ServerId']) ?? '');
            $respUser = (string) ($this->pickFirst($data, ['username', 'Username']) ?? $username);
            $errorBlock = $data['error'] ?? $data['Error'] ?? null;
            $errorId = is_array($errorBlock) ? ($errorBlock['id'] ?? $errorBlock['Id'] ?? null) : null;
            $errorMsg = is_array($errorBlock) ? ($errorBlock['msg'] ?? $errorBlock['Msg'] ?? null) : null;
            $success = ($errorId === 0);
            return response()->json([
                'success' => $success,
                'username' => $respUser,
                'currency' => $currency,
                'balance' => $balance,
                'outstanding' => $outstanding,
                'serverId' => $server,
                'error' => $errorBlock,
                'message' => $errorMsg,
                'raw' => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('SBO Get Balance Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
