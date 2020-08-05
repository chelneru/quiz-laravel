@if($other_question['structure'] == 2 && $other_question['other_question_position'] == 2)
    <div id="{{$other_question['id']}}" class="other-section">
        <div class="monitor-section-header">{{$other_question['name']}}</div>
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
            <div class="conf-phase-{{$phase}}" @if($phase != $quiz->phase){{'style=display:none'}}@endif>
                @foreach($quiz->questions as $question)


                    <div class="question-row" data-question-id="{{$question['id']}}">
                        <div class="question-row-title">Q {{$loop->index+1}}</div>

                        @if(isset($other_question['responses'][$phase][$question['id']]))
                            <div class="all-conf-answers">{{number_format((float)$other_question['responses'][$phase][$question['id']]['all_average'], 2, '.', '')}}</div>
                            <div class="correct-conf-answers">{{number_format((float)$other_question['responses'][$phase][$question['id']]['correct_average'], 2, '.', '')}}</div>
                            <div class="incorrect-conf-answers">{{number_format((float)$other_question['responses'][$phase][$question['id']]['incorrect_average'], 2, '.', '')}}</div>
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

@elseif($other_question['structure'] == 2 && ($other_question['other_question_position'] == 1 || $other_question['other_question_position'] == 3))
    <div id="{{$other_question['id']}}" class="other-section inline">
        <div class="monitor-section-header">{{$other_question['name']}}
        </div>
        <div class="divider"></div>
        <div id="other-question-pie-chart{{$other_question['id']}}"></div>
    </div>
@elseif($other_question['structure'] == 3 && $other_question['other_question_position'] == 2)
    <div id="{{$other_question['id']}}" class="other-section inline">
        <div class="monitor-section-header">{{$other_question['name']}}
        </div>
        <div class="divider"></div>
            <div class="row">
                <div class="input-field col s4">
                    <select class="other-question-select">
                        @foreach($quiz->questions as $question)
                            @if(in_array($question['id'],$other_question['feedback']))
                            <option value="{{$question['id']}}" @if($loop->index == 0) {{'selected'}}@endif>{{'Q '.($loop->index+1)}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="input-field col answers-tabs-field">
                    <div class="answers-tabs-row">
                        @foreach($quiz->questions[0]['answers'] as $answer)
                            <div class="answer-tab @if($loop->index ==0){{'selected'}}@endif"
                                 data-index="{{$loop->index+1}}">{{ strtoupper(chr(64 + $loop->index+1))}}</div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="other-question-container">
                @php
                    $first_question = reset($other_question['responses']);
                @endphp
                @if(isset($first_question) && is_array($first_question))
                    @foreach($first_question as  $key => $answer_index)
                        @if($key == 0 || $key == 1)
                            @foreach($answer_index as $response)
                                <div class="other-content-row">{{$response}}</div>
                            @endforeach
                        @endif
                        @break
                    @endforeach
                @endif
            </div>
    </div>
@elseif($other_question['structure'] == 3 && ($other_question['other_question_position'] == 1 || $other_question['other_question_position'] == 3))
    <div id="{{$other_question['id']}}" class="other-section outside-text inline">
        <div class="monitor-section-header">{{$other_question['name']}}
        </div>
        <div class="divider"></div>

        <div class="other-question-container">
            @foreach($other_question['responses'] as $response)

            <div class="other-content-row">{{$response}}</div>
            @endforeach
        </div>
    </div>
@endif
