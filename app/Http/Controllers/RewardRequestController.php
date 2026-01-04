<?php

namespace App\Http\Controllers;

use App\Models\RewardRequest;
use Illuminate\Support\Facades\Auth;

class RewardRequestController extends Controller
{
    // 交換申請一覧
    public function index()
    {
        $familyId = Auth::user()->family_id;

        // 同じ家族の申請だけ取得
        $requests = RewardRequest::whereHas('user', function ($q) use ($familyId) {
            $q->where('family_id', $familyId);
        })->with('reward', 'user')->get();

        return view('parent.reward_requests.index', compact('requests'));
    }

    // 承認処理
    public function approve(RewardRequest $request)
    {
        // 親以外は承認できない
        if (Auth::user()->role !== 'parent') {
            abort(403);
        }

        $child = $request->user;
        $reward = $request->reward;

        // ポイントが足りない場合（念のため）
        if ($child->points < $reward->cost) {
            return back()->with('error', 'ポイントが足りません');
        }

        // ポイント減算
        $child->points -= $reward->cost;
        $child->save();

        // 申請を承認
        $request->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', '交換申請を承認しました！');
    }
}