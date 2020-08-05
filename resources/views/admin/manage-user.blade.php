<style>.help-button {
        display: none !important;
    }</style>
@extends('layouts.app-no-menu')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href="{{route('admin-users')}}">@lang('admin-users.users-page-title')</a>

    <a class="breadcrumb grey-text text-darken-4"
                    href="/admin-create-user">&nbsp;@if($user_info->id==null)@lang('admin-users.manage-user-title-create')@else @lang('admin-users.manage-user-title-edit')@endif</a>
@endsection
@section('tab-title') - ADMIN - @if($user_info->id==null)@lang('admin-users.manage-user-title-create')@else @lang('admin-users.manage-user-title-edit')@endif @endsection

@section('content')

    <link href="{{ asset('css/admin_manage_user.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container manage-user-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="card-body">
                        <form id="manage_user_form" method="POST"
                              action="{{route('admin-manage-user-action')}}">
                            @csrf
                            <input type="hidden" name="id" value="{{$user_info->id}}">
                            <div class="panel-row">
                                <div class="panel-input-field input-counter-div">
                                    <input id="email" type="email"
                                           class="panel-text-input{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                           name="email" maxlength="190"
                                           value="{{$user_info->email}}" placeholder="@lang('admin-users.manage-user-email-placeholder')" required>
                                    <span class="input-counter">0/190</span>
                                </div>
                            </div>

                            <div class="panel-row">
                                <div class="panel-input-field input-counter-div">
                                    <input id="teacher-first-name" required type="text" maxlength="255"
                                           class="panel-text-input"
                                           placeholder="@lang('admin-users.manage-user-first-name-placeholder')"
                                           name="first_name" value="{{$user_info->first_name}}">
                                    <span class="input-counter">0/255</span>
                                </div>
                            </div>
                            <div class="panel-row">
                                <div class="panel-input-field input-counter-div">
                                    <input id="teacher-last-name" required type="text" maxlength="255"
                                           class="panel-text-input"
                                           name="last_name" placeholder="@lang('admin-users.manage-user-last-name-placeholder')" value="{{$user_info->last_name}}">
                                    <span class="input-counter">0/255</span>

                                </div>
                            </div>
                            <div class="panel-row">
                                <div class="role-select-div">
                                    <select class="role-select" name="role" required>
                                        <option value="" disabled>@lang('admin-users.manage-user-choose-role-option')</option>
                                        <option class="black-text"
                                                value="1"@if($user_info->role == 1){{'selected'}}@endif>@lang('profile.profile-student')</option>
                                        <option class="black-text"
                                                value="2"@if($user_info->role == 2){{'selected'}}@endif>@lang('profile.profile-teacher')</option>
                                        <option class="black-text"
                                                value="3"@if($user_info->role == 3){{'selected'}}@endif>@lang('profile.profile-admin')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="teacher-information" style="@if($user_info->role == 2){{'display:block'}}@endif">
                                <div class="panel-row">
                                    <div class="panel-input-field input-counter-div">
                                        <input id="teacher-department" type="text" maxlength="100"
                                               class="panel-text-input"
                                               name="department" value="{{$user_info->department}}"
                                               placeholder="@lang('admin-users.manage-user-department-placeholder')">
                                        <span class="input-counter">0/100</span>

                                    </div>
                                </div>

                                <div class="panel-row">
                                    <div class="panel-input-field input-counter-div">
                                        <input id="teacher-position" type="text" maxlength="100"
                                               class="panel-text-input"
                                               name="position" value="{{$user_info->position}}" placeholder="@lang('admin-users.manage-user-position-placeholder')">
                                        <span class="input-counter">0/100</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div class="panel-row">
                                    <a href="{{route('admin-users')}}" class="btn grey">
                                        @lang('admin-users.go-back')
                                    </a>
                                    <button type="submit" class="btn create-account-btn">
                                        @if($user_info->id == null)

                                            @lang('admin-users.create-user')
                                        @else
                                            @lang('admin-users.edit-user')
                                        @endif

                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/admin_manage_user.js',config('app.secure', null)) }}" defer></script>

@endsection
