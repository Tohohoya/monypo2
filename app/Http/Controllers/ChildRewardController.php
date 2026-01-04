<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use App\Models\RewardRequest;
use Illuminate\Support\Facades\Auth;

class ChildRewardController extends Controller
{
    // ご褒美一覧
    public function index()
    {
        $familyId = Auth::user()->family_id;

        $rewards = Reward::where('family_id', $familyId)->get();

        return view('child.rewards.index', compact('rewards'));
    }

    // 交換申請
    public function request(Reward $reward)
    {
        $user = Auth::user();

        // ポイント不足チェック
        if ($user->points < $reward->cost) {
            return back()->with('error', 'ポイントが足りません');
        }

        // 交換申請を作成
        RewardRequest::create([
            'reward_id' => $reward->id,
            'child_id' => $user->id,
            'status' => 'pending',
        ]);

        // 🔥 ポイント減算
        $user->points -= $reward->cost;
        $user->save();

        return back()->with('success', '交換申請を送りました！');
    }
    public function store(Request $request)
    {
        RewardRequest::create([
            'reward_id' => $request->reward_id,
            'child_id' => auth()->id(),
            'status' => 'pending',
        ]);

        return redirect()
            ->back()
            ->with('success', '交換依頼を送信しました！');
    }
}