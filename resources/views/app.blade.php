<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="overflow-y: auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ config('app.env') !== 'local' ? secure_asset('js/app.js') : asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ config('app.env') !== 'local' ? secure_asset('css/app.css') : asset('css/app.css') }}"
        rel="stylesheet">
</head>

<body>
    <div id="app">
    </div>
</body>

</html>
