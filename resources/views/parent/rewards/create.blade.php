<x-app-layout>
    <h1 class="text-xl font-bold mb-4">ご褒美を作成</h1>

    <form method="POST" action="{{ route('parent.rewards.store') }}">
        @csrf

        <div class="mb-4">
            <label>タイトル</label>
            <input type="text" name="title" class="border w-full p-2" required>
        </div>

        <div class="mb-4">
            <label>説明（任意）</label>
            <textarea name="description" class="border w-full p-2"></textarea>
        </div>

        <div class="mb-4">
            <label>必要ポイント</label>
            <input type="number" name="cost" class="border w-full p-2" required>
        </div>

        <button class="bg-blue-500 text-white px-4 py-2 rounded">
            登録する
        </button>
    </form>
</x-app-layout>
