@extends('layouts.child')

@section('content')
    <h1 class="text-xl font-bold mb-4">ご褒美一覧</h1>

    <p>現在のポイント: {{ Auth::user()->points }} pt</p>

    <ul class="mt-4">
        @foreach ($rewards as $reward)
            <li class="border p-2 mb-2">
                <strong>{{ $reward->title }}</strong>
                （必要ポイント: {{ $reward->cost }} pt）

                <form method="POST" action="{{ route('child.rewards.request', $reward->id) }}">
                    @csrf
                    <button class="bg-green-500 text-white px-2 py-1 mt-2 rounded">
                        交換申請する
                    </button>
                </form>
            </li>
        @endforeach
    </ul>
@endsection