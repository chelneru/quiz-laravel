@extends('layouts.app')
@section('title')<a class="breadcrumb grey-text text-darken-4" href={{route('quizzes')}}>Quizzes</a></a>
@endsection
@section('tab-title') - Quizzes @endsection
@section('help-page')
    <div class="help-modal-header">Quizzes</div>
    <p>Here, you can see all the quizzes of the classes you are enrolled in.</p>
@endsection
@section('content')
    {{--<link href="{{ asset('css/profile.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/quizzes.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container quizzes-page participants-side">
        <div class="row justify-content-center">
            <div class="col-md-8">


                <div class="default-panel z-depth-2">
                    <div class="panel-header"></div>
                    <div class="panel-body">

                        @if ($quizzes->total() > $quizzes->perPage())
                        {{ $quizzes->links() }}
                        @endif
                        <table class="quiz-table striped">
                            <thead>
                            <tr>
                                <th>Title</th>
                                <th>Class</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($quizzes as $quiz)
                                <tr id="{{$quiz->id}}">
                                    <td><a href="{{route('quiz-start-page',['quiz_id'=>$quiz->id])}}">{{$quiz->title}}</a></td>

                                    <td>{{$quiz->class_name}}</td>

                                </tr>

                                @empty
                                <tr id="">

                                    <td colspan="2" style="text-align: center">You are not enrolled in any quiz</td>

                                </tr>
                            @endforelse
                            </tbody>
                        </table>

                            @if ($quizzes->total() > $quizzes->perPage())
                                {{ $quizzes->links() }}
                            @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/quizzes_participants.js',config('app.secure', null)) }}" defer></script>

@endsection
