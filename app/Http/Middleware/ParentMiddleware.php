<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ParentMiddleware
{
    public function handle($request, Closure $next)
    {
        // 未ログインなら何もせず通す（auth ミドルウェアが処理する）
        if (!Auth::check()) {
            return $next($request);
        }

        // ログイン済みなら role をチェック
        if (Auth::user()->role === 'parent') {
            return $next($request);
        }

        return redirect('/login');
    }
}