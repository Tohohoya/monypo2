<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RewardController extends Controller
{
    // ご褒美一覧
    public function index()
    {
        $familyId = Auth::user()->family_id;

        $rewards = Reward::where('family_id', $familyId)->get();

        return view('parent.rewards.index', compact('rewards'));
    }

    // ご褒美作成フォーム
    public function create()
    {
        return view('parent.rewards.create');
    }

    // ご褒美登録処理
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
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
                         ->with('success', 'ご褒美を登録しました');
    }
}