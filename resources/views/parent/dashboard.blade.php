<x-app-layout>
    <div class="parent-theme max-w-3xl mx-auto p-4">

    <div class="max-w-3xl mx-auto p-4">

        <h1 class="text-2xl font-bold mb-6 !text-blue-700">親ダッシュボード</h1>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    <div class="bg-blue-100 p-4 rounded shadow">
        <h2 class="text-lg font-bold">子どもの合計ポイント</h2>
        <p class="text-3xl font-bold text-blue-600">{{ $totalPoints }}</p>
    </div>

    <div class="bg-yellow-100 p-4 rounded shadow">
        <a href="{{ route('parent.chores.pending') }}">
            <div class="bg-yellow-100 p-4 rounded shadow hover:bg-yellow-200 transition">
                <h2 class="text-lg font-bold">承認待ちのお手伝い</h2>
                <p class="text-3xl font-bold text-yellow-600">{{ $pendingChores }}</p>
            </div>
        </a>
    </div>

    <div class="bg-green-100 p-4 rounded shadow">
        <h2 class="text-lg font-bold">ご褒美交換申請</h2>
        <p class="text-3xl font-bold text-green-600">{{ $pendingRewards }}</p>
    </div>

</div>
        {{-- 管理メニュー --}}
        <div class="bg-blue-50 shadow rounded p-4 mb-6">
            <h2 class="text-lg font-bold mb-3 text-blue-700">管理メニュー</h2>

            <ul class="space-y-3">
                <li>
                    <a href="{{ route('parent.chores.index') }}" class="text-blue-600 underline">
                        お手伝い一覧を見る
                    </a>
                </li>
                <li>
                    <a href="{{ route('parent.chores.create') }}"   class="block bg-blue-100 p-4 rounded shadow hover:bg-blue-200">
                        お手伝いを作成する
                    </a>
                </li>
                <li>
                    <a href="{{ route('parent.rewards.index') }}" class="text-blue-600 underline">
                        ご褒美一覧を見る
                    </a>
                </li>
                <li>
                    <a href="{{ route('parent.reward_requests.index') }}" class="text-blue-600 underline">
                        ご褒美交換申請一覧を見る
                    </a>
                </li>
            </ul>
        </div>

        {{-- 子どものポイント --}}
        <div class="bg-blue-50 shadow rounded p-4">
            <h2 class="text-lg font-bold mb-3 text-blue-700">子どものポイント</h2>

            <ul class="space-y-2">
                @foreach ($children as $child)
                    <li class="border-b pb-2">
                        {{ $child->name }}：
                        <span class="font-bold text-blue-600">{{ $child->points }} pt</span>
                    </li>
                @endforeach
            </ul>
        </div>

    </div>
</x-app-layout>