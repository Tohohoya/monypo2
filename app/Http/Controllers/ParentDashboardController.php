<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Chore;
use App\Models\RewardRequest;
use Illuminate\Support\Facades\Auth;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $parent = Auth::user();

        // 親に紐づく子ども一覧
       $children = User::where('family_id', $parent->family_id)
            ->where('role', 'child')
            ->get();
        // 子どもの合計ポイント
        $totalPoints = $children->sum('points');

        // 未承認のお手伝い数
        $pendingChores = Chore::whereIn('child_id', $children->pluck('id'))
            ->where('status', 'completed')
            ->count();

        // ご褒美交換申請の件数
        $pendingRewards = RewardRequest::whereIn('child_id', $children->pluck('id'))
            ->where('status', 'pending')
            ->count();

        return view('parent.dashboard', compact(
            'children',
            'totalPoints',
            'pendingChores',
            'pendingRewards'
        ));
    }
}