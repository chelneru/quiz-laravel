@extends('layouts.app')
@section('title')<a class="breadcrumb grey-text text-darken-4" href="/home">@lang('nav-menu.dashboard-page')</a>
@endsection
@section('tab-title') - Dashboard @endsection
@section('help-page')
    <div class="help-modal-header">Dashboard</div>
    <p>In the <i>Dashboard</i>, you can have a comprehensive picture of all the classes and quizzes you have created and the number of active/inactive participants in your quizzes. </p>
    <p>Active participants are users that have enrolled in your classes and have started at least one of your quizzes. Inactive participants are users that have enrolled in your classes but have not participated in a quiz yet. By clicking on the respective numbers, you can see the names of the students.</p>
    <p>In the <i>Classes</i> tab, you can see the title of a class, the number of quizzes you have created in it, and the number of total participants. Have in mind that one participant may be enrolled in several classes.</p>
    <p>In the <i>Quizzes</i> tab, you can see the title of a quiz, the number of questions you have created in it, and the classes in which it belongs. A red dot next to the quiz title means that the quiz is currently closed (no access for students), while a blinking green dot means that the quiz is currently open (students can answer it).</p>
    <p>From the <i>Dashboard</i>, you can take a shortcut and create a new class, a new quiz, or add participants to one of your classes. The same functionality is available in the respective pages <i>PARTICIPANTS</i>, <i>CLASSES</i>, <i>QUIZZES</i> that you can visit by using the top menu.</p>
@endsection
@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container teacher-page dashboard-page">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="default-panel classes-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">
                        <div class="section"><span class="section-title">@lang('dashboard.section-classes')</span>

                            <a class="btn right" href="/class/create-class">@lang('dashboard.create-new-class')</a>
                        </div>
                        <div class="divider"></div>
                        <table class="classes-table striped responsive-table">
                            <thead>
                            <tr>
                                <th>@lang('dashboard.class-table-header-title')</th>
                                <th>@lang('dashboard.class-table-header-quizzes')</th>
                                <th>@lang('dashboard.class-table-header-participants')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($classes as $class)
                                <tr>
                                    <td>
                                        @if($class->description != "")
                                            <a href="/class/class-info/{{$class->id}}" class="tooltipped"
                                               data-position="top"
                                               data-tooltip="{{$class->description}}">{{shortenedString($class->name,30)}}</a>

                                        @else
                                            <a href="/class/class-info/{{$class->id}}">{{shortenedString($class->name,30)}}</a>

                                        @endif
                                    </td>
                                    <td>{{$class->quizzes}}</td>
                                    <td>{{$class->participants}}</td>
                                </tr>
                            @empty
                                <tr class="empty-row">
                                    <td>@lang('dashboard.msg-no-classes-rows')</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                    </div>

                </div>

                <div class="default-panel quizzes-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">
                        <div class="section"><span class="section-title">@lang('dashboard.section-quizzes')</span>
                            <a class="btn right" href="/quiz/create-quiz">@lang('dashboard.create-new-quiz')</a></div>
                        <div class="divider"></div>
                        <table class="quiz-table striped responsive-table">
                            @if(count($quizzes) > 0)
                                <thead>
                                <tr>
                                    <th>@lang('dashboard.quiz-table-header-title')</th>
                                    <th>@lang('dashboard.quiz-table-header-questions')</th>
                                    <th>@lang('dashboard.quiz-table-header-class')</th>
                                </tr>
                                </thead>
                            @endif
                            <tbody>
                            @forelse($quizzes as $quiz)
                                <tr>
                                    <td><a href="/quiz/quiz-info/{{$quiz->id}}">{{shortenedString($quiz->title,30)}}</a>
                                        @if($quiz->session_id !== null)
                                            <div class="running-status-icon pulse"></div>
                                        @else
                                            <div class="stopped-status-icon"></div>@endif</td>
                                    <td>{{$quiz->questions}}</td>
                                    <td>
                                        <a href="/class/class-info/{{$quiz->class_id}}">{{shortenedString($quiz->class_name ?? '',30)}}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-row">
                                    <td>@lang('dashboard.msg-no-quizzes-rows')</td>
                                </tr>

                            @endforelse
                            </tbody>
                        </table>

                    </div>

                </div>

                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">
                        <div class="section"><span class="section-title">@lang('dashboard.section-participants')</span>
                            <a class="btn right" href="/class/invite-participants">@lang('dashboard.add-new-participants')</a></div>
                    </div>
                    <div class="divider"></div>
                    <table class="participants-table striped responsive-table">

                        <tbody>
                        <tr>
                            <td>Total</td>
                            <td>Active: <a class="active-list modal-trigger"
                                           href="#teacher-participants-list-modal">{{$participants->active_participants}}</a>
                            </td>
                            <td>Inactive: <a class="inactive-list modal-trigger"
                                             href="#teacher-participants-list-modal">{{$participants->inactive_participants}}</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>
    @include('dashboard.popup-user-list')
    <script src="{{ asset('js/dashboard.js',config('app.secure', null)) }}" defer></script>

@endsection
