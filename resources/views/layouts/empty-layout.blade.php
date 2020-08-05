<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="height: 98%">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-165412255-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-165412255-1');
    </script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/libs/jquery-3.3.1.min.js',config('app.secure', null)) }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Styles -->
    <link href="/css/toastr.min.css" rel="stylesheet"/>

    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/app-global.css',config('app.secure', null)) }}" rel="stylesheet">

</head>
<body style="height: 100%">
<div class="loading-spinner"></div>

<div id="app" style="height: 100%">
    <main class="py-4 overwritten-py-4" style="height: 100%">
        @if (\Session::has('message'))
            <div class="notif
            @if(\Session::has('status') && \Session::get('status') == 'fail')
            {{'fail'}}@elseif(\Session::has('status') && \Session::get('status') == 'success') {{'success'}}@endif">{!! \Session::get('message') !!}</div>
        @else
        @endif
        @yield('content')
    </main>
</div>

</body>
<script src="/js/libs/toastr.min.js" defer></script>

<script src="{{ asset('js/materialize.min.js',config('app.secure', null)) }}" defer></script>
<script src="{{ asset('js/app.js',config('app.secure', null)) }}" defer></script>

</html>
