<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>1001Guide Admin</title>

    <!-- Scripts -->
    <script src="{{ asset('auth/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('auth/app.css') }}" rel="stylesheet">
</head>
<body style="background-color: #4355fa; color: #FFF;">
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="/img/logo.svg" alt="1001 Guide" style="max-width: 200px;">
            </a>
        </div>
    </nav>

    <main class="py-4" style="padding-top: 30px;">
        @yield('content')
    </main>
</div>
</body>
</html>
