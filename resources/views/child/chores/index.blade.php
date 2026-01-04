<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">

        <h1 class="text-2xl font-bold mb-4">今日のお手伝い</h1>

        <table class="w-full bg-white shadow rounded">
            <thead>
                <tr class="bg-orange-100 text-left">
                    <th class="p-3">タイトル</th>
                    <th class="p-3">ポイント</th>
                    <th class="p-3">ステータス</th>
                    <th class="p-3">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chores as $chore)
                    <tr class="border-b">
                        <td class="p-3">{{ $chore->title }}</td>
                        <td class="p-3">{{ $chore->points }}</td>
                        <td class="p-3">
                            @if ($chore->status === 'pending')
                                <span class="text-yellow-600">未完了</span>
                            @elseif ($chore->status === 'completed')
                                <span class="text-blue-600">完了報告済み</span>
                            @elseif ($chore->status === 'approved')
                                <span class="text-green-600">承認済み</span>
                            @endif
                        </td>
                        <td class="p-3">
                            @if ($chore->status === 'pending')
                                <form action="{{ route('child.chores.complete', $chore->id) }}" method="POST">
                                    @csrf
                                    <button class="text-blue-600 underline">
                                        完了報告
                                    </button>
                                </form>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h1 class="text-2xl font-bold mb-6 text-orange-600 mt-10">
            お手伝い一覧
        </h1>

        @foreach ($chores as $chore)
            <div class="bg-white border border-orange-200 p-4 rounded-lg shadow mb-4">

                <div class="flex justify-between items-center mb-2">
                    <p class="font-semibold text-lg text-orange-700">
                        {{ $chore->title }}
                    </p>

                    @if ($chore->status === 'pending')
                        <span class="text-sm bg-blue-100 text-blue-700 px-2 py-1 rounded">
                            未完了
                        </span>
                    @elseif ($chore->status === 'completed')
                        <span class="text-sm bg-yellow-100 text-yellow-700 px-2 py-1 rounded">
                            承認待ち
                        </span>
                    @elseif ($chore->status === 'approved')
                        <span class="text-sm bg-green-100 text-green-700 px-2 py-1 rounded">
                            承認済み！
                        </span>
                    @endif
                </div>

                <p class="text-gray-600 mb-3">{{ $chore->description }}</p>

                @if ($chore->status === 'pending')
                    <form method="POST" action="{{ route('child.chores.complete', $chore) }}">
                        @csrf
                        <button
                            class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 w-full">
                            完了報告する
                        </button>
                    </form>
                @endif

            </div>
        @endforeach

        @if ($chores->isEmpty())
            <p class="text-gray-600">まだお手伝いがありません。</p>
        @endif

    </div>
</x-app-layout>