<x-parent-layout>
    <div class="max-w-3xl mx-auto mt-8">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">ご褒美一覧</h1>

            <a href="{{ route('parent.rewards.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                ＋ 新規登録
            </a>
        </div>

        <div class="grid grid-cols-1 gap-5">
            @foreach ($rewards as $reward)
                <div class="p-5 bg-white rounded-xl shadow hover:shadow-md transition border border-gray-100">
                    <h2 class="text-xl font-semibold text-gray-800">{{ $reward->title }}</h2>

                    @if ($reward->description)
                        <p class="text-gray-600 mt-1">{{ $reward->description }}</p>
                    @endif

                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-blue-600 font-bold text-lg">
                            必要ポイント：{{ $reward->cost }}
                        </span>

                        <span class="text-sm text-gray-400">
                            登録日：{{ $reward->created_at->format('Y/m/d') }}
                        </span>
                    </div>
                    <div class="mt-4 flex gap-3">
                        <a href="{{ route('parent.rewards.edit', $reward) }}"
                        class="text-blue-600 hover:text-blue-800">
                            編集
                        </a>

                        <form action="{{ route('parent.rewards.destroy', $reward) }}" method="POST"
                            onsubmit="return confirm('本当に削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:text-red-800">
                                削除
                            </button>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>
        <a href="{{ route('parent.dashboard') }}"
        class="inline-block mb-4 bg-gray-200 text-gray-700 px-3 py-1 rounded hover:bg-gray-300 transition">
            ← ダッシュボードに戻る
        </a>
    </div>
</x-parent-layout>