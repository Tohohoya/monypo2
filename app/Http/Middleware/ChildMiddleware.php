<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;   // ← これが必要！
use Illuminate\Http\Request;

class ChildMiddleware
{
public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        if (Auth::user()->role === 'child') {
            return $next($request);
        }

        return redirect('/login');
    }
}
