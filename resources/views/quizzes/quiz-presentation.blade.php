@extends('layouts.app')
@section('title')
    <a class="breadcrumb grey-text text-darken-4" href={{route('quizzes')}}>Quizzes</a>
<a class="breadcrumb grey-text text-darken-4"
   href="/quiz/quiz-info/{{$quiz->data['id']}}">{{shortenedString($quiz->data['title'],40)}}</a>
<a class="breadcrumb grey-text text-darken-4"
   href="#">Quiz presentation</a>
@endsection
@section('tab-title') - Quiz presentation - {{shortenedString($quiz->data['title'],10)}}@endsection

@section('content')
    <link href="{{ asset('css/quiz_presentation.css',config('app.secure', null)) }}" rel="stylesheet">
    <div class="container quiz-presentation-page page" data-questions="{{json_encode($quiz->questions)}}">
        <div class="row justify-content-center">
            <div class="col-md-8">
    <div class="default-panel quiz-presentation-panel z-depth-2" data-right-answer="{{$quiz->questions[0]['right_answer']}}">

    <div class="panel-header"></div>
    <div class="panel-body">
        <div class="header-row row index-row">
            <div class="question-index">Question 1</div>
        </div>
        <div class="header-row">
            <div class="main-question-title">{{$quiz->questions[0]['text']}}</div>
        </div>
            <div class="header-row row">
                <div class="main-question-image"
                @if(!isset($quiz->questions[0]['image_link']) || $quiz->questions[0]['image_link'] == '')
                   style="display: none"
                    @endif
                    >
                    <img src="{{$quiz->questions[0]['image_link']}}" alt="Loading image...">
                </div>
            </div>
        <div class="panel-body-left full">

                <div class="revision-metrics-section">
                    <div class="responses-headers">

                        <div class="init-responses">Init (%)</div>
                        <div class="rev-responses">Rev (%)</div>

                    </div>
                    <div class="responses">
                        @foreach($quiz->questions[0]['answers'] as $answer)
                            <div class="choice-div" >

                                <div class="initial-phase-response">{{number_format($answer['init_resp'] , 2, '.', '')}}
                                    %
                                </div>
                                <div class="revision-phase-response">{{number_format($answer['rev_resp'], 2, '.', '')}}
                                    %
                                </div>

                            </div>
                        @endforeach


                    </div>
                </div>

                <div class="headers-row">
                    <div class="answers-header">Answers</div>


                </div>
            <div class="question-choices">

                @foreach($quiz->questions[0]['answers'] as $answer)
                    <div class="choice-div">
                        {{strtoupper(chr(64 + $loop->index+1)).' : '.$answer['text']}}
                    </div>
                @endforeach
            </div>


        </div>

    </div>
    <div class="panel-footer">
        <button type="button" class="btn next-question-btn">next question</button>
        <button type="button" class="btn show-answer-btn">right answer</button>
        <button type="button" class="btn previous-question-btn">previous question</button>
    </div>



</div>
    <script src="{{ asset('js/quiz_presentation.js',config('app.secure', null)) }}" defer></script>

@endsection
