@extends('layouts.app')
@section('title')

    <a class="breadcrumb grey-text text-darken-4" href="{{route('profile')}}">Profile</a>
@endsection;

@section('content')
    <link href="{{ asset('css/profile.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container profile-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-header">{{$user->u_first_name.' '.$user->u_last_name}}</div>

                    <div class="panel-body">

                        <div class="panel-row">
                            <div class="panel-label">@lang('profile.profile-name'):</div>
                            <div class="panel-info-field">{{$user->u_first_name.' '.$user->u_last_name}}</div>

                        </div>
                        <div class="panel-row">
                            <div class="panel-label">@lang('profile.profile-role'):</div>
                            <div class="panel-info-field">@lang('profile.profile-'.$role)</div>
                        </div>
                        @if($user->u_role ==  1)
                        @elseif($user->u_role == 2)

                            <div class="panel-row">
                                <div class="panel-label">@lang('profile.profile-department'):</div>
                                <div class="panel-info-field">{{$user->u_department}}</div>
                            </div>

                            <div class="panel-row">
                                <div class="panel-label">@lang('profile.profile-position'):</div>
                                <div class="panel-info-field">{{$user->u_position}}</div>
                            </div>

                        @endif
                    </div>
                    <div class="panel-footer">

                        <div class="panel-row">

                            <a href="/home" class="btn grey">
                                @lang('profile.back-button')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@include('user.confirm-delete-account')
    <script src="{{ asset('js/profile.js',config('app.secure', null)) }}" defer></script>

@endsection
