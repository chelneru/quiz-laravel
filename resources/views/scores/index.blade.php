@extends('layouts.app')
@section('title')<a class="breadcrumb grey-text text-darken-4" href="/scores">Scores</a></a>
@endsection
@section('action-buttons-space')
@endsection
@section('tab-title') - Scores @endsection
@section('help-page')
    <div class="help-modal-header">Scores</div>
    <p>Here, you can have a comprehensive view of the performance of participants in your quizzes. First, you will have to select a class and a quiz. Then SAGA will show you all the finished sessions of this quiz. Note that a quiz can be used several times by stopping it and starting it multiple times. Each time a quiz is stopped, the system considers that quiz session closed and when it starts again, a new session is created. To see the scores of participants in a quiz session, just click on the session name. </p>
    <p>If the quiz was used for self-assessment, you will not see the names of the participants. Instead, you will see <i>User</i> IDs numbers. That way, the participants can take a quiz anonymously, and you can follow their progress in different quizzes by noting their <i>User</i> IDs. You will not be able to know the identity of a participant behind this number. On the contrary, If a quiz was marked for assessment, the participants’ first and last name will be visible as <i>User</i> ID in the scores page and you will be able to identify them and assess their performance. </p>
    <p>he score table includes information on when the quiz was finished, the score of the participants in the initial and the revision phase, and the score difference between the two phases.</p>
@endsection
@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/scores.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container scores-page" data-quiz="{{json_encode($quizzes)}}"  data-participants="{{json_encode($participants_autocomplete)}}">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="default-panel z-depth-2 quick-access-panel">
                    <div class="panel-header"></div>
                    <div class="panel-body">

                        <div class="input-field col s3 class-filter-field">
                            <select id="class-dropdown">
                                <option value="">All classes</option>
                                @foreach($classes as $class)
                                    <option value="{{$class->id}}" >{{shortenedString($class->name,20)}}</option>
                                @endforeach
                            </select>
                            <label>Class</label>
                        </div>
                        <div class="input-field col s3 quiz-filter-field">
                            <select id="quiz-dropdown" >
                                <option value=""> </option>

                            </select>
                            <label>Quiz</label>
                        </div>
                        <div class="input-field col s3 autocomplete-participant">
                            <input type="text" id="autocomplete-participant-input" class="autocomplete" value="">
                            <label for="autocomplete-participant-input">Participant</label>
                        </div>
                        <div class="quick-access-area">
                            <div> <i class="material-icons prev-query teal-text text-lighten-2">chevron_left
                                </i></div>
                            <table class="highlight">
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="default-panel z-depth-2 scores-content-section">
                    <table class="scores-table">
                        <thead>
                        <tr>
                            <th class="name" data-function="sort-asc-name">
                                <span class="sort-group">
                                    <i class="material-icons asc">arrow_drop_up</i>
                                    <i class="material-icons desc">arrow_drop_down</i>
                                </span>
                            </th>
                            <th class="date" data-function="sort-asc-date">
                                <span class="sort-group">
                                    <i class="material-icons asc">arrow_drop_up</i>
                                    <i class="material-icons desc">arrow_drop_down</i>
                                </span></th>
                            <th class="init" data-function="sort-asc-init">
                                <span class="sort-group">
                                    <i class="material-icons asc">arrow_drop_up</i>
                                    <i class="material-icons desc">arrow_drop_down</i>
                                </span></th>
                            <th class="rev" data-function="sort-asc-rev">
                                <span class="sort-group" data-function="sort-asc-rev">
                                    <i class="material-icons asc">arrow_drop_up</i>
                                    <i class="material-icons desc">arrow_drop_down</i>
                                </span></th>
                            <th  class="diff" data-function="sort-asc-diff">
                                <span class="sort-group" data-function="sort-asc-diff">
                                    <i class="material-icons asc">arrow_drop_up</i>
                                    <i class="material-icons desc">arrow_drop_down</i>
                                </span></th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/libs/moment.min.js"></script>

    <script src="{{ asset('js/scores.js',config('app.secure', null)) }}" defer></script>

@endsection
