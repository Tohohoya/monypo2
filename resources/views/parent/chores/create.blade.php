<x-app-layout>
    <div class="max-w-xl mx-auto p-6">

        <h1 class="text-2xl font-bold mb-6 text-blue-600">
            お手伝いをお願いする。
        </h1>

        <form method="POST" action="{{ route('parent.chores.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block font-semibold mb-1">タイトル</label>
                <input type="text" name="title" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">説明</label>
                <textarea name="description" class="w-full border rounded p-2" required></textarea>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">ポイント</label>
                <input type="number" name="points" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">担当する子ども</label>
                <select name="child_id" class="w-full border rounded p-2" required>
                    @foreach ($children as $child)
                        <option value="{{ $child->id }}">{{ $child->name }}</option>
                    @endforeach
                </select>
            </div>

            <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                作成する
            </button>
        </form>

    </div>
</x-app-layout>