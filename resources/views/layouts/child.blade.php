<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>子どもダッシュボード</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-orange-50">

    <div class="min-h-screen">
        @if (session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded shadow">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded shadow">
        🎉{{ session('error') }}
    </div>
@endif
<div class="mb-4">
    <a href="{{ route('child.dashboard') }}" 
       class="inline-block bg-orange-500 text-white px-4 py-2 rounded shadow hover:bg-orange-600">
        ダッシュボードに戻る
    </a>
</div>
        @yield('content')
    </div>

</body>
</html>