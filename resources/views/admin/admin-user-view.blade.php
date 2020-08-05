<style>.help-button {
        display: none !important;
    }</style>
@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href="{{route('admin-users')}}">@lang('admin-users.users-page-title')</a>

    <a class="breadcrumb grey-text text-darken-4"
       href="{{route('admin-user-view',['user_id'=>$user->id])}}">@lang('admin-users.user-view-page-title')</a>
@endsection
@section('tab-title') - ADMIN - User View  @endsection

@section('content')
    <link href="{{ asset('css/admin_user_view.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container admin-user-view-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-body">
                        <div class="row">
                            <div class="panel-row">
                                <div class="panel-label">@lang('admin-users.edit-user-name-label')</div>
                                <div
                                    class="panel-info-field">{{shortenedString($user->first_name,30).' '.shortenedString($user->last_name,30)}}</div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="panel-row">
                                <div class="panel-label">@lang('admin-users.edit-user-email-label')</div>
                                <div class="panel-info-field">{{$user->email}}</div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="panel-row">@lang('admin-users.edit-user-class-info-header')</div>

                        </div>
                        <div class="class-quiz-info">
                        @foreach($user->classes as $class)

                            <div class="row">
                                <div class="panel-row">
                                    <div class="panel-info-field">{{shortenedString($class->name,30)}}</div>
                                    <div
                                        class="panel-info-field count-info">@if($class->participants_count >0 ){{$class->participants_count}}@else{{'0'}}@endif{{' participants'}}</div>
                                    <div
                                        class="panel-info-field count-info">@if($class->quizzes_count >0 ){{$class->quizzes_count}}@else{{'0'}}@endif{{' quizzes'}}</div>
                                </div>
                            </div>
                                <div class="divider"></div>

                            @foreach($class->quizzes as $quiz)
                                <div class="row">
                                    <div class="panel-row">
                                        <div class="panel-label"></div>

                                        <div class="panel-info-field quiz-name-field"><a href="{{route('quiz-info',['id'=>$quiz->id])}}">{{shortenedString($quiz->name,30)}}</a></div>
                                        <div
                                            class="panel-info-field count-info">@if($quiz->questions_count >0 ){{$quiz->questions_count}}@else{{'0'}}@endif{{' questions'}}</div>
                                        <div
                                            class="panel-info-field count-info">@if($quiz->sessions_count >0 ){{$quiz->sessions_count}}@else{{'0'}}@endif{{' sessions'}}</div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach

                        @foreach($user->quizzes as $quiz)
                            @if($quiz->class_id === null)
                                <div class="row">
                                    <div class="panel-row">
                                        <div class="panel-info-field">{{shortenedString($quiz->name,30)}}</div>
                                        <div
                                            class="panel-info-field">@if($quiz->questions_count >0 ){{$quiz->questions_count}}@else{{'0'}}@endif{{' questions'}}</div>
                                        <div
                                            class="panel-info-field">@if($quiz->sessions_count >0 ){{$quiz->sessions_count}}@else{{'0'}}@endif{{' sessions'}}</div>
                                        <div
                                            class="panel-info-field">{{'Belongs to '.shortenedString($quiz->class_name,40).' class'}}</div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <a class="btn cancel-create-btn grey noselect" href="{{route('admin-users')}}">@lang('admin-users.admin-users-back')</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    </div>

    <script src="{{ asset('js/admin_user_view.js',config('app.secure', null)) }}" defer></script>

@endsection
