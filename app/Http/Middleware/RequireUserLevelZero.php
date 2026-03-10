<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireUserLevelZero
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()?->level !== 0) {
            abort(403);
        }

        return $next($request);
    }
}

