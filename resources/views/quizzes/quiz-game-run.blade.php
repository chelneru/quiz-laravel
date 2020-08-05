@extends('layouts.empty-layout')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href="{{route('quizzes')}}">Quizzes</a>
    <a class="breadcrumb grey-text text-darken-4" href="{{route('quiz-game-run-page')}}">Quiz game run</a>
@endsection
@section('tab-title') - Quiz game run @endsection

@section('content')
    <link href="{{ asset('css/quiz_game.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <div class="container quiz-game-page run-page" data-responses="{{json_encode($responses)}}" style=" height: 99%; box-shadow: none">
        <div class="row justify-content-center" style="height: 100%">
            <div class="col-md-8" style="height: 100%">
                <div class="default-panel z-depth-2" style="height: 100%">
                    <div class="panel-body" style="height: 100%">
                        @foreach($responses['value'] as $quiz)
                            <div class="quiz-chart-container">
                                <div class="lead-div">
                                <img src="/images/green-wreath.jpg" alt="leading">
                                </div>
                                <div class="quiz-group">{{$quiz['group_name']}}</div>
                                <div id="quiz{{$quiz['id']}}" class="quiz-chart">
                                    <div class="init-column">
                                        <div class="top-rect">
                                            <div class="leading-position"></div>
                                        </div>
                                        <div class="bottom-rect" @if($quiz['rev_rep_count'] > 0){{'style=opacity:0.5;'}}@endif></div>

                                    </div><div class="rev-column">
                                        <div class="top-rect">
                                            <div class="leading-position"></div>
                                        </div>
                                        <div class="bottom-rect"></div>

                                    </div>
                                </div>
                                <div class="percentages">
                                    <div class="initial-percentage"></div>
                                    <div class="revision-percentage"></div>
                            </div>
                                <div class="chart-diff-div">
                                    <div class="chart-diff-text"></div>
                                </div>
                                <div class="quiz-group-size">(n=)</div>
                            </div>
                        @endforeach

                </div>
            </div>
        </div>
    </div>
    </div>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script src="{{ asset('js/quiz_game.js',config('app.secure', null)) }}"></script>

@endsection
