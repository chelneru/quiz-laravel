<div class="percentage-section section">
    <div class="scores-legend">
        <div class="correct-answer-template">Correct answer</div>
        <div class="final-answer-template">Most popular answer</div>
    </div>
    <div class="choose-phase-tabs">
        <div class="select-phase-1 @if($quiz->phase == 1){{'selected'}}@endif">Initial phase</div>
        <div class="select-phase-2 @if($quiz->phase == 2){{'selected'}}@endif">Revision phase</div>
    </div>
    <div class="phases">

        @foreach(Config::get('defines.PHASES') as $phase)
            <div class="phase-{{$phase}}" @if($phase == $quiz->phase){{'style=display:block'}}@else{{'style=display:none'}} @endif>
                @if(isset($quiz->questions))

                    @foreach($quiz->questions as $question)

                        <div class="row" data-right-answer="{{$question['right_answer']}}"
                             data-index="{{$loop->index+1}}">
                            <div class="quiz-title">Q {{$loop->index+1}}</div>
                            <div class="quiz-answers">


                                @foreach($question['answers'] as $answer)


                                    <div class="question-answer-row">{{strtoupper(chr(64 + $loop->index+1))}}
                                        :
                                        @php
                                            if(isset($question['responses']) && $question['responses'][$phase]['total_responses'] > 0 ){
                                            $answer_percentage = $question['responses'][$phase]['answers_values'][$loop->index+1] / $question['responses'][$phase]['total_responses']*100;
                                            }
                                            else {
                                            $answer_percentage = 0;
                                            }
                                        @endphp
                                        <div class="answers-count"
                                             data-index="{{$loop->index+1}}">{{number_format((float)$answer_percentage, 2, '.', '')}}
                                            %
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endforeach

    </div>
</div>