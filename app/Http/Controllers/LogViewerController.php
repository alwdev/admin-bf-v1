<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogViewerController extends Controller
{
    public function index(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');
        $lines = $request->get('lines', 500);
        $search = $request->get('search', '');

        if (!File::exists($logPath)) {
            return view('logs.viewer', ['logs' => 'Log file not found', 'lines' => $lines, 'search' => $search]);
        }

        // Read last N lines
        $content = File::get($logPath);
        $allLines = explode("\n", $content);
        $lastLines = array_slice($allLines, -$lines);

        // Filter by search if provided
        if ($search) {
            $lastLines = array_filter($lastLines, function($line) use ($search) {
                return stripos($line, $search) !== false;
            });
        }

        $logContent = implode("\n", $lastLines);

        return view('logs.viewer', [
            'logs' => $logContent,
            'lines' => $lines,
            'search' => $search
        ]);
    }

    public function clear()
    {
        $logPath = storage_path('logs/laravel.log');

        if (File::exists($logPath)) {
            File::put($logPath, '');
        }

        return redirect()->route('logs.viewer')->with('success', 'Log file cleared successfully');
    }
}
