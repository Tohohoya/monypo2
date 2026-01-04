<x-app-layout>
    {{ Auth::id() }}
    <div class="max-w-4xl mx-auto p-6">

        <h1 class="text-2xl font-bold mb-6 text-blue-700">
            承認待ちのお手伝い
        </h1>

        @forelse ($chores as $chore)
            <div class="bg-white border border-blue-200 p-4 rounded-lg shadow mb-4">

                <div class="flex justify-between items-center mb-2">
                    <p class="font-semibold text-lg text-blue-800">
                        {{ $chore->title }}
                    </p>

                    <span class="text-sm bg-yellow-100 text-yellow-700 px-2 py-1 rounded">
                        完了報告：{{ $chore->completed_at->format('Y/m/d H:i') }}
                    </span>
                </div>

                <p class="text-gray-600 mb-2">
                    {{ $chore->description }}
                </p>

                <p class="text-gray-700 mb-4">
                    <span class="font-bold">{{ $chore->child->name }}</span> さん  
                    （{{ $chore->points }} pt）
                </p>

                <form action="{{ route('parent.chores.approve', $chore->id) }}" method="GET">
                    <button
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                        承認する
                    </button>
                </form>

            </div>
        @empty
            <p class="text-gray-600">承認待ちのお手伝いはありません。</p>
        @endforelse

    </div>
</x-app-layout>