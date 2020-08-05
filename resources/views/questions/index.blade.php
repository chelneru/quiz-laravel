@extends('layouts.app')
@section('title')<a class="breadcrumb grey-text text-darken-4" href="{{route('questions')}}">Questions</a></a>
@endsection

@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/questions.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container questions-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">

                        <div class="input-field col s3">
                            <select id="class-dropdown">
                                <option value="" @if($class_filter == null){{'selected'}}@endif>All Classes</option>
                                @foreach($classes as $class)

                                    <option @if($class_filter == $class->id){{'selected'}}@endif value="{{$class->id}}">{{$class->name}}</option>

                                @endforeach
                            </select>
                            <label>Class</label>
                        </div>

                        <div class="input-field col s3">
                            <select id="quiz-dropdown">
                                <option value="" @if($quiz_filter == null){{'selected'}}@endif>All Quizzes</option>
                                @foreach($quizzes as $quiz)
                                    <option  @if($quiz_filter == $quiz->id){{'selected'}}@endif value="{{$quiz->id}}">{{$quiz->title}}</option>
                                @endforeach
                            </select>
                            <label>Quiz</label>
                        </div>
                        @if ($questions->total() > $questions->perPage())
                            {{ $questions->links() }}
                        @endif
                        <table class="quiz-table striped responsive-table">
                            <thead>
                            <tr>
                                <th>Question</th>
                                <th>Quiz</th>
                                <th>Class</th>
                                <th class="right-align">Created on</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($questions as $question)
                                <tr id="{{$question->id}}">
                                    <td class="tooltipped" data-position="top" data-tooltip="{{$question->question_text  }}">{{shortenedString($question->question_text, 30)}}</td>
                                    <td><a href="/quiz/quiz-info/{{$question->quiz_id}}">{{shortenedString($question->quiz_text, 20)}}</a></td>
                                    <td><a href="/class/class-info/{{$question->class_id}}">{{$question->class_text}}</a></td>
                                    <td class="right-align">{{$question->created_on}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if ($questions->total() > $questions->perPage())
                            {{ $questions->links() }}
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/questions.js',config('app.secure', null)) }}" defer></script>

@endsection
