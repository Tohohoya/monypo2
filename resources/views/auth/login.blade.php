<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
<div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-b from-orange-100 to-blue-100">

    <!-- Logo -->
    <div class="mb-8 text-center">
        <h1 class="text-4xl font-bold text-gray-800">monypo</h1>
        <p class="text-gray-600 mt-2">家族でつくる、やさしいおこづかい管理</p>
    </div>

    <!-- Login Card -->
    <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md">
        <form method="POST" action="/login">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 mb-1">メールアドレス</label>
                <input type="email" name="email" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-orange-300" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 mb-1">パスワード</label>
                <input type="password" name="password" class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-orange-300" required>
            </div>

            <button class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg font-semibold transition">
                ログイン
            </button>

            <div class="text-center mt-4">
                <a href="/register" class="text-blue-600 hover:underline">アカウントを作成する</a>
            </div>
        </form>
    </div>

    <footer class="mt-8 text-gray-500 text-sm">
        © monypo
    </footer>
</div>

</x-guest-layout>
