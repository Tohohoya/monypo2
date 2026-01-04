<?php

namespace App\Http\Controllers;

use App\Models\Chore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChildChoreController extends Controller
{
    public function index()
{
    $childId = Auth::id();

    $chores = Chore::where('child_id', $childId)
        ->orderByRaw("FIELD(status, '未完了')")
        ->orderBy('created_at', 'desc')

        ->get();

    return view('child.chores.index', compact('chores'));
}


    public function complete($id)
{
    // 自分のお手伝い以外は触れないようにする
    $chore = Chore::where('id', $id)
        ->where('child_id', Auth::id())
        ->firstOrFail();

    // すでに完了済み or 承認済みなら何もしない
    if ($chore->status !== 'pending') {
        return redirect()->back()->with('error', 'このお手伝いはすでに完了報告済みです。');
    }

    // ステータス更新
    $chore->status = 'completed';
    $chore->completed_at = now();
    $chore->save();

    return redirect()->back()->with('success', '完了報告を送りました！');
}

}