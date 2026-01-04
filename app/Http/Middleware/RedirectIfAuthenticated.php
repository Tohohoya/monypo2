<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        if (Auth::check()) {
            // ★ ログイン済みの人が /login に来たときの行き先をここで決める
            // いまはとりあえずトップに返す
            return redirect('/');
            // もしくは role で分岐したければ：
            /*
            $user = Auth::user();
            if ($user->role === 'parent') {
                return redirect()->route('parent.dashboard');
            }
            return redirect()->route('child.dashboard');
            */
        }

        return $next($request);
    }
}
