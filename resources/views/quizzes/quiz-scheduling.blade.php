@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href={{route('quizzes')}}>Quizzes</a>
    <a class="breadcrumb grey-text text-darken-4"
       href="/quiz/quiz-info/{{$quiz->id}}">{{shortenedString($quiz->title,50)}}</a>
    <a class="breadcrumb grey-text text-darken-4" href="#">Quiz scheduling</a>
@endsection
@section('tab-title') - When should the quiz be available for the participants? @endsection
@section('help-page')
    <div class="help-modal-header">Quiz scheduling</div>
    <p>Here, you can define how many times a participant can take the quiz and when the quiz should be available to the participants. Pay attention to the scheduling and the sequence of the quiz phases because once a quiz has started, any changes made to it will not take effect until the quiz is stopped and started again.
    </p>
   @endsection
@section('content')
    <link href="/css/flatpickr.min.css" rel="stylesheet">
    <link href="/css/confirmDate.css" rel="stylesheet">

    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/quiz_scheduling.css',config('app.secure', null)) }}" rel="stylesheet">

    <div id="{{$quiz->id}}" class="container quiz-scheduling-page page">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <form id="scheduling_form" method="POST" action="{{ route('update-quiz-scheduling') }}">
                        @csrf
                        <input type="hidden" name="quiz_id" value="{{$quiz->id}}">
                        <div class="panel-body">
                            <div class="quiz-participation-count-text">1. How many times can a participant take the quiz?</div>
                            <div class="quiz-participation-count-options-container">
                                <div class="quiz-participation-count-option">
                                    <label>
                                        <input name="quiz_participation_count" type="radio"
                                               value="1" @if($quiz->participation_limit == 1){{'checked'}}@endif/>
                                        <span>Only once</span>
                                    </label>
                                </div>
                                <div class="quiz-participation-count-option">

                                    <label>
                                        <input name="quiz_participation_count" type="radio"
                                               value="0" @if($quiz->participation_limit == 0){{'checked'}}@endif/>
                                        <span>No limit (as many as he/she wants)</span>
                                    </label>
                                </div>

                                <div class="quiz-participation-count-option">

                                    <label>
                                        <input name="quiz_participation_count" type="radio"
                                               value="2" @if($quiz->participation_limit > 1){{'checked'}}@endif/>
                                        <span>Specific number of times</span>
                                    </label>
                                    <div class="participation-input-div">
                                        <input class="quiz_participation_input"
                                               @if($quiz->participation_limit <= 1){{'disabled'}}@endif type="number"
                                               name="participation_input"
                                               value="@if($quiz->participation_limit > 1){{$quiz->participation_limit}}@endif">
                                    </div>
                                </div>
                            </div>
                            <div class="quiz-availability-text">2. When should the quiz be available for the participants?</div>
                            <div class="quiz-availability-container">
                                <div class="quiz-availability-option">
                                    <label>
                                        <input name="quiz_availability" type="radio"
                                               value="1"@if($quiz->active === null ||$quiz->active === 0){{'checked'}}@endif/>
                                        <span>I will start/stop every phase myself</span>
                                    </label>
                                </div>
                                <div class="quiz-availability-option">
                                    <label>
                                        <input name="quiz_availability" type="radio"
                                               value="2" @if($quiz->active === 1){{'checked'}}@endif/>
                                        <span>I want to schedule the start/stop of the quiz (I will still be able to edit or start/stop myself - I will be careful, not to affect active participants)</span>
                                    </label>
                                </div>
                            </div>
                            <div class="quiz-scheduling-section">
                                <div class="availability-row">
                                    <div class="schedule-label">Initial phase:</div>
                                    <div class="date-picker-div">
                                        <label id="initial_phase_start" class="picker-label">
                                            <input class="datepicker" name="initial_phase_start"
                                                   @if($quiz->active === 0){{'disabled'}}@endif value="{{$quiz->init_start}}">
                                            <i class="material-icons datepicker-icon @if($quiz->active === 0){{'grey-text'}}@else{{'black-text'}}@endif small">date_range</i>
                                        </label>
                                        <label id="initial_phase_end" class="picker-label">
                                            <input class="datepicker" name="initial_phase_end"
                                                   @if($quiz->active === 0){{'disabled'}}@endif value="{{$quiz->init_end}}">
                                            <i class="material-icons datepicker-icon @if($quiz->active === 0){{'grey-text'}}@else{{'black-text'}}@endif small">date_range</i>
                                        </label>

                                    </div>
                                </div>
                                <div class="availability-row">
                                    <div class="schedule-label">Revision phase:</div>
                                    <div class="date-picker-div">
                                        <label id="revision_phase_start" class="picker-label">
                                            <input class="datepicker" name="revision_phase_start"
                                                   @if($quiz->active === 0){{'disabled'}}@endif value="{{$quiz->rev_start}}">
                                            <i class="material-icons datepicker-icon @if($quiz->active === 0){{'grey-text'}}@else{{'black-text'}}@endif small">date_range</i>
                                        </label>
                                        <label id="revision_phase_end" class="picker-label">
                                            <input class="datepicker" name="revision_phase_end"
                                                   @if($quiz->active === 0){{'disabled'}}@endif value="{{$quiz->rev_end}}">
                                            <i class="material-icons datepicker-icon @if($quiz->active === 0){{'grey-text'}}@else{{'black-text'}}@endif small">date_range</i>
                                        </label>
                                    </div>
                                </div>
                                <div class="availability-row">
                                    <div class="schedule-label">Reveal answers:</div>
                                    <div class="date-picker-div">
                                        <label id="reveal_answers_start" class="picker-label">
                                            <input class="datepicker" name="reveal_answers_start"
                                                   @if($quiz->active === 0){{'disabled'}}@endif value="{{$quiz->ans_start}}">
                                            <i class="material-icons datepicker-icon @if($quiz->active === 0){{'grey-text'}}@else{{'black-text'}}@endif small">date_range</i>
                                        </label>
                                        <label id="reveal_answers_end" class="picker-label">
                                            <input id="reveal_answers_end" class="datepicker" name="reveal_answers_end"
                                                   @if($quiz->active === 0){{'disabled'}}@endif value="{{$quiz->ans_end}}">
                                            <i class="material-icons datepicker-icon @if($quiz->active === 0){{'grey-text'}}@else{{'black-text'}}@endif small">date_range</i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="panel-footer">

                        <a class="btn update-scheduling noselect">@if($create_quiz_mode === true){{'set scheduling (4/4)'}}@else{{'save changes'}}@endif</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/js/libs/flatpickr.min.js"></script>
    <script src="/js/libs/moment.min.js"></script>
    <script src="/js/libs/confirmDate.js"></script>

    <script src="{{ asset('js/quiz_scheduling.js',config('app.secure', null)) }}" defer></script>
@endsection
