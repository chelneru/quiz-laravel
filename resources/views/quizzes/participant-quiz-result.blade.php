@extends($layout)
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href={{route('quizzes')}}>Quizzes</a>
    <a class="breadcrumb grey-text text-darken-4"
       href="#">Results</a>
@endsection
@section('tab-title') - Quiz results for {{shortenedString($quiz_info->title,20)}}@endsection
@section('help-page')
    <div class="help-modal-header">Results</div>
    <p>Here, you can see your scores in the quiz. At the top right corner, you can see your scores in the two phases of the quiz, while in the area below, you can see the correct answer (marked with a green border) and your final answer (marked with a green highlight). If you have taken the same quiz several times, then you can also see your scores in previous attempts by selecting the respective session on the dropdown list.</p>
@endsection
@section('content')

    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/quiz_results.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container quiz-result-page" data-quiz-id="@if($quiz_info !== null){{$quiz_info->id}}@endif">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="default-panel z-depth-2">
                    <div class="result-headers">
                        <div class="class-info">Class :
                            @if($quiz_info !== null)
                            <span class="tooltipped" data-position="top"
                                  data-tooltip="{{$quiz_info->class_name}}">
                            @if(strlen($quiz_info->class_name) > 20){{substr($quiz_info->class_name,0,20).'...'}}
                                @else{{$quiz_info->class_name}}@endif</span>
                                @endif
                        </div>
                        <div class="quiz-title">Quiz :
                            @if($quiz_info !== null)
                            <span class="tooltipped" data-position="top"
                                                             data-tooltip="{{$quiz_info->title}}">@if(strlen($quiz_info->title) > 20){{substr($quiz_info->title,0,20).'...'}}
                                @else{{$quiz_info->title}}@endif</span>

                        @endif</div>
                        <div class="session-scores">
                            Scores:
                            <div class="initial-score">Initial ({{$session_data['scores']['initial']}}
                                / {{$session_data['scores']['total']}})
                            </div>
                            <div class="final-score">Final ({{$session_data['scores']['revision']}}
                                / {{$session_data['scores']['total']}})
                            </div>
                            <div class="scores-legend">
                                <div class="correct-answer-template">Correct answer</div>
                                <div class="final-answer-template">Final answer</div>
                            </div>
                        </div>
                    </div>
                    <div class="result-info">
                        <div class="quiz-tries-div">
                        </div>
                        @if(isset($sessions) && $sessions !== null)

                        <div class="quiz-session-filter">
                            <span>Taken on</span>
                            <select class="session-select dropdown-content">

                                @foreach($sessions as $session)
                                    <option value="{{$session->progress_id}}" @if($session->progress_id == $progress_id){{'selected'}}@endif>{{$session->pr_started_at}}</option>
                                @endforeach
                            </select>

                        </div>
                        @endif

                    </div>
                    <div class="result-answers">
                        @if($session_data !== null)
                        @foreach($session_data['questions'] as $question)
                            <div class="question-div">
                                <div class="question-title">
                                    {{'Q'.($loop->index+1).' '.$question['text']}}
                                    @if(isset($question['image_link']))
                                        <div class="image-link-div">
                                    <img class="image-link" src="{{$question['image_link']}}">
                                        </div>
                                    @endif
                                    <div class="response">

                                        <div class="participant-answer">
                                            @if(isset($question['revision_response']) && $question['revision_response'] == $question['right_answer'])  <i
                                                    class="answer-result-icon small material-icons green-text text-lighten-2">check</i>
                                            @else
                                                <i class="answer-result-icon small material-icons red-text text-lighten-1">remove</i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="answers-row">
                                        @foreach($question['answers'] as $answer)
                                            <div class="answer-div

                                            @if($loop->index+1 == $question['right_answer']){{'correct-answer'}}@endif
                                            @if(isset($question['revision_response']) && $loop->index+1 == $question['revision_response']){{'participant-answer'}}@endif

                                                    ">{{strtoupper(chr(64 + $loop->index+1)).'.'.$answer['text']}}</div>
                                        @endforeach


                                    </div>

                                </div>
                            </div>
                            <div class="divider"></div>
                        @endforeach
                            @endif
                    </div>
                </div>
            </div>
        </div>


    </div>

    <script src="{{ asset('js/quiz_results.js',config('app.secure', null)) }}" defer></script>
@endsection
