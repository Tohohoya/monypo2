<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>monypo</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-orange-50 flex items-center justify-center min-h-screen">

    <div class="text-center">
        <h1 class="text-4xl font-bold text-orange-600 mb-6">monypo</h1>
        <p class="text-gray-700 mb-8">家族で使えるポイント管理アプリ</p>

        <a href="/login" class="bg-orange-500 text-white px-6 py-3 rounded shadow hover:bg-orange-600">
            ログイン
        </a>
        <a href="/register" class="ml-4 text-orange-600 underline">
            新規登録
        </a>
    </div>

</body>
</html>