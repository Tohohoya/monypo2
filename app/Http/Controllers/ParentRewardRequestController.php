<?php

namespace App\Http\Controllers;

use App\Models\RewardRequest;
use Illuminate\Support\Facades\Auth;

class ParentRewardRequestController extends Controller
{
    public function index()
    {
        $familyId = Auth::user()->family_id;

        // 家族内の子どもたちの申請だけ取得
        $requests = RewardRequest::with(['reward', 'child'])
            ->whereHas('child', function ($q) use ($familyId) {
                $q->where('family_id', $familyId);
            })
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('parent.reward_requests.index', compact('requests'));
    }
    public function approve($id)
    {
        $request = RewardRequest::findOrFail($id);

        // ステータス更新
        $request->status = 'approved';
        $request->save();

        return back()->with('success', '交換申請を承認しました！');
    }

    public function reject($id)
    {
        $request = RewardRequest::findOrFail($id);

        // 子どものポイントを返す（任意）
        $child = $request->child;
        $child->points += $request->reward->cost;
        $child->save();

        // ステータス更新
        $request->status = 'rejected';
        $request->save();

        return back()->with('error', '交換申請を却下しました');
    }
}