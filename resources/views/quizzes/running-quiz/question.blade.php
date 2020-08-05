<div class="row">
    <div class="default-panel running-quiz-panel z-depth-2">
        <div class="panel-header"></div>
        <div class="panel-body">
            <div class="header-row row index-row">
                <div class="question-index">Question {{$question['index']}}</div>
            </div>
            <div class="header-row">
                <div class="main-question-title">{{$question['text']}}</div>
            </div>
            @if(isset($question['image_link']) && $question['image_link'] != '')
                <div class="header-row row">
                    <div class="main-question-image">
                        <img src="{{$question['image_link']}}" alt="Loading image...">
                    </div>
                </div>
            @endif
            <div
                class="panel-body-left @if($progress->phase == Config::get('defines.QUIZ_INITIAL_PHASE')){{'full'}}@endif">

                @if($progress->phase == Config::get('defines.QUIZ_REVISION_PHASE'))

                    <div class="revision-metrics-section">
                        <div class="responses-headers">


                            <div class="question-responses">Class (%)</div>
                            @if($conf_question !== null && in_array($question['id'],$conf_question->feedback,false))

                                <div class="confidence-responses">Confidence</div>
                            @endif
                            @if($prep_question !== null)
                                <div class="preparation-responses">Preparation</div>
                            @endif
                            @if(is_array($other_questions))
                                @foreach($other_questions as $other_question)
                                    @if($other_question->structure < 3)
                                        @if(($other_question->other_question_position == 2 && in_array($question['id'],$other_question->positions)))
                                            <div
                                                class="other-responses">{{shortenedString($other_question->name,16)}}</div>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <div class="responses">
                            @foreach($question['answers'] as $answer)
                                <div class="choice-div" data-choice-id="{{$answer['id']}}">

                                    <div
                                        class="initial-phase-response">{{number_format($answer['responses_percentage'], 2, '.', '')}}
                                        %
                                    </div>

                                    @if($conf_question !== null && in_array($question['id'],$conf_question->feedback))
                                        @if(isset($conf_question->responses[$loop->index+1][0]))
                                            <div
                                                class="initial-phase-conf-response">{{number_format($conf_question->responses[$loop->index+1][0], 2, '.', '')}}</div>
                                        @else
                                            <div
                                                class="initial-phase-conf-response">{{number_format(0, 2, '.', '')}}</div>
                                        @endif
                                    @endif

                                    @if( $prep_question !== null)
                                        @if(isset($prep_question->responses[$loop->index+1][0]))
                                            <div
                                                class="initial-phase-prep-response">{{number_format($prep_question->responses[$loop->index+1][0], 2, '.', '')}}</div>
                                        @else
                                            <div
                                                class="initial-phase-prep-response">{{number_format(0, 2, '.', '')}}</div>

                                        @endif
                                    @endif
                                    @foreach($other_questions as $other_question)
                                        @if($other_question->structure < 3)
                                            @if(($other_question->other_question_position == 2 && in_array($question['id'],$other_question->positions)))

                                                @if(isset($other_question->responses[$loop->parent->index+1][0]))
                                                    <div
                                                        class="initial-phase-other-response">{{number_format($other_question->responses[$loop->parent->index+1][0], 2, '.', '')}}</div>
                                                @else
                                                    <div
                                                        class="initial-phase-other-response">{{number_format(0, 2, '.', '')}}</div>

                                                @endif

                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach


                        </div>
                    </div>

                    <div class="headers-row">
                        <div class="answers-header">Answers</div>


                    </div>
                @endif
                <div class="question-choices"
                    {{--                     @if($progress->phase == Config::get('defines.QUIZ_INITIAL_PHASE'))style="width:619px"@endif--}}
                >

                    @foreach($question['answers'] as $answer)
                        <div class="choice-div" data-choice-id="{{$answer['id']}}">
                            <label>
                                <input class="with-gap" name="question_answer" required
                                       type="radio"
                                       value="{{$answer['id']}}"
                                @if($previous_answers!== null)

                                    @if($loop->index+1 == $previous_answers->response){{'checked'}}@endif
                                    @endif
                                />
                                <span>{{strtoupper(chr(64 + $loop->index+1)).' : '.$answer['text']}}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                @if($progress->phase == Config::get('defines.QUIZ_REVISION_PHASE'))

                    <div class="explanations-section">
                        <div class="question-explanation">
                            <span class="title">Class:</span> The percentage of students in the class that selected each option.</div>
                        @if($conf_question !== null && in_array($question['id'],$conf_question->positions))
                            <div class="question-explanation"><span class="title">Confidence :</span>{{$conf_question->explanation}}</div>
                        @endif
                        @if($prep_question !== null)
                            <div class="question-explanation"><span class="title">Preparation :</span>The average preparation score of participants that selected each option.</div>
                        @endif
                        @foreach($other_questions as $other_question)

                            @if($other_question->structure == 2)
                                @if(($other_question->other_question_position == 2 && in_array($question['id'],$other_question->positions)))

                                    <div class="question-explanation"><span
                                            class="title">{{$other_question->name}} :</span>
                                        {{$other_question->explanation}}</div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                @endif
                @if($progress->phase == Config::get('defines.QUIZ_REVISION_PHASE'))
                    @if($conf_question !== null && in_array($question['id'],$conf_question->positions))
                        <input type="hidden" name="conf_question_id"
                               value="{{$conf_question->id}}">

                        <div class="conf-quiz-section">
                            <div class="question-name">Confidence</div>
                            <div class="question-title">{{$conf_question->text}}</div>


                            <div class="conf-question-choices">
                                @foreach($conf_question->answers as $answer)
                                    <div class="choice-div"
                                         data-choice-id="{{$answer['id']}}">
                                        <label>
                                            <input class="with-gap" required
                                                   name="conf_question_answer"
                                                   type="radio"
                                                   value="{{$answer['id']}}"
                                            @if($previous_answers!== null)
                                                @foreach($previous_answers->acc_responses as $acc_response)
                                                    @if($acc_response->acc_question_id == $conf_question->id)
                                                        @if($loop->parent->index+1 == $acc_response->acc_question_response){{'checked'}}@endif
                                                        @endif
                                                    @endforeach
                                                @endif
                                            />
                                            <span>{{$answer['text']}}</span>
                                        </label>

                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @endif
                @endif
                @if($progress->phase == Config::get('defines.QUIZ_INITIAL_PHASE'))
                    {{--for old quizzes--}}
                    @php
                        if(isset($conf_question) && !is_array($conf_question->feedback)) {
                        $conf_question->feedback = explode(',',$conf_question->feedback);
                        }
                    @endphp

                    @if($conf_question !== null && in_array($question['id'],$conf_question->feedback))
                        <input type="hidden" name="conf_question_id"
                               value="{{$conf_question->id}}">

                        <div class="conf-quiz-section">
                            <div class="question-name">Confidence</div>
                            <div class="question-title">{{$conf_question->text}}</div>


                            <div class="conf-question-choices">
                                @foreach($conf_question->answers as $answer)
                                    <div class="choice-div"
                                         data-choice-id="{{$answer['id']}}">
                                        <label>
                                            <input class="with-gap" required
                                                   name="conf_question_answer"
                                                   type="radio"
                                                   value="{{$answer['id']}}"
                                            @if($previous_answers!== null)
                                                @if($loop->index+1 == $previous_answers->conf_response){{'checked'}}@endif
                                                @endif

                                            />
                                            <span>{{$answer['text']}}</span>
                                        </label>

                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @endif
                @endif
                @if($just_question !== null && $progress->phase == Config::get('defines.QUIZ_INITIAL_PHASE') && in_array($question['id'],$just_question->feedback))

                    <div class="justification-section just-init-phase">
                        <div class="question-name">Justifications</div>
                        <input type="hidden" name="just_question_id"
                               value="{{$just_question->id}}">
                        <div class="question-title">{{$just_question->text}}</div>
                        <div class="just-question-choices">
                            <div class="input-counter-div">
                                <input class="answer-justification" required maxlength="140"
                                       name="justification_answer"/>
                                <span class="input-counter">0/140</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @if($progress->phase == Config::get('defines.QUIZ_REVISION_PHASE'))
                <div class="panel-body-right">

                    @if($just_question !== null && in_array($question['id'],$just_question->positions))

                        <div class="justification-section just-rev-phase"
                             data-just-question-answers=" {{json_encode($just_question->responses )}}">
                            <div class="question-name">Justifications</div>
                            <input type="hidden" name="just_question_id"
                                   value="{{$just_question->id}}">
                            <div class="justif-answers-row">
                                @foreach($question['answers'] as $index => $answer)

                                    <span
                                        class="answer-letter @if($previous_answers !== null && $index+1 ==$previous_answers->response){{"selected"}}@endif"
                                        data-index="{{$index}}">{{strtoupper(chr(64 + $index+1))}}</span>
                                @endforeach
                            </div>

                            <div class="just-question-answers">


                                @foreach($just_question->responses as $response)
                                    @if($response->question_id == $question['id'] && $previous_answers !== null && $response->answer_index == $previous_answers->response)
                                        <div class="just-answer-row">{{$response->answer_content}}</div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    {{--revision phase--}}
                    @if(is_array($other_questions))
                        @foreach($other_questions as $other_question)
                            @if($other_question->structure == 3 && $other_question->other_question_position == 2 && in_array($question['id'],$other_question->positions ))
                                {{--show responses for other questions that are text based--}}
                                <div class="other-question-responses-section"
                                     data-other-question-answers=" {{json_encode($other_question->responses )}}">
                                    <div class="other-question-header">{{$other_question->name}}
                                        answers
                                    </div>
                                    <div class="other-questions-answers-row">
                                        @foreach($question['answers'] as $index => $answer)

                                            <span
                                                class="answer-letter @if($previous_answers !== null && $index+1 ==$previous_answers->response){{"selected"}}@endif"
                                                data-index="{{$index}}">{{strtoupper(chr(64 + $index+1))}}</span>
                                        @endforeach
                                    </div>
                                    <div class="other-questions-answers">
                                        @foreach($other_question->responses as $response)
                                            @if($response->question_id == $question['id'] &&$previous_answers !== null &&  $response->answer_index == $previous_answers->response)
                                                <div
                                                    class="other-questions-answer-row">{{$response->answer_content}}</div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            @endif
            @if($progress->phase == Config::get('defines.QUIZ_REVISION_PHASE'))
                {{--revision phase--}}
                @if(is_array($other_questions))
                    @foreach($other_questions as $other_question)
                        @if($other_question->structure == 2 && in_array($question['id'],$other_question->positions ) && $other_question->aq_rev_ph_display == 1)
                            {{--display radio buttons for the other question rating based that needs to be displayed in the revision phase --}}
                            <div class="other-question-section">
                                <input type="hidden" name="other_question_ids[]"
                                       value="{{$other_question->id}}">
                                <div class="other-question-header">{{$other_question->name}}
                                </div>
                                <div class="question-title">{{$other_question->text}}</div>


                                <div class="other-question-choices">
                                    @foreach($other_question->answers as $answer)
                                        <div class="choice-div"
                                             data-choice-id="{{$answer['id']}}">
                                            <label>
                                                <input class="with-gap" required
                                                       name="other_question_answers_content_{{$other_question->id}}"
                                                       type="radio"
                                                       value="{{$answer['id']}}"
                                                @if($previous_answers!== null)
                                                    @foreach($previous_answers->acc_responses as $acc_response)
                                                        @if($acc_response->acc_question_id == $other_question->id)
                                                            @if($loop->parent->index+1 == $acc_response->acc_question_response){{'checked'}}@endif
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                />
                                                <span>{{$answer['text']}}</span>
                                            </label>

                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            @else
                {{--initial phase--}}

                @if(is_array($other_questions))
                    @foreach($other_questions as $other_question)
                        @if($other_question->other_question_position == 2 && in_array($question['id'],$other_question->feedback))
                            <div class="other-question-section">
                                <input type="hidden" name="other_question_ids[]"
                                       value="{{$other_question->id}}">

                                @if($other_question->structure == 2)
                                    <div class="question-name">{{$other_question->name}}</div>

                                    <div class="question-title">{{$other_question->text}}</div>


                                    <div class="other-question-choices">
                                        @foreach($other_question->answers as $answer)
                                            <div class="choice-div"
                                                 data-choice-id="{{$answer['id']}}">
                                                <label>
                                                    <input class="with-gap" required
                                                           name="other_question_answers_content_{{$other_question->id}}"
                                                           type="radio"
                                                           value="{{$answer['id']}}"/>
                                                    <span>{{$answer['text']}}</span>
                                                </label>

                                            </div>
                                        @endforeach
                                    </div>

                                @elseif($other_question->structure == 3)
                                    <div class="question-name">{{$other_question->name}}</div>

                                    <div class="question-title">{{$other_question->text}}</div>

                                    <div class="other-question-choices">

                                        <div class="input-counter-div">
                                            <input class="answer-other-question" required
                                                   maxlength="140"
                                                   name="other_question_answers_content_{{$other_question->id}}"/>
                                            <span class="input-counter">0/140</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                @endif
            @endif
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn submit-answer">next question</button>
        </div>

    </div>

</div>
