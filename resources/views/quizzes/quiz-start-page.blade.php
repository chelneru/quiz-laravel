@extends($layout)

@section('title')<a class="breadcrumb grey-text text-darken-4" href="#">Starting message (Consent)</a>

@endsection
@section('tab-title') - Quiz start page @endsection

@section('content')
    <link href="https://cdn.quilljs.com/1.0.0/quill.snow.css" rel="stylesheet">

    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/quiz_start.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container quiz-start-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">

                        <div class="quiz-title">{{$quiz->title}}</div>
                        <div class=" row quiz-description">{{$quiz->description}}</div>
                        <div class="divider"></div>
                        @if($quiz->is_assessed)
                            <div>This quiz is marked for assessment. This means that your first and last name will be visible to the quiz-creator in the scoreboard.</div>
                        @endif
                            @if(isset($quiz->message_title) &&$quiz->message_title !== null && $quiz->message_title != '')
                        <div class=" row quiz-message-title">{{$quiz->message_title }}</div>
                        @endif
                        <div class="row">
                            <div id="editor" class="quiz-message" data-message="{{$quiz->message ?? ''}}"></div>
                        </div>
                        <div class="modal-buttons">
                            @if($quiz->status === true)
                                <form method="post" action="{{route('participant-start-quiz')}}">
                                    @csrf
                                    <input type="hidden" name="participant_id" value="{{$participant_id}}">
                                    <input type="hidden" name="session_id" value="{{$session_id}}">
                                    <input type="hidden" name="quiz_id" value="{{$quiz->id}}">
                                    <input type="hidden" name="anon_participation" value="{{$participant_id !== null}}">
                                    <button type="submit" class="btn start-quiz-btn">@if(isset($ongoing_progress) && $ongoing_progress!== null){{'resume quiz'}}@else{{'start quiz'}}@endif</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>

    <script src="{{ asset('js/quiz_start.js',config('app.secure', null)) }}" defer></script>

@endsection
