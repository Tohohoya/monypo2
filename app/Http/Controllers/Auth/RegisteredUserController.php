<?php

namespace App\Http\Controllers\Auth;

use App\Models\Family;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Providers\RouteServiceProvider;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'invite_code' => ['nullable', 'string'],
        'is_child' => ['nullable'],
    ]);

    // 子として参加するか？
    $isChild = $request->has('is_child');

    // ユーザー作成（role は後で上書きする）
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $isChild ? 'child' : 'parent',
    ]);

    // 招待コードが入力されている場合 → 既存 family に参加
    if (!empty($request->invite_code)) {
        $family = Family::where('invite_code', $request->invite_code)->first();

        if (!$family) {
            return back()->withErrors(['invite_code' => '招待コードが正しくありません']);
        }

        // 子なら child、親なら parent のまま
        $user->family_id = $family->id;
        $user->save();

    } else {
        // 招待コードなし → 新しい family を作る（親のみ）
        if ($isChild) {
            return back()->withErrors(['invite_code' => '子として参加する場合は招待コードが必要です']);
        }

        $family = Family::create([
            'name' => $user->name . ' Family',
            'invite_code' => Str::random(8),
        ]);

        $user->family_id = $family->id;
        $user->save();
    }

    Auth::login($user);

    return redirect(RouteServiceProvider::HOME);
}
}