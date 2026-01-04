<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ParentMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->role === 'parent') {
            return $next($request);
        }

        return redirect('/login');
    }
}