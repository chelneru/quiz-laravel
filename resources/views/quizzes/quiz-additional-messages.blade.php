@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href={{route('quizzes')}}>Quizzes</a>
    <a class="breadcrumb grey-text text-darken-4"
       href="/quiz/quiz-info/{{$quiz->id}}">{{shortenedString($quiz->title,50)}}</a>
    <a class="breadcrumb grey-text text-darken-4" href="#">Quiz disclaimer/Start message</a>
@endsection
@section('tab-title') - Additional Message @endsection
@section('help-page')
    <div class="help-modal-header">Quiz disclaimer/Start message</div>
    <p>Here, you must write an informative text about the quiz including information on its purpose, your
        identity, and your plans for the recorded data.</p>
    <p style="color: red">IMPORTANT: This is also when you need to ask for participants&#39; consent! Therefore, make sure that
        when the participants click on the Next button, they will know adequate information about the quiz
        and they have given you their consent in recording and using their data. Also, if you have marked
        this quiz as &quot;Assessment quiz&quot;, then you also need to inform them that their names are going to be
        visible to you (only you, not to other participants).</p>
@endsection
@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/additional_messages.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/quizzes.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.0.0/quill.snow.css" rel="stylesheet">

    <div id="{{$quiz->id}}" class="container quiz-additional-messages-page page"  data-interaction-type="@if($create_quiz_mode){{'create'}}@else{{'edit'}}@endif">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <form>

                        <div class="panel-body">
                                <div class="input-counter-div">
                                    <input type="text" class="" name="message_title"  maxlength="100"  placeholder="Enter message title here" value="@if($quiz_message_info->message_title ==''){{'Consent'}} @else{{$quiz_message_info->message_title}} @endif">
                                    <span class="input-counter">0/100</span>

                                </div>
                            <!-- Create the editor container -->
                            <div id="editor">

                                @if($quiz_message_info->message =='')
                                    <p>Here, you should write an informative text about the quiz including information on its purpose, your identity, and your plans for the recorded data.</p>
                                    <p>IMPORTANT: This is also when you need to ask for participants' consent! Therefore, make sure that when the participants click on the Next button they will know adequate information about the quiz and they have given you their consent in recording and using their data. Also, if you have marked this quiz as "Assessment quiz", then you also need to inform them that their names are going to be visible to you (only you, not to other participants).</p>

                                @endif
                            </div>
                            @if($quiz_message_info->message !='')

                                <div class="existing-editor-content">{{$quiz_message_info->message}}</div>
                            @endif
                        </div>
                    </form>
                    <div class="panel-footer">

                            <a class="btn save-additional-messages-btn noselect">@if($create_quiz_mode === true){{'set starting message (3/4)'}}@else{{'save changes'}}@endif</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>

    <script src="{{ asset('js/additional_messages.js',config('app.secure', null)) }}" defer></script>

@endsection
