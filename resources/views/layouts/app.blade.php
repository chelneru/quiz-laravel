<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{config('app.name', 'Laravel')}}@yield('tab-title')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/libs/jquery-3.3.1.min.js',config('app.secure', null)) }}"></script>
    <script src="/js/libs/toastr.min.js" defer></script>

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
            <a href="{{route('home')}}" class="brand-logo hide-on-small-and-down">{{ config('app.name', 'Laravel') }}</a>
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
                    <a href="#" data-target="mobile-demo" class="sidenav-trigger hide-on-med-and-up"><i
                                class="material-icons black-text">menu</i></a>

                    <!-- Dropdown Trigger -->
                    <li class="hide-on-small-and-down">
                        <a class="navbar-dropdown-trigger black-text" href="#!"
                           data-target="dropdown1">{{ shortenedString(Auth::user()->u_first_name,20).' '.shortenedString(Auth::user()->u_last_name,20) }}
                            <i class="material-icons right">arrow_drop_down</i>
                        </a>
                        <ul id="dropdown1" class="dropdown-content" tabindex="0">
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

    <ul id="mobile-demo" class="sidenav">
        <li>
            <ul class="collapsible">
                <li>

                    <div class="collapsible-header user-name">{{ shortenedString(Auth::user()->u_first_name,20).' '.shortenedString(Auth::user()->u_last_name,20) }}
                        <i class="small material-icons">arrow_drop_down</i></div>
                    <div class="collapsible-body">
                        <ul>
                            <li><a href="{{ route('edit-profile') }}">Profile</a></li>
                            <li><a href="{{ route('delete-account-page') }}">Delete Account</a></li>
                            <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Logout</a></li>
                        </ul>
                    </div>
                </li>

            </ul>
        </li>
        <li>
            <div class="divider"></div>
        </li>
       @if(Auth::user()->u_role == 1)
            @include('layouts.participant-mobile-menu')
        @elseif(Auth::user()->u_role == 2)
            @include('layouts.teacher-menu')
        @endif
    </ul>

    <main class="py-4 overwritten-py-4">
        <div class="menu-nav hide-on-small-and-down desktop-menu">
            @if(\App\Services\UserService::IsAdmin(Auth::user()->u_id)=== true)
                @include('layouts.admin-menu')
            @elseif(Auth::user()->u_role == 1)
                @include('layouts.participant-menu')
            @elseif(Auth::user()->u_role == 2)
                @include('layouts.teacher-menu')
            @endif
        </div>

        @if (\Session::has('message'))
            <div class="notif
            @if(\Session::has('status') && \Session::get('status') == 'fail')
            {{'fail'}}@elseif(\Session::has('status') && \Session::get('status') == 'success') {{'success'}}@endif">{!! \Session::get('message') !!}</div>
        @else
        @endif
        <div class="action-buttons-space">
            @yield('action-buttons-space')
        </div>

        <div id="help_modal"  class="modal">
            <div class="modal-content">
            @yield('help-page')
            </div>
        </div>
        <a class="modal-trigger" href="#help_modal"><i class="material-icons teal-text text-lighten-1 small help-button">help_outline</i></a>
        @yield('content')
    </main>
</div>

</body>

<script src="{{ asset('js/materialize.min.js',config('app.secure', null)) }}" defer></script>

<script src="{{ asset('js/app.js',config('app.secure', null)) }}" defer></script>
</html>
