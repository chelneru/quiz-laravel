@extends($layout)
@section('title')
    <a class="breadcrumb grey-text text-darken-4"
       href="/quiz/quiz-info/{{$progress->quiz_id}}">{{shortenedString($progress->quiz_title,20)}}</a>
    <a class="breadcrumb grey-text text-darken-4"
       href="#">@if($progress->phase ==1){{'Initial Phase'}}@else{{'Revision Phase'}}@endif</a>
@endsection
@section('tab-title') - {{shortenedString($progress->quiz_title,20)}}@endsection

@section('content')
    <style>.help-button {
            display: none !important;
        }</style>
    <link href="{{ asset('css/materialize.css',config('app.secure', null)) }}" rel="stylesheet">
    <link href="{{ asset('css/running_quiz.css',config('app.secure', null)) }}" rel="stylesheet">

    <div class="container running-quiz-page">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if($is_acc_question == true)
                @include('quizzes.running-quiz.accompanying-question')
                @else
                    <form id="question-form" method="post" action="/quiz/submit-answer">
                        @csrf
                        <input type="hidden" name="question_id" value="{{$question['id']}}">
                        <input type="hidden" name="quiz_id" value="{{$progress->quiz_id}}">
                        @include('quizzes.running-quiz.question')
                    </form>
                @endif
            </div>
        </div>


    </div>

    <script src="{{ asset('js/running_quiz.js',config('app.secure', null)) }}" defer></script>

@endsection
