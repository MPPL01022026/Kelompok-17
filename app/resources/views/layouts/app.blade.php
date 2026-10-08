<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Ayamo | Fried Chicken')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/tailwindcss@1.9.6/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/ayamo.css') }}">
</head>
<body>
    @if (session('status')) <div class="flash">{{ session('status') }}</div> @endif
    @if ($errors->any()) <div class="flash error">{{ $errors->first() }}</div> @endif
    @yield('content')
</body>
</html>
