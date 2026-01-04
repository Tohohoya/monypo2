<?php

namespace App\Http\Controllers;

use App\Models\Chore;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChoreController extends Controller
{
    // お手伝い一覧（親用）
    public function index()
    {
        $familyId = Auth::user()->family_id;

        $chores = Chore::where('family_id', $familyId)->get();

        return view('parent.chores.index', compact('chores'));
    }

    // お手伝い作成フォーム
    public function create()
    {
        $familyId = Auth::user()->family_id;

        // 子ども一覧（assigned_to 用）
        $children = User::where('family_id', $familyId)
                        ->where('role', 'child')
                        ->get();

        return view('parent.chores.create', compact('children'));
    }

    // お手伝い登録処理
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points' => 'required|integer|min:0',
            'assigned_to' => 'required|exists:users,id',
        ]);

        Chore::create([
            'family_id' => Auth::user()->family_id,
            'title' => $request->title,
            'description' => $request->description,
            'points' => $request->points,
            'assigned_to' => $request->assigned_to,
            'created_by' => Auth::id(),
            'status' => 'pending',
        ]);

        return redirect()->route('parent.chores.index')
                         ->with('success', 'お手伝いを登録しました');
    }
    public function approve(Chore $chore)
{
    // 親以外は承認できない
    if (Auth::user()->role !== 'parent') {
        abort(403);
    }

    // 同じ家族以外は触れない
    if ($chore->family_id !== Auth::user()->family_id) {
        abort(403);
    }

    // 承認処理
    $chore->update([
        'status' => 'approved',
        'approved_at' => now(),
    ]);

    // 子どもにポイントを付与
    $child = $chore->assignedToUser; // 後でリレーション作る
    $child->points += $chore->points;
    $child->save();

    return redirect()->route('parent.chores.index')
                     ->with('success', 'お手伝いを承認しました！');
    }
}

