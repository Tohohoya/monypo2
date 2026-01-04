<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\ParentChoreController;
use App\Http\Controllers\ChildChoreController;
use App\Http\Controllers\ParentRewardController;
use App\Http\Controllers\ChildRewardController;
use App\Http\Controllers\ParentRewardRequestController;

require __DIR__.'/auth.php';

// トップページ
Route::get('/', function () {
    return view('welcome');
});

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

// ログイン後の振り分け（auth のみ）
Route::get('/dashboard', function () {
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    if ($user->role === 'parent') {
        return redirect()->route('parent.dashboard');
    }

    return redirect()->route('child.dashboard');
})->middleware('auth')->name('dashboard');


// =============================
// 親専用ルート
// =============================
Route::prefix('parent')->middleware(['auth', 'parent'])->group(function () {

    Route::get('/dashboard', [ParentDashboardController::class, 'index'])
        ->name('parent.dashboard');

    // お手伝い
    Route::get('/chores', [ParentChoreController::class, 'index'])
        ->name('parent.chores.index');

    Route::get('/chores/pending', [ParentChoreController::class, 'pending'])
        ->name('parent.chores.pending');

    Route::get('/chores/{chore}/approve', [ParentChoreController::class, 'approve'])
        ->name('parent.chores.approve');

    Route::get('/chores/create', [ParentChoreController::class, 'create'])
        ->name('parent.chores.create');

    Route::post('/chores', [ParentChoreController::class, 'store'])
        ->name('parent.chores.store');

    // ご褒美
    Route::get('/rewards', [ParentRewardController::class, 'index'])
        ->name('parent.rewards.index');

    Route::get('/rewards/create', [ParentRewardController::class, 'create'])
        ->name('parent.rewards.create');

    Route::post('/rewards', [ParentRewardController::class, 'store'])
        ->name('parent.rewards.store');

    Route::get('/rewards/{reward}/edit', [ParentRewardController::class, 'edit'])
        ->name('parent.rewards.edit');

    Route::put('/rewards/{reward}', [ParentRewardController::class, 'update'])
        ->name('parent.rewards.update');

    Route::delete('/rewards/{reward}', [ParentRewardController::class, 'destroy'])
        ->name('parent.rewards.destroy');

    // ご褒美交換申請
    Route::get('/reward_requests', [ParentRewardRequestController::class, 'index'])
        ->name('parent.reward_requests.index');

    Route::post('/reward_requests/{id}/approve', [ParentRewardRequestController::class, 'approve'])
        ->name('parent.reward_requests.approve');

    Route::post('/reward_requests/{id}/reject', [ParentRewardRequestController::class, 'reject'])
        ->name('parent.reward_requests.reject');
});


// =============================
// 子ども専用ルート
// =============================
Route::prefix('child')->middleware(['auth', 'child'])->group(function () {

    Route::get('/dashboard', function () {
        return view('child.dashboard');
    })->name('child.dashboard');

    Route::get('/chores', [ChildChoreController::class, 'index'])
        ->name('child.chores.index');

    Route::get('/rewards', [ChildRewardController::class, 'index'])
        ->name('child.rewards.index');

    Route::post('/rewards/{reward}/request', [ChildRewardController::class, 'request'])
        ->name('child.rewards.request');

    Route::post('/chores/{id}/complete', [ChildChoreController::class, 'complete'])
        ->name('child.chores.complete');
});