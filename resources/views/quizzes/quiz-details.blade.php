@extends('layouts.app')
@section('title')
    @if(!$is_admin)
    <a class="breadcrumb grey-text text-darken-4" href={{route('quizzes')}}>Quizzes</a>
    <a class="breadcrumb grey-text text-darken-4"
       href="#">{{shortenedString($quiz->title,40)}}</a>
    <a class="breadcrumb grey-text text-darken-4"
       href="/quiz/quiz-info/{{$quiz->id}}">Quiz details</a>
    @else
        <a class="breadcrumb grey-text text-darken-4" href={{route('admin-quizzes')}}>Quizzes</a>
        <a class="breadcrumb grey-text text-darken-4"
           href="#">{{shortenedString($quiz->title,40)}}</a>

    @endif
@endsection
@section('tab-title') - {{shortenedString($quiz->title,20)}} - Quiz Details @endsection
@section('help-page')
    <div class="help-modal-header">Quiz details</div>
    <p>Here, you can have an overview of the quiz questions, their choices, and the correct answers. The direct link can be shared with participants. In case you have allowed anonymous participation in the quiz, the link will direct the participants straight to the quiz. In case anonymous participation is not enabled, the link will direct the participants to the login page, before they are automatically redirected to the quiz.</p>
    <p>The menu at the top of the tab directs you to additional actions you can perform on a quiz:</p>
    <ul>
        <li><i>START/MONITOR QUIZ</i>: This is where you can monitor the quiz and enable each of its phases.</li>
        <li><i>EDIT</i>: Clicking this will give you four additional options that will allow you to edit the quiz, the accompanying questions, the starting message (i.e., consent form), and the scheduling of the quiz.</li>
        <li><i>DUPLICATE & EDIT</i>: This will create a duplicate of the quiz on the same class and will automatically redirect you in the editing page of the quiz.</li>
        <li><i>DUPLICATE</i>: Same as before, without the redirection to the editing page.</li>
        <li><i>EXPORT</i>: You can export the data of a finished session. This means that you will be able to get an Excel file with all user answers to all questions (including accompanying) only for sessions of the quiz that have already finished. You can use a quiz several times by starting it and stopping it multiple times. This will create different quiz sessions.</li>
        <li><i>DELETE</i>: This will completely remove the quiz from the system. This action is not reversible.</li>
    </ul>
    @endsection
@section('content')
    <link href="{{ asset('css/quizzes.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container quiz-details-page" data-quiz-id="{{$quiz->id}}">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-body">
                        @if($is_admin === false)
                        <div class="row quiz-controls-row">
                                <a class="btn-flat" href="/quiz/quiz-monitoring-panel/{{$quiz->id}}">Start/Monitor Quiz</a>
                                <a class="btn-flat modal-trigger" href="#quiz-edit-options-modal">Edit</a>
                                <a class="btn-flat" href="/quiz/duplicate-edit-quiz/{{$quiz->id}}">Duplicate & Edit</a>
                                <a class="btn-flat" href="/quiz/duplicate-quiz/{{$quiz->id}}">Duplicate</a>
                                <a class="btn-flat" href="/quiz/export-quiz/{{$quiz->id}}">Export</a>
                                <a class="btn-flat modal-trigger" href="#quiz-delete-confirm-modal">Delete</a>
                        </div>
                        <div class="divider"></div>
                        @endif

                        <div class="row">
                            <div class="panel-row">
                                <div class="panel-label">Title:</div>
                                <div class="panel-info-field">{{$quiz->title}}</div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="panel-row">
                                <div class="panel-label">Description:</div>
                                <div class="panel-info-field">{{$quiz->description}}</div>
                            </div>

                        </div>
<div class="row">
                            <div class="panel-row">
                                <div class="panel-label">Class:</div>
                                <div class="panel-info-field">{{$quiz->class_name}}</div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="panel-row">
                                <div class="panel-label">Direct link:</div>
                                <div class="panel-info-field"><a id="quiz-link" href="{{$quiz->link}}">{{$quiz->link}}</a></div>
                            <button class="btn-flat teal-text copy-direct-link">copy</button>

                            </div>

                        </div>
                        @if(isset($quiz->questions) && is_array($quiz->questions->toArray()))
                            @foreach($quiz->questions as $question)
                                <div class="divider"></div>

                                <div class="row">
                                    <div class="panel-row">
                                        <div class="panel-label">Question title:</div>
                                        <div class="panel-info-field">{{($loop->index+1).'. '}}{{$question->question_text}}</div>
                                    </div>
                                </div>
                            @if($question->image_link !== null && $question->image_link !== '')
                                <div class="row">
                                    <div class="panel-row">
                                        <div class="panel-label">Question image:</div>
                                        <div class="panel-info-field"><img style="max-width: 100%" src="{{$question->image_link}}"></div>
                                    </div>
                                </div>
                                @endif
                                @foreach($question->question_answers as $answer)
                                    <div class="row">
                                        <div class="panel-row">
                                            <div class="panel-label">@if($loop->first)Options:@endif</div>
                                            @if($loop->index+1 == $question->question_correct_answer)<i class="correct-icon small material-icons">check</i>@endif

                                            <div class="panel-info-field @if($loop->index+1 == $question->question_correct_answer){{'correct-answer'}}@endif">
                                                {{$answer->answer_text}}</div>

                                        </div>
                                    </div>
                                    @endforeach

                            @endforeach
                        @endif

                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <a class="btn cancel-create-btn grey noselect" href=@if($is_admin){{route('admin-quizzes')}}@else{{route('quizzes')}}@endif>return to quizzes page</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('quizzes.confirmation-panel')
        @include('quizzes.edit-quiz-modal')

    </div>

    <script src="{{ asset('js/quizzes.js',config('app.secure', null)) }}" defer></script>

@endsection
