<x-parent-layout>
    <div class="max-w-3xl mx-auto mt-8">

        <a href="{{ route('parent.rewards.index') }}"
           class="inline-block mb-4 text-blue-600 hover:text-blue-800">
            ← ご褒美一覧に戻る
        </a>

        <h1 class="text-2xl font-bold mb-6">ご褒美を編集</h1>

        <form action="{{ route('parent.rewards.update', $reward) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-semibold mb-1">タイトル</label>
                <input type="text" name="title" class="w-full border rounded p-2"
                       value="{{ old('title', $reward->title) }}" required>
            </div>

            <div>
                <label class="block font-semibold mb-1">説明（任意）</label>
                <textarea name="description" class="w-full border rounded p-2" rows="3">{{ old('description', $reward->description) }}</textarea>
            </div>

            <div>
                <label class="block font-semibold mb-1">必要ポイント</label>
                <input type="number" name="cost" class="w-full border rounded p-2"
                       value="{{ old('cost', $reward->cost) }}" required min="1">
            </div>

            <div class="flex justify-end">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    更新する
                </button>
            </div>
        </form>
    </div>
</x-parent-layout>