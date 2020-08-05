<style>.help-button {
        display: none !important;
    }</style>
@extends('layouts.app-no-menu')

@section('title')
    <a class="breadcrumb grey-text text-darken-4" href="{{route('admin-quizzes')}}">@lang('admin-quizzes.admin-quizzes-page-title')</a>
    <a class="breadcrumb grey-text text-darken-4"
                    href="{{route('admin-edit-quiz',['quiz_id'=>$quiz_info->id])}}">@lang('admin-quizzes.edit-quiz-page-title')</a>
@endsection
@section('tab-title') - ADMIN - @lang('admin-quizzes.edit-quiz-page-title') @endsection

@section('content')

    <link href="{{ asset('css/admin_edit_quiz.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container admin-edit-quiz-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="card-body">
                        <form id="edit_class_form" method="POST"
                              action="{{route('admin-edit-quiz-action')}}">
                            @csrf
                            <input type="hidden" name="id" value="{{$quiz_info->id}}">
                            <div class="panel-row">
                                <div class="panel-input-field input-counter-div">
                                    <input id="name" type="text"
                                           class="panel-text-input"
                                           name="name" maxlength="255"
                                           value="{{$quiz_info->title}}" placeholder="@lang('admin-quizzes.quiz-name-field-placeholder')" required>
                                    <span class="input-counter">0/255</span>
                                </div>
                            </div>

                            <div class="panel-footer">
                                <div class="panel-row">
                                    <a href="{{route('admin-quizzes')}}" class="btn grey">
                                        @lang('admin-quizzes.go-back')
                                    </a>
                                    <button type="submit" class="btn edit-class-btn">
                                        @lang('admin-quizzes.edit-quiz')

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
