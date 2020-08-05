@extends('layouts.app')
@section('title')<a class="breadcrumb grey-text text-darken-4" href="/home">@lang('nav-menu.dashboard-page')</a>

@endsection
@section('tab-title') - Dashboard @endsection
@section('help-page')
    <div class="help-modal-header">Dashboard</div>
    <p>In the <i>Dashboard</i>, you can have a comprehensive picture of all the classes you are enrolled in their quizzes. </p>
    <p>If a quiz has a blinking green dot next to its title, it means that the quiz is currently open and you can participate in it. In case you have started a quiz and you have accidentally closed the browser, you will be redirected to the question you were in, once you revisit the quiz (provided, of course, that the quiz is still open).</p>
    <p>If a quiz has a red dot next to its title, it means that the quiz is closed and that you cannot participate in it.</p>
    <p>You can join a class by clicking on the <i>JOIN A CLASS</i> button and submitting the class code (you should get this from your teacher). Alternatively, you can ask your teacher to invite you to the class by sending you an email invitation.</p>
@endsection
@section('content')
    <link href="https://cdn.quilljs.com/1.0.0/quill.snow.css" rel="stylesheet">

    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container participant-page dashboard-page">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="default-panel classes-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">
                        <div class="section"><span class="section-title">@lang('dashboard.section-classes')</span>
                            <a class="btn modal-trigger" href="#join_class_modal">@lang('dashboard.join-class-btn')</a>
                        </div>
                        <div class="divider"></div>
                        <table class="classes-table striped">

                            <tbody>
                            @forelse($classes as $class)
                                <tr>
                                    <td>
                                      {{$class->name}}
                                    </td>

                                </tr>
                            @empty
                                <tr class="empty-row">
                                    <td>@lang('dashboard.participant-msg-no-classes-rows')</td>
                                </tr>

                            @endforelse
                            </tbody>
                        </table>

                    </div>

                </div>

                <div class="default-panel quizzes-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">
                        <div class="section"><span class="section-title">@lang('dashboard.section-quizzes')</span></div>
                            <div class="divider"></div>
                            <table class="quiz-table striped">
                                <tbody>
                                @forelse($quizzes as $quiz)
                                    <tr>
                                        <td>
                                                <a id="{{$quiz->id}}"
                                               class="get-quiz-info"
                                               data-has-answers-revealed="@if($quiz->session !== null){{$quiz->session->reveal_answers}}@else{{0}}@endif"
                                               data-has-progress="@if($quiz->session !== null && $quiz->session->progress_id !== null){{1}}@else{{0}}@endif"
                                            >{{shortenedString($quiz->title,40)}}

                                            </a>
                                            @if($quiz->session !== null)<div class="running-status-icon pulse"></div>
                                            @else<div class="stopped-status-icon"></div>@endif</td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td>@lang('dashboard.participant-msg-no-quizzes-rows')</td>
                                    </tr>

                                @endforelse
                                </tbody>
                            </table>



                    </div>


                </div>
            </div>
        </div>
        <div id="join_class_modal" class="modal">
            <i class="material-icons right modal-close">close</i>

            <div class="modal-content">
                <div class="class-code-field-label">@lang('dashboard.insert-class-code')</div>
                <div class="input-field class-code-field">
                    <input class="class_code" name="class_code" value="">
                </div>
            </div>
            <div class="modal-footer"><a class="btn join-class-btn">@lang('dashboard.join-class-btn-action')</a></div>
        </div>
,
        <div id="quiz_presentation" class="modal">
            <i class="material-icons right modal-close">close</i>

            <div class="modal-content">
                <div class="quiz-title"></div>
                <div class=" row quiz-description"></div>
                <div class="divider"></div>
                <div class="row">
                </div>
                <div class=" row quiz-message-title"></div>
                <div class="row">
                <div id="editor" class="quiz-message"></div>
                </div>
                <div class="modal-buttons">
                    <a class="btn start-quiz-btn" href="#">@lang('dashboard.modal-start-quiz')</a>
                </div>
            </div>

        </div>
    </div>
    <script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>

    <script src="{{ asset('js/dashboard.js',config('app.secure', null)) }}" defer></script>

@endsection
