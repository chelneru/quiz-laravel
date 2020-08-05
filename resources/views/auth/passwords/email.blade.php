@extends('layouts.app-no-menu')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href="#">Reset Password</a>

@endsection
@section('content')
    <link href="{{ asset('css/profile.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container reset-password-page">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="default-panel z-depth-2">

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="panel-row">
                            <div class="panel-label">@lang('register.email')</div>

                            <div class="panel-input-field">
                                <input id="email" type="email"
                                       class="panel-text-input{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                       name="email"
                                       value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row mb-0 panel-footer">
                            <div class="col-md-6 offset-md-4 footer-row">
                                <a href="{{route('login')}}" class="btn grey">< login</a>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send  Email') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
