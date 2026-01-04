<?php

namespace App\Http\Controllers;

use App\Models\Chore;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // ★ 必須


class ParentChoreController extends Controller
{
    public function create()
    {
        // 親に紐づく子ども一覧を取得
        $children = User::where('parent_id', Auth::id())->get();

        return view('parent.chores.create', compact('children'));
    }
    public function index()
    {
        $familyId = Auth::user()->family_id;

        $chores = Chore::where('family_id', $familyId)
            ->with('child') // 子どもの名前を表示するため
            ->orderBy('status')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('parent.chores.index', compact('chores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'points' => 'required|integer|min:1',
            'child_id' => 'required|exists:users,id',
        ]);

        Chore::create([
            'title' => $request->title,
            'description' => $request->description,
            'points' => $request->points,
            'child_id' => $request->child_id,
            'family_id' => Auth::user()->family_id, // ★ これが重要
            'created_by' => Auth::id(), // ★ これが必須（今回のエラー原因）
            'status' => 'pending',
        ]);

        return redirect()->route('parent.dashboard')
            ->with('success', 'お手伝いを作成しました！');
    }

    public function pending()
    {
        $familyId = Auth::user()->family_id;

        $chores = Chore::where('family_id', $familyId)
            ->where('status', 'completed') // 承認待ちだけ
            ->with('child')
            ->orderBy('completed_at', 'desc')
            ->get();

        return view('parent.chores.pending', compact('chores'));
    }


    public function approve(Chore $chore)
    {
        // 親が他の家族の chore を承認できないようにする
        if ($chore->family_id !== Auth::user()->family_id) {
            abort(403, 'このお手伝いは承認できません。');
        }
            
        // 子どもを取得
        $child = $chore->child;

        // ポイント加算
        $child->points += $chore->points;
        $child->save();

        // お手伝いのステータスを更新
        $chore->status = 'approved';
        $chore->approved_at = now();
        $chore->save();

        return redirect()->back()->with('success', 'お手伝いを承認しました！');
    }
}