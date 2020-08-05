@extends('layouts.app-no-menu')
@section('title')<a class="breadcrumb grey-text text-darken-4" href="{{route('register')}}">&nbsp;&nbsp;Register</a>
    @endsection

@section('content')

    <link href="{{ asset('css/register.css',config('app.secure', null)) }}" rel="stylesheet">
    <script type="text/javascript">
        var onloadCallback = function() {
            grecaptcha.render('g-recaptcha', {
                'sitekey' : '{{config('app.debug') == false?$_ENV['CAPTCHA_SITE_KEY']:$_ENV['CAPTCHA_SITE_KEY_DEBUG']}}'
            });
        };
    </script>

    <div class="container register-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="card-body">
                        <div class="register-type">
                            <div class="register-tab active participant-reg">@lang('register.register-participant')</div>
                            @if(!isset($token) && $quiz_id === null)
                            <div class="register-tab teacher-reg">@lang('register.register-teacher')</div>
                                @endif
                        </div>

                        <form id="register_form" method="POST" action="{{ route('register') }}">
                            @csrf
                            @if(isset($token))
                                <input type="hidden" dusk="class_token" name="class_token" value="{{$token}}">
                                <input type="hidden" name="name" value="username">
                                @endif
                            @if($quiz_id !== null)
                                <input type="hidden" dusk="quiz_id" name="quiz_id" value="{{$quiz_id}}">
                            @endif
                            <input id="user_role" type="hidden" name="user_role" value="1"/>
                                <p class="teacher-validate-text">As an unvalidated teacher, you will be able to create only one class with one quiz. To remove this limit, you will need to have you account validated by sending an email with your information at saga [dot] edu [dot] eng [at] gmail [dot] com.</p>
                            <div class="panel-row">
                                <label for="email"
                                       class="panel-label">@lang('register.email')</label>


                                <div class="panel-input-field input-counter-div">
                                    <input id="email" dusk="email" type="email"
                                           class="panel-text-input{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                           name="email" maxlength="190"
                                           value="{{ old('email') }}" required>
                                    <span class="input-counter">0/190</span>

                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
v
                                    @endif
                                </div>
                            </div>

                            <div class="panel-row">
                                <label for="password"
                                       class="panel-label">@lang('register.password')</label>

                                <div class="panel-input-field input-counter-div">
                                    <input id="password" type="password"
                                           class="panel-text-input{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                           dusk="password" name="password" required  maxlength="50">
                                    <span class="input-counter">0/50</span>

                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="panel-row">
                                <label for="password-confirm"
                                       class="panel-label">@lang('register.password-confirm')</label>

                                <div class="panel-input-field input-counter-div">
                                    <input id="password-confirm" type="password" maxlength="50" class="panel-text-input"
                                           dusk="password_confirmation" name="password_confirmation" required>
                                    <span class="input-counter">0/50</span>

                                </div>
                            </div>

                            <div class="panel-row">
                                <label for="teacher-first-name"
                                       class="panel-label">@lang('register.first-name')</label>

                                <div class="panel-input-field input-counter-div">
                                    <input id="teacher-first-name" required type="text" maxlength="255" class="panel-text-input"
                                           dusk ="first_name" name="first_name" value="{{ old('fist_name') }}">
                                    <span class="input-counter">0/255</span>

                                </div>
                            </div>

                            <div class="panel-row">
                                <label for="teacher-last-name"
                                       class="panel-label">@lang('register.last-name')</label>

                                <div class="panel-input-field input-counter-div">
                                    <input id="teacher-last-name" required type="text" maxlength="255"  class="panel-text-input"
                                           dusk="last_name" name="last_name" value="{{ old('last_name') }}">
                                    <span class="input-counter">0/255</span>

                                </div>
                            </div>

                            <div class="teacher-information">

                                <div class="panel-row">
                                    <label for="teacher-department"
                                           class="panel-label">@lang('register.department')</label>

                                    <div class="panel-input-field input-counter-div">
                                        <input id="teacher-department" type="text"  maxlength="100" class="panel-text-input"
                                               dusk="department" name="department" value="{{ old('department') }}">
                                        <span class="input-counter">0/100</span>

                                    </div>
                                </div>

                                <div class="panel-row">
                                    <label for="teacher-position"
                                           class="panel-label">@lang('register.position')</label>

                                    <div class="panel-input-field input-counter-div">
                                        <input id="teacher-position" type="text" maxlength="100"  class="panel-text-input"
                                               dusk="position" name="position" value="{{ old('position') }}">
                                        <span class="input-counter">0/100</span>

                                    </div>
                                </div>
                            </div>
                            <label class="policy-label">
                                <input type="checkbox" class="filled-in" name="privacy_policy"/>
                                <span>I accept the <a href="/Privacy%20Policy.pdf">terms and conditions</a> for using SAGA.</span>
                            </label>
                            <br>
                            <label class="terms-label">
                                <input type="checkbox" class="filled-in" name="responsibility"/>
                                <span class="responsible-text">I am solely responsible for the text I will submit on SAGA as answers and personal information.</span>
                            </label>
                            <div id="g-recaptcha" style="margin-bottom: 20px;" data-sitekey="{{config('app.debug') == false?$_ENV['CAPTCHA_SITE_KEY']:$_ENV['CAPTCHA_SITE_KEY_DEBUG']}}"></div>

                            <div class="panel-footer">
                                <div class="panel-row">

                                    <a href="{{route('login')}}" class="btn grey">
                                        back to login
                                    </a>
                                    <button dusk="submit-register-btn" type="submit" class="btn create-account-btn">
                                        @lang('register.create-account')
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
            async defer>
    </script>

    <script src="{{ asset('js/register.js',config('app.secure', null)) }}" defer></script>


@endsection
