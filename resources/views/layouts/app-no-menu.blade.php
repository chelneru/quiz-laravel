<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
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
<body>
<div class="loading-spinner"></div>

<div id="app">
    <nav class="black-text white">
        <div class="nav-wrapper">
            <span class="menu-info">
            <a href="/home" class="brand-logo hide-on-small-and-down">{{ config('app.name', 'Laravel') }}</a>
            @yield('title')
                </span>
            <ul class="right">
                @guest
                    <li tabindex="0">
                        <a class="nav-link" href="{{ route('login') }}">@lang('messages.login')</a>
                    </li>
                    <li tabindex="0">
                        @if (Route::has('register'))
                            <a class="nav-link" href="{{ route('register') }}">@lang('messages.register')</a>
                        @endif
                    </li>

                @elseif(Auth::user()!== null)
                    <a href="#" data-target="mobile-demo" class="sidenav-trigger hide-on-med-and-up"><i class="material-icons black-text">menu</i></a>

                    <!-- Dropdown Trigger -->
                    <li class="hide-on-small-and-down">
                        <a class="navbar-dropdown-trigger black-text" href="#!"
                           data-target="dropdown1">{{ shortenedString(Auth::user()->u_first_name,20).' '.shortenedString(Auth::user()->u_last_name,20) }}
                            <i class="material-icons right">arrow_drop_down</i>
                        </a>
                        <ul id="dropdown1" class="dropdown-content" tabindex="0">
                            <li tabindex="0">
                                <a class="grey-text text-darken-4" href="{{ route('profile') }}">
                                    @lang('messages.profile')
                                </a>
                            </li>
                            <li tabindex="0">
                                <a class="grey-text text-darken-4" href="{{ route('edit-profile') }}">
                                    @lang('messages.edit-account')
                                </a>
                            </li>
                            <li tabindex="0">
                                <a class="grey-text text-darken-4" href="{{ route('delete-account-page') }}">
                                    <b>@lang('messages.delete-account')</b>
                                </a>
                            </li>
                            <li tabindex="0">
                                <a class="grey-text text-darken-4" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    @lang('messages.logout')
                                </a>
                            </li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                  style="display: none;">
                                @csrf
                            </form>

                        </ul>
                    </li>
                @endguest

            </ul>
        </div>
    </nav>
    <main class="py-4 overwritten-py-4">
        @if (\Session::has('message'))
            <div class="notif
            @if(\Session::has('status') && \Session::get('status') == 'fail')
            {{'fail'}}@elseif(\Session::has('status') && \Session::get('status') == 'success') {{'success'}}@endif">{!! \Session::get('message') !!}</div>
        @else
        @endif
        <div class="action-buttons-space">
            @yield('action-buttons-space')
        </div>
        @yield('content')
    </main>
</div>

</body>
<script src="/js/libs/toastr.min.js" defer></script>

<script src="{{ asset('js/materialize.min.js',config('app.secure', null)) }}" defer></script>
<script src="{{ asset('js/app.js',config('app.secure', null)) }}" defer></script>

</html>
