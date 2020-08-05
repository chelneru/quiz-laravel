@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href={{route('classes')}}>Classes</a>
    <a class="breadcrumb grey-text text-darken-4" href={{route('classes')}}>{{shortenedString($class_info->name,50)}}</a>
    <a class="breadcrumb grey-text text-darken-4"
       href="/class/class-additional-info/{{$class_info->id}}">Additional info</a>
@endsection
@section('help-page')
    <div class="help-modal-header">Additional info</div>
    <p>Now that you have created the class, you can invite participants to enroll, create new quizzes inside it, or import an already existing quiz from another class. You  can do these tasks now or later by visiting the respective pages on the top menu.</p>
@endsection
@section('content')
    <link href="{{ asset('css/classes.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container page class-additional-info-page" data-class-id="{{$class_info->id}}">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-body">


                        <div class="row">
                            <div class="panel-row additional-actions-row">
                            <a class="btn invite-participants" href="/class/invite-participants/{{$class_info->id}}">invite
                                    participants</a>
                                <a class="btn create-new-quiz" href="/quiz/create-quiz?class_id={{$class_info->id}}">create a new
                                    quiz</a>
                                <a id="add_quizzes" class="btn modal-trigger" href="#add_existing_quiz_modal">add an
                                    existing quiz</a>
                            </div>

                        </div>
                        <div class="divider"></div>

                    </div>
                    <div class="panel-footer">
                        <div class="row right-align ">
                            <a class="btn cancel-create-btn noselect" href={{route('classes')}}>go to classes page</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="{{ asset('js/class_after_creation.js',config('app.secure', null)) }}"></script>
    @php
        $class_id = $class_info->id;
    @endphp
    @include('quizzes.add-existing-quiz')
@endsection
