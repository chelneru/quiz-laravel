<div class="menu-nav-item @if(Route::currentRouteName() == 'intro'){{'selected'}}@endif">
    <a href="{{route('intro')}}">
        <div class="nav-item-content">home</div>
    </a>
</div>
{{--<div class="menu-nav-item @if(Route::currentRouteName() == 'how-to'){{'selected'}}@endif">--}}
{{--    <a href="{{route('how-to')}}">--}}
{{--        <div class="nav-item-content">How to use SAGA</div>--}}
{{--    </a>--}}
{{--</div>--}}

<div class="menu-nav-item @if(Route::currentRouteName() == 'examples'){{'selected'}}@endif">
    <a href="{{route('examples')}}">
        <div class="nav-item-content">Examples and good practices</div>
    </a>
</div>

<div class="menu-nav-item @if(Route::currentRouteName() == 'publications'){{'selected'}}@endif">
    <a href="{{route('publications')}}">
        <div class="nav-item-content">Publications</div>
    </a>
</div>

<div class="login-item">
    <a href="{{route('login')}}" class="btn">login</a>
</div>
<div class="register-item">
    <a href="{{route('register')}}" class="btn">register</a>
</div>
