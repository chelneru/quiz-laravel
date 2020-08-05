<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{env("ANALYTICS_CODE", "")}}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '{{env("ANALYTICS_CODE", "")}}');
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
    <link href="{{ asset('css/landing-page.css',config('app.secure', null)) }}" rel="stylesheet">

</head>
<body>
<div class="loading-spinner"></div>

<div id="app">
    <nav class="black-text white">
        <div class="nav-wrapper">
            <span class="menu-info">
            <a href="/" class="brand-logo hide-on-small-and-down">{{ config('app.name', 'Laravel') }}</a>
                </span>
            <div class="desktop-menu">
            @include('layouts.visitor-menu')
            </div>
            <div class="mobile-menu">
                <div class="login-item">
                    <a href="{{route('login')}}" class="btn">login</a>
                </div>
                <div class="register-item">
                    <a href="{{route('register')}}" class="btn">register</a>
                </div>
            <ul class="right">

                <a href="#" data-target="mobile-demo" class="sidenav-trigger hide-on-med-and-up"><i
                        class="material-icons black-text">menu</i></a>



            </ul>
            </div>
        </div>
    </nav>
    <ul id="mobile-demo" class="sidenav">

            @include('layouts.visitor-menu-mobile')

    </ul>
    <main class="py-4 overwritten-py-4">
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
