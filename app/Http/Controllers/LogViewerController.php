<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogViewerController extends Controller
{
    public function index(Request $request)
    {
        $logsDir = storage_path('logs');
        $lines = $request->get('lines', 500);
        $search = $request->get('search', '');
        $logFile = $request->get('file', 'laravel.log');

        // Get all log files
        $logFiles = [];
        if (File::isDirectory($logsDir)) {
            $allFiles = File::files($logsDir);
            foreach ($allFiles as $file) {
                if (strpos($file->getFilename(), '.log') !== false) {
                    $logFiles[] = $file->getFilename();
                }
            }
            rsort($logFiles); // Newest first
        }

        // Default to first file if specified not found
        if (!in_array($logFile, $logFiles) && count($logFiles) > 0) {
            $logFile = $logFiles[0];
        }

        $logPath = $logsDir . '/' . $logFile;

        if (!File::exists($logPath)) {
            return view('logs.viewer', [
                'logs' => 'Log file not found: ' . $logFile,
                'lines' => $lines,
                'search' => $search,
                'logFile' => $logFile,
                'logFiles' => $logFiles
            ]);
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
            'search' => $search,
            'logFile' => $logFile,
            'logFiles' => $logFiles
        ]);
    }

    public function clear(Request $request)
    {
        $logFile = $request->get('file', 'laravel.log');
        $logPath = storage_path('logs/' . $logFile);

        if (File::exists($logPath)) {
            File::put($logPath, '');
        }

        return redirect()->route('logs.viewer', ['file' => $logFile])->with('success', 'Log file cleared successfully');
    }
}
