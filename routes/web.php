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

Route::get('/', function () {
    return view('welcome');
});

// ログイン後の振り分け
Route::get('/dashboard', function () {
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    if ($user->role === 'parent') {
        return redirect()->route('parent.dashboard');
    }

    return redirect()->route('child.dashboard');
})->name('dashboard');

// 親専用
Route::middleware(['auth', 'parent'])->group(function () {

    Route::get('/parent/dashboard', [ParentDashboardController::class, 'index'])
        ->name('parent.dashboard');
    
    Route::get('/parent/chores', [ParentChoreController::class, 'index'])
        ->name('parent.chores.index')
        ->middleware('auth', 'parent');

    Route::get('/parent/chores/pending', [ParentChoreController::class, 'pending'])
        ->name('parent.chores.pending')
        ->middleware('auth', 'parent');

    Route::get('/parent/chores/{chore}/approve', [ParentChoreController::class, 'approve'])
        ->name('parent.chores.approve');

    Route::get('/parent/chores/create', [ParentChoreController::class, 'create'])
        ->name('parent.chores.create');

    Route::post('/parent/chores', [ParentChoreController::class, 'store'])
        ->name('parent.chores.store');

    // ご褒美一覧

    Route::get('/parent/rewards', [ParentRewardController::class, 'index'])
        ->name('parent.rewards.index');

    Route::get('/parent/rewards/create', [ParentRewardController::class, 'create'])
        ->name('parent.rewards.create');

    Route::post('/parent/rewards', [ParentRewardController::class, 'store'])
        ->name('parent.rewards.store');

        Route::get('/parent/rewards/{reward}/edit', [ParentRewardController::class, 'edit'])->name('parent.rewards.edit');

    Route::put('/parent/rewards/{reward}', [ParentRewardController::class, 'update'])->name('parent.rewards.update');

    Route::delete('/parent/rewards/{reward}', [ParentRewardController::class, 'destroy'])->name('parent.rewards.destroy');

    // ご褒美交換申請一覧
    Route::get('/parent/reward_requests', [ParentRewardRequestController::class, 'index'])
        ->name('parent.reward_requests.index');
    
        // 交換申請一覧
    Route::get('/parent/reward_requests', [ParentRewardRequestController::class, 'index'])
        ->name('parent.reward_requests.index');

    // 承認
    Route::post('/parent/reward_requests/{id}/approve', [ParentRewardRequestController::class, 'approve'])
        ->name('parent.reward_requests.approve');

    // 却下
    Route::post('/parent/reward_requests/{id}/reject', [ParentRewardRequestController::class, 'reject'])
        ->name('parent.reward_requests.reject');
    
});

// 子ども専用（テストのため child ミドルウェアを外す）
Route::middleware(['auth', 'child'])->group(function () {

    Route::get('/child/dashboard', function () {
        return view('child.dashboard');
    })->name('child.dashboard');

    // ← これを追加
    Route::get('/child/chores', [ChildChoreController::class, 'index'])
        ->name('child.chores.index');

    Route::get('/child/rewards', [ChildRewardController::class, 'index'])
        ->name('child.rewards.index');
    
    Route::post('/child/rewards/{reward}/request', [ChildRewardController::class, 'request'])
        ->name('child.rewards.request');

    Route::post('/child/chores/{id}/complete', [ChildChoreController::class, 'complete'])
    ->name('child.chores.complete')
    ->middleware('auth', 'child');
});

Route::get('/debug-env', function () {
    return [
        'DB_CONNECTION' => env('DB_CONNECTION'),
        'DB_HOST' => env('DB_HOST'),
        'DB_DATABASE' => env('DB_DATABASE'),
        'DB_USERNAME' => env('DB_USERNAME'),
        'DB_PASSWORD' => env('DB_PASSWORD'),
    ];
});

Route::get('/debug-db', function () {
    try {
        $pdo = DB::connection()->getPdo();
        return [
            'driver' => DB::connection()->getDriverName(),
            'database' => DB::connection()->getDatabaseName(),
            'status' => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS),
        ];
    } catch (\Exception $e) {
        return 'DB ERROR: ' . $e->getMessage();
    }
});

Route::get('/debug-users-columns', function () {
    return DB::select('SHOW COLUMNS FROM users');
});

Route::get('/debug-migrations', function () {
    return DB::table('migrations')->pluck('migration');
});

Route::get('/debug-migration-files', function () {
    $files = File::files(database_path('migrations'));
    return collect($files)->map(fn($f) => $f->getFilename())->values();
});

Route::get('/debug-error', function () {
    $log = file(storage_path('logs/laravel.log'));
    return array_slice($log, -20); // 最後の20行だけ返す
});