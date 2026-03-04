<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Models\Logs;

class SboApiController extends Controller
{
    private function txn(string $p): string
    {
        return $p . date('YmdHis') . mt_rand(10000, 99999);
    }

    public function deposit(string $username, float $amount): array
    {
        $baseUrl = rtrim(env('SBO_BASE_URL', ''), '/');
        $companyKey = env('SBO_COMPANY_KEY', '');
        $serverId = env('SBO_SERVER_ID', '');
        $prefix = env('SBO_AGENT_USERNAME_PREFIX', '');
        if ($baseUrl === '' || $companyKey === '' || $serverId === '') {
            throw new \RuntimeException('SBO env missing');
        }
        if ($prefix !== '' && strncmp($username, $prefix, strlen($prefix)) !== 0) {
            $username = $prefix . $username;
        }
        $client = new Client(['timeout' => 10]);
        $payload = [
            'Username'   => $username,
            'txnId'      => $this->txn('D'),
            'Amount'     => (float) $amount,
            'CompanyKey' => $companyKey,
            'ServerId'   => $serverId,
        ];
        $response = $client->post($baseUrl . '/web-root/restricted/player/deposit.aspx', [
            'headers' => ['Content-Type' => 'application/json'],
            'json'    => $payload,
        ]);
        $body = (string) $response->getBody();
        Log::info('SBO deposit resp ' . $body);
        $log = new Logs;
        $log->username = $username;
        $log->log = 'SBO deposit resp ' . $body;
        $log->save();
        $resp = json_decode($body, true);
        $resp = is_array($resp) ? $resp : [];
        $resp['__raw'] = $body;
        $resp['__username'] = $username;
        return $resp;
    }

    public function withdraw(string $username, float $amount, bool $isFullAmount = false): array
    {
        $baseUrl = rtrim(env('SBO_BASE_URL', ''), '/');
        $companyKey = env('SBO_COMPANY_KEY', '');
        $serverId = env('SBO_SERVER_ID', '');
        $prefix = env('SBO_AGENT_USERNAME_PREFIX', '');
        if ($baseUrl === '' || $companyKey === '' || $serverId === '') {
            throw new \RuntimeException('SBO env missing');
        }
        if ($prefix !== '' && strncmp($username, $prefix, strlen($prefix)) !== 0) {
            $username = $prefix . $username;
        }
        $client = new Client(['timeout' => 10]);
        $payload = [
            'Username'     => $username,
            'txnId'        => $this->txn('W'),
            'IsFullAmount' => $isFullAmount,
            'Amount'       => abs($amount),
            'CompanyKey'   => $companyKey,
            'ServerId'     => $serverId,
        ];
        $response = $client->post($baseUrl . '/web-root/restricted/player/withdraw.aspx', [
            'headers' => ['Content-Type' => 'application/json'],
            'json'    => $payload,
        ]);
        $body = (string) $response->getBody();
        Log::info('SBO withdraw resp ' . $body);
        $log = new Logs;
        $log->username = $username;
        $log->log = 'SBO withdraw resp ' . $body;
        $log->save();
        $resp = json_decode($body, true);
        $resp = is_array($resp) ? $resp : [];
        $resp['__raw'] = $body;
        $resp['__username'] = $username;
        return $resp;
    }
}

