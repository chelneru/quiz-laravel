@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4"
       href="#">{{shortenedString($user->u_first_name,20).' '.shortenedString($user->u_last_name,20)}}</a>
    <a class="breadcrumb grey-text text-darken-4"
       href="#">Edit profile</a>
@endsection
@section('tab-title') - Edit Profile @endsection
@section('help-page')
    <div class="help-modal-header">Edit profile</div>
    <p>Here, you can edit your personal information. Your role cannot be changed – you will need to create
    two separate accounts if you want to have the roles of both the teacher (quiz-creator) and the
    student. If you are a teacher, you can participate in quizzes and you can create your own quizzes. As
    a student, you can only participate in quizzes that others have created.</p>
    <p>The User ID is a unique number that is given to an account by the system. Only you (and the SAGA
    admins) know this number. In quizzes that are created for self-assessment your performance is
    anonymous. The quiz-creator will see this number instead of your personal information.</p>
@endsection
@section('content')
    <link href="{{ asset('css/profile.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container edit-profile-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-body">
                        <form id="edit-profile" method="post" action="/update-profile">
                            @csrf
                            <div class="panel-row">
                                <div class="panel-label">@lang('profile.profile-role')</div>
                                <div class="panel-info-field">@lang('profile.profile-'.$role)</div>
                            </div>

                            <div class="panel-row">
                                <div class="panel-label">@lang('profile.profile-email')</div>
                                <div class="panel-input-field input-counter-div">
                                    <input id="email" type="email" class="panel-text-input" name="email"
                                           value="{{$user->email}}" required  maxlength="190">
                                    <span class="input-counter">0/190</span>

                                </div>
                            </div>

                            <div class="panel-row">
                                <div class="panel-label">@lang('profile.profile-first-name')</div>
                                <div class="panel-input-field input-counter-div">
                                    <input id="first_name" type="text" class="panel-text-input" name="first_name"
                                           value="{{$user->u_first_name}}" required  maxlength="255">
                                    <span class="input-counter">0/255</span>

                                </div>
                            </div>

                            <div class="panel-row">
                                <div class="panel-label">@lang('profile.profile-last-name')</div>
                                <div class="panel-input-field input-counter-div">
                                    <input id="last_name" type="text" class="panel-text-input" name="last_name"
                                           value="{{$user->u_last_name}}" required  maxlength="255">
                                    <span class="input-counter">0/255</span>

                                </div>
                            </div>
                            @if($user->u_role ==  1)

                            @elseif($user->u_role == 2)

                                <div class="panel-row">
                                    <div class="panel-label">@lang('profile.profile-department')</div>
                                    <div class="panel-input-field input-counter-div">
                                        <input id="department" type="text" class="panel-text-input" name="department"
                                               value="{{$user->u_department}}" required maxlength="100">
                                        <span class="input-counter">0/100</span>

                                    </div>
                                </div>

                                <div class="panel-row">
                                    <div class="panel-label">@lang('profile.profile-position')</div>
                                    <div class="panel-input-field input-counter-div">
                                        <input id="position" type="text" class="panel-text-input" name="position"
                                               value="{{$user->u_position}}" required maxlength="100">
                                        <span class="input-counter">0/100</span>

                                    </div>
                                </div>

                            @endif
                            <div class="panel-row">
                                <div class="panel-label">Existing password</div>
                                <div class="panel-input-field input-counter-div">
                                    <input type="password" class="panel-text-input existing-password-field" name="existing_password"
                                           value="" maxlength="50">
                                    <span class="input-counter">0/50</span>

                                </div>
                            </div>
                            <div class="panel-row">
                                <div class="panel-label">New Password</div>
                                <div class="panel-input-field input-counter-div">
                                    <input type="password" class="panel-text-input new-password-field" name="new_password"
                                           value="" maxlength="50">
                                    <span class="input-counter">0/50</span>

                                </div>
                            </div>
                            <div class="panel-row">
                                <div class="panel-label">Confirm new Password</div>
                                <div class="panel-input-field input-counter-div">
                                    <input type="password" class="panel-text-input confirm-password-field" name="confirm_new_password"
                                           value="" maxlength="50">
                                    <span class="input-counter">0/50</span>

                                </div>
                            </div>
                        </form>
                        <div class="panel-row">
                            <div class="panel-label">User ID</div>
                            <div class="panel-input-field">
                                @if(Auth::user()){{Auth::user()->u_id.' (Automatically assigned by the system)'}}@endif
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">

                        <div class="panel-row">

                            <a href="/home" class="btn grey">
                                @lang('profile.back-button')
                            </a>
                            <button type="submit" form="edit-profile" class="btn save-changes">
                                @lang('profile.save-button')
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
