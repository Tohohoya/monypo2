<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentRewardController extends Controller
{
public function index()
{
    $rewards = Reward::where('family_id', Auth::user()->family_id)->get();
    return view('parent.rewards.index', compact('rewards'));
}

public function create()
{
    return view('parent.rewards.create');
}

public function store(Request $request)
{
    $request->validate([
        'title' => 'required',
        'description' => 'nullable|string',
        'cost' => 'required|integer|min:1',
    ]);

    Reward::create([
        'family_id' => Auth::user()->family_id,
        'title' => $request->title,
        'description' => $request->description,
        'cost' => $request->cost,
    ]);

    return redirect()->route('parent.rewards.index')
        ->with('success', 'ご褒美を追加しました！');
}    //
public function edit(Reward $reward)
{
    // 家族が違う報酬を編集できないように保護
    if ($reward->family_id !== Auth::user()->family_id) {
        abort(403);
    }

    return view('parent.rewards.edit', compact('reward'));
}

public function update(Request $request, Reward $reward)
{
    if ($reward->family_id !== Auth::user()->family_id) {
        abort(403);
    }

    $request->validate([
        'title' => 'required',
        'description' => 'nullable|string',
        'cost' => 'required|integer|min:1',
    ]);

    $reward->update([
        'title' => $request->title,
        'description' => $request->description,
        'cost' => $request->cost,
    ]);

    return redirect()->route('parent.rewards.index')
        ->with('success', 'ご褒美を更新しました！');
}

public function destroy(Reward $reward)
{
    if ($reward->family_id !== Auth::user()->family_id) {
        abort(403);
    }

    $reward->delete();

    return redirect()->route('parent.rewards.index')
        ->with('success', 'ご褒美を削除しました！');
}
}
