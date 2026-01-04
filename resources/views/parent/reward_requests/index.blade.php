@extends('layouts.parent')

@section('content')

<div class="mb-4">
    <a href="{{ route('parent.dashboard') }}"
       class="inline-block bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600">
        ダッシュボードに戻る
    </a>
</div>

<h1 class="text-xl font-bold mb-4">交換申請一覧</h1>

@foreach ($requests as $req)
    <div class="border p-4 mb-3 rounded">
        <p>子ども: {{ $req->child->name }}</p>
        <p>ご褒美: {{ $req->reward->title }}</p>
        <p>必要ポイント: {{ $req->reward->cost }}</p>
        <p>ステータス: {{ $req->status }}</p>

        @if ($req->status === 'pending')
            <div class="mt-2 flex gap-2">
                <form method="POST" action="{{ route('parent.reward_requests.approve', $req->id) }}">
                    @csrf
                    <button class="bg-blue-500 text-white px-3 py-1 rounded">承認</button>
                </form>

                <form method="POST" action="{{ route('parent.reward_requests.reject', $req->id) }}">
                    @csrf
                    <button class="bg-red-500 text-white px-3 py-1 rounded">却下</button>
                </form>
            </div>
        @endif
    </div>
@endforeach
@endsection