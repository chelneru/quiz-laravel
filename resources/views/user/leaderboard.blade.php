@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href="/leaderboards">Leaderboard</a>
@endsection

@section('content')
    <link href="{{ asset('css/leaderboard.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container leaderboard-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>

                    <div class="panel-body">
                        @if(isset($classes))

                            <div class="">
                                <select class="class-filter">

                                    @foreach($classes as $class)
                                        <option value="{{$class->id}}"
                                        @if($class_filter == $class->id){{'selected'}}@endif>{{shortenedString($class->name,30)}}</option>
                                    @endforeach
                                </select>
                                <label>Class</label>
                            </div>
                            @endif
                        <div class="section-title">General</div>
                        <div class="divider"></div>
                        <div class="general-section">
                            <div class="info-categ">
                                <div class="info-text">Students who have answered questions</div>
                                <div class="info-value">{{$no_of_participants}}</div>
                            </div>
                            <div class="info-categ">
                                <div class="info-text">Number of answers to all questions</div>
                                <div class="info-value">{{$no_of_answers}}</div>
                            </div>
                        </div>

                        <div class="section-title">Average answer time</div>
                        <div class="divider"></div>
                        <div class="time-section">
                            <div class="table">
                                <div class="headers">
                                    <div class="table-header">Rank</div>
                                    <div class="table-header">Avg. time</div>
                                </div>
                                <div class="body-rows">
                                    @foreach($info['times']  as $time)
                                        @if($loop->index+1 <=5)
                                        <div class="table-row">
                                            <div class="table-cell">{{$loop->index+1}}</div>
                                            <div class="table-cell">{{number_format($time, 2, '.', '')}}s</div>
                                        </div>
                                        @else
                                            @break
                                        @endif
                                    @endforeach

                                </div>

                            </div>
                            <div class="user-section">
                                <div class="user-text">Your average response time is</div>
                                <div class="user-value">{{number_format($user_info['times'], 2, '.', '')}}s</div>
                            </div>
                            <div id="times_chart" data-user-value ="{{$user_info['times']}}" data-chart="{{json_encode($info['times'])}}"></div>

                        </div>

                        <div class="section-title">Overall score</div>
                        <div class="divider"></div>
                        <div class="scores-section">
                            <div class="table">
                                <div class="headers">
                                    <div class="table-header">Rank</div>
                                    <div class="table-header">Overall score</div>
                                </div>
                                <div class="body-rows">
                                    @foreach($info['scores']  as $score)
                                        @if($loop->index+1 <=5)

                                        <div class="table-row">
                                            <div class="table-cell">{{$loop->index+1}}</div>
                                            <div class="table-cell">{{$score}}</div>
                                        </div>
                                        @else
                                            @break
                                        @endif

                                    @endforeach

                                </div>
                            </div>
                            <div class="user-section">
                                <div class="user-text">Your overall score is</div>
                                <div class="user-value">@if($user_info['scores'] !== null){{$user_info['scores']}}@else{{'0'}}@endif</div>
                            </div>
                            <div id="scores_chart" data-chart="{{json_encode($info['scores'])}}" data-user-value ="{{$user_info['scores']}}" ></div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script src="{{ asset('js/leaderboard.js',config('app.secure', null)) }}" defer></script>

@endsection
