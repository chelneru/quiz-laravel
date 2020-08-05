<div class="conf-question-section section inline">
    <div class="monitor-section-header">Confidence</div>
    <div class="divider"></div>
    <div class="phases">
        <div class="initial-phase-tab @if($quiz->phase == 1){{'selected'}}@endif">Initial phase</div>
        <div class="revision-phase-tab @if($quiz->phase == 2){{'selected'}}@endif">Revision phase</div>
    </div>
    <div class="conf-section-columns">
        <div class="all-column header">All</div>
        <div class="correct-column header">Correct</div>
        <div class="incorrect-column header">Incorrect</div>
    </div>

    @foreach(Config::get('defines.PHASES') as $phase)

        <div class="conf-phase-{{$phase}}" @if($phase == $quiz->phase){{'style=display:block'}}@else{{'style=display:none'}}@endif>
            @foreach($quiz->questions as $question)


                <div class="question-row" data-question-id="{{$question['id']}}">
                    <div class="question-row-title">Q {{$loop->index+1}}</div>

                    @if(isset($quiz->conf_question['responses'][$phase][$question['id']]))
                        <div class="all-conf-answers">{{number_format((float)$quiz->conf_question['responses'][$phase][$question['id']]['all_average'], 2, '.', '')}}</div>
                        <div class="correct-conf-answers">{{number_format((float)$quiz->conf_question['responses'][$phase][$question['id']]['correct_average'], 2, '.', '')}}</div>
                        <div class="incorrect-conf-answers">{{number_format((float)$quiz->conf_question['responses'][$phase][$question['id']]['incorrect_average'], 2, '.', '')}}</div>
                    @else
                        <div class="all-conf-answers">{{number_format((float)0, 2, '.', '')}}</div>
                        <div class="correct-conf-answers">{{number_format((float)0, 2, '.', '')}}</div>
                        <div class="incorrect-conf-answers">{{number_format((float)0, 2, '.', '')}}</div>

                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
</div>