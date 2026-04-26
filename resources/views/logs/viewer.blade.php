<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Log Viewer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            min-height: 100vh;
        }
        .header {
            background: #2d2d2d;
            padding: 15px 20px;
            border-bottom: 1px solid #3e3e3e;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .header h1 {
            color: #fff;
            font-size: 18px;
            margin-right: auto;
        }
        .header form {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header input, .header select {
            background: #3e3e3e;
            border: 1px solid #555;
            color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            font-family: inherit;
        }
        .header button {
            background: #0e639c;
            border: none;
            color: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-family: inherit;
        }
        .header button:hover {
            background: #1177bb;
        }
        .header .clear-btn {
            background: #c75450;
        }
        .header .clear-btn:hover {
            background: #d46460;
        }
        .log-container {
            padding: 20px;
            overflow-x: auto;
        }
        .log-content {
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 12px;
            line-height: 1.6;
        }
        .log-line {
            padding: 2px 0;
            border-bottom: 1px solid #2d2d2d;
        }
        .log-line:hover {
            background: #2d2d2d;
        }
        .error { color: #f48771; }
        .warning { color: #dcdcaa; }
        .info { color: #4ec9b0; }
        .debug { color: #9cdcfe; }
        .success-message {
            background: #4ec9b0;
            color: #1e1e1e;
            padding: 10px 20px;
            margin: 10px 20px;
            border-radius: 4px;
        }
        .no-logs {
            color: #808080;
            font-style: italic;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Laravel Log Viewer</h1>
        
        <form method="GET" action="{{ route('logs.viewer') }}">
            <select name="file" onchange="this.form.submit()">
                @foreach($logFiles as $file)
                    <option value="{{ $file }}" {{ $logFile == $file ? 'selected' : '' }}>{{ $file }}</option>
                @endforeach
            </select>
            <input type="text" name="search" placeholder="Search..." value="{{ $search }}">
            <select name="lines">
                <option value="100" {{ $lines == 100 ? 'selected' : '' }}>100 lines</option>
                <option value="500" {{ $lines == 500 ? 'selected' : '' }}>500 lines</option>
                <option value="1000" {{ $lines == 1000 ? 'selected' : '' }}>1000 lines</option>
                <option value="5000" {{ $lines == 5000 ? 'selected' : '' }}>5000 lines</option>
            </select>
            <button type="submit">🔍 View</button>
        </form>
        
        <form method="POST" action="{{ route('logs.clear') }}?file={{ $logFile }}" onsubmit="return confirm('Are you sure you want to clear {{ $logFile }}?');">
            @csrf
            <button type="submit" class="clear-btn">🗑️ Clear</button>
        </form>
    </div>

    @if(session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    <div class="log-container">
        @if(empty(trim($logs)))
            <div class="no-logs">No logs found</div>
        @else
            <div class="log-content">
                @foreach(explode("\n", $logs) as $line)
                    @php
                        $class = '';
                        if (stripos($line, 'ERROR') !== false) $class = 'error';
                        elseif (stripos($line, 'WARNING') !== false || stripos($line, 'WARN') !== false) $class = 'warning';
                        elseif (stripos($line, 'INFO') !== false) $class = 'info';
                        elseif (stripos($line, 'DEBUG') !== false) $class = 'debug';
                    @endphp
                    <div class="log-line {{ $class }}">{{ $line }}</div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>
