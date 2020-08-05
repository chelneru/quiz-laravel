@extends('layouts.app')
@section('title')<a class="breadcrumb grey-text text-darken-4" href={{route('quizzes')}}>Quizzes</a></a>
@endsection
@section('action-buttons-space')
    <a class="btn" href="/quiz/create-quiz">create new quiz</a>
@endsection
@section('tab-title') - Quizzes @endsection
@section('help-page')
    <div class="help-modal-header">Quizzes</div>
    <p>Here, you can have a comprehensive picture of all your quizzes, the class they belong to and the date they were created. You can narrow down the results by using the class filter.</p>
    <p>You can click on the <i>CREATE NEW QUIZ</i>  button to create a new quiz, or click on the option icon on a quiz row to view the quiz details, edit it, duplicate and edit the quiz, duplicate as is, export its data, or delete it. </p>
@endsection
@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/quizzes.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container quizzes-page">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">

                        <div class="input-field col s3">
                            <select class="class-filter">
                                <option value=""@if($class_filter == null){{'selected'}}@endif>All classes</option>
                                @foreach($classes as $class)

                                    <option value="{{$class->id}}"
                                    @if($class_filter == $class->id){{'selected'}}@endif>{{shortenedString($class->name,30)}}</option>
                                @endforeach
                            </select>
                            <label>Class</label>
                        </div>
                        @if($class_filter !== null && $quizzes->total() > $quizzes->perPage())
                            {{$quizzes->appends(['class' => $class_filter])}}
                        @else
                            {{ $quizzes->links() }}
                        @endif
                        <table class="quiz-table striped responsive-table">
                            <thead>
                            <tr>
                                <th>Title</th>
                                <th>Class</th>
                                <th class="right-align">Created on</th>
                                <th class="right-align">Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($quizzes as $quiz)
                                <tr id="{{$quiz->id}}">
                                    <td class="quiz-title-cell"><a href="/quiz/quiz-info/{{$quiz->id}}">{{shortenedString($quiz->title,70)}}</a></td>

                                    <td>{{$quiz->class_name}}</td>
                                    <td class=" right-align">{{$quiz->date}}</td>
                                    <td class="right-align">


                                        <!-- Dropdown Trigger -->
                                        <a class='dropdown-trigger black-text' href='#'
                                           data-target='dropdown{{$loop->index+2}}'>
                                            <i class="material-icons options-icon">more_vert</i>

                                        </a>
                                        <!-- Dropdown Structure -->
                                        <ul id='dropdown{{$loop->index+2}}' class='dropdown-content black-text'>
                                            <li><a href="/quiz/quiz-info/{{$quiz->id}}">View Details</a></li>
                                            <li><a href="/quiz/edit-quiz/{{$quiz->id}}">Edit</a></li>
                                            <li><a href="/quiz/duplicate-edit-quiz/{{$quiz->id}}">Duplicate & Edit</a>
                                            </li>
                                            <li><a href="/quiz/duplicate-quiz/{{$quiz->id}}">Duplicate</a></li>
                                            <li><a href="/quiz/export-quiz/{{$quiz->id}}">Export</a></li>
                                            <li><a class="delete-quiz-button" href="#!">Delete</a></li>
                                        </ul>

                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-row">
                                    <td colspan="4">@lang('dashboard.msg-no-quizzes-rows')</td>
                                </tr>

                            @endforelse
                            </tbody>
                        </table>
                        @if($class_filter !== null && $quizzes->total() > $quizzes->perPage())
                            {{$quizzes->appends(['class' => $class_filter])}}
                        @else
                                {{ $quizzes->links() }}
                        @endif

                    </div>

                </div>
            </div>
        </div>
        @include('quizzes.confirmation-panel')
    </div>
    <script src="{{ asset('js/quizzes.js',config('app.secure', null)) }}" defer></script>

@endsection
