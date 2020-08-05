@extends('layouts.app-no-menu')
@section('title')<a class="breadcrumb grey-text text-darken-4" href="{{route('login')}}">&nbsp;&nbsp;Login</a>
@endsection
@section('content')
    <link href="{{ asset('css/login.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container login-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="card-body">
                        <form method="POST" action="{{route('login')}}">

                            @csrf
                            @if(isset($quiz_id) && $quiz_id !== null)
                                <input type="hidden" name="quiz_id" value="{{$quiz_id}}">
                            @endif
                            <div class="panel-row">
                                <div class="panel-label">{{ 'Email Address'}}</div>

                                <div class="panel-input-field">
                                    <input id="email" type="email"
                                           class="panel-text-input{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                           name="email" value="{{ old('email') }}" required autofocus>

                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="panel-row">
                                <div class="panel-label">{{ 'Password' }}</div>

                                <div class="panel-input-field">
                                    <input id="password" type="password"
                                           class="panel-text-input{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                           name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="panel-row forgot-pass-row">
                                <div class="btn-flat">
                                    <a href="{{ route('password.request') }}">
                                        {{ 'Forgot Your Password?' }}
                                    </a>
                                </div>
                                <div class="form-check">

                                    <label for="remember">
                                        <input type="checkbox" class="filled-in"
                                               {{ old('remember') ? 'checked="checked"' : '' }} name="remember"
                                               id="remember"/>
                                        <span> {{ 'Remember Me' }}</span>
                                    </label>

                                </div>
                            </div>

                            <div class="panel-footer">
                                <div class="panel-row">


                                    @if($allow_anonymous === true)
                                        <button type="submit" form="anon_quiz_participation"
                                                class="btn-flat teal-text lighten-2 connect-anon"
                                                href="{{$direct_link }}">
                                            {{ 'connect anonymously' }}
                                        </button>

                                    @endif
                                        @if(isset($quiz_id) && $quiz_id !== null)
                                    <a class="btn register-btn btn-link" href="{{ route('register').'?quiz_id='.$quiz_id }}">
                                        {{ 'Register' }}
                                    </a>
                                        @else
                                            <a class="btn register-btn btn-link" href="{{ route('register') }}">
                                                {{ 'Register' }}
                                            </a>
                                            @endif
                                    <button type="submit" class="btn">
                                        {{ 'Login' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                        <form id="anon_quiz_participation" method="POST" action="{{ route('set-anon-credentials') }}">
                            @csrf
                            @if($direct_link !== null)
                                <input type="hidden" name="direct_link" value="{{$direct_link}}">

                            @endif
                            @if($quiz_id !== null)
                                <input type="hidden" name="quiz_id" value="{{$quiz_id}}">

                            @endif
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
