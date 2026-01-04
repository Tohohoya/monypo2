<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100">

    <header class="bg-blue-600 text-white p-4">
        <h1 class="text-xl font-bold">親ダッシュボード</h1>
    </header>

    <main class="p-6">
        {{ $slot }}
    </main>

</body>
</html>