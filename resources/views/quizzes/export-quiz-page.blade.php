@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href={{route('quizzes')}}>Quizzes</a>
    <a class="breadcrumb grey-text text-darken-4"
       href="/quiz/quiz-info/{{$quiz_id}}">{{shortenedString($quiz_title,50)}}</a>
    <a class="breadcrumb grey-text text-darken-4" href="#">Export quiz session</a>
@endsection
@section('tab-title') - Export quiz {{shortenedString($quiz_title,20)}}@endsection
@section('help-page')
    <div class="help-modal-header">Export quiz session</div>
    <p>Here, you can export in an Excel file the data recorded in a past quiz session. A quiz can be used multiple times by starting and stopping it. Once you stop a quiz, the session of the quiz is considered closed and no more data are included in it. When you export a quiz session you have two additional options:</p>
    <ul>
        <li>
            <i>Show Correctness per question</i>: This will include in the Excel file a column that will denote whether participants’ answers in each question were correct or not.
        </li>
        <li>
            <i>Exclude incomplete quiz responses</i>: This will omit any incomplete rows. An incomplete row occurs when a participant leaves a quiz unfinished.         </li>
    </ul>
@endsection
@section('content')
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/export_quiz.css',config('app.secure', null)) }}" rel="stylesheet">

    <div id="{{$quiz_id}}" class="container quiz-export-page page">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">
                        {{ $sessions->links() }}

                        <table class="quiz-table striped responsive-table">
                            <thead>
                            <tr>
                                <th>Started at</th>
                                <th>Stopped at</th>
                                <th>Responses</th>

                                <th class="right-align">Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($sessions as $session)
                                <tr id="{{$session->id}}">
                                    <td>{{$session->started_at}}</td>
                                    <td>{{$session->stopped_at}}</td>
                                    <td>{{$session->responses}}</td>

                                    <td class="right-align">
                                        @if($session->stopped_at !== null)
                                        <a class="btn-flat modal-trigger open-export-modal" href="#export_session_modal">export</a>
                                            @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                            {{ $sessions->links() }}

                    </div>
                    <div class="panel-footer">

                        <div class="row">
                            <a class="btn cancel-create-btn  grey noselect" href={{route('quizzes')}}>return to quizzes page</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="export_session_modal" class="modal">
        <i class="material-icons right modal-close">close</i>
        <form id="export_session_form" method="POST" action="/quiz/export-quiz">
        @csrf
            <input type="hidden" name="session_id" value="">
            <div class="modal-content">
                <div class="row">
                    <label>
                        <input type="checkbox" class="filled-in"
                               name="show_correctness"/>
                        <span>Show correctness per question</span>
                    </label>
                </div>
                <div class="row">

                    <label>
                        <input type="checkbox" class="filled-in"
                               name="exclude_incomplete"/>
                        <span>Exclude incomplete quiz responses</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn submit-export-form-btn">export session</button>
            </div>
        </form>
    </div>
    <script src="{{ asset('js/export_quiz.js',config('app.secure', null)) }}" defer></script>

@endsection
