<x-app-layout>
    <h1 class="text-xl font-bold mb-4">お手伝い一覧</h1>

    <a href="{{ route('parent.chores.create') }}" class="text-blue-500 underline">
        新しいお手伝いを作成
    </a>

    <ul class="mt-4">
        @foreach ($chores as $chore)
            <li class="border p-2 mb-2">
                <strong>{{ $chore->title }}</strong>
                （{{ $chore->points }} pt）
                - 状態: {{ $chore->status }}
                            @if ($chore->status === 'completed')
                <form method="POST" action="{{ route('parent.chores.approve', $chore->id) }}">
                    @csrf
                    <button class="bg-blue-500 text-white px-2 py-1 mt-2 rounded">
                        承認する
                    </button>
                </form>
            @endif
            </li>
        @endforeach
    </ul>
    @extends('layouts.parent')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">お手伝い一覧</h1>

    <table class="w-full bg-white shadow rounded">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="p-3">子ども</th>
                <th class="p-3">タイトル</th>
                <th class="p-3">ポイント</th>
                <th class="p-3">ステータス</th>
                <th class="p-3">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($chores as $chore)
                <tr class="border-b">
                    <td class="p-3">{{ $chore->child->name ?? '不明' }}</td>
                    <td class="p-3">{{ $chore->title }}</td>
                    <td class="p-3">{{ $chore->points }}</td>
                    <td class="p-3">
                        @if ($chore->status === 'pending')
                            <span class="text-yellow-600">未完了</span>
                        @elseif ($chore->status === 'completed')
                            <span class="text-blue-600">完了報告</span>
                        @elseif ($chore->status === 'approved')
                            <span class="text-green-600">承認済み</span>
                        @endif
                    </td>
                    <td class="p-3">
                        @if ($chore->status === 'completed')
                            <a href="{{ route('parent.chores.approve', $chore->id) }}"
                               class="text-blue-600 underline">
                                承認する
                            </a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
</x-app-layout>
