<div id="qpanel{{$question_counter}}" class="question-panel">

    <div>
        <div class="input-counter-div">
        <input placeholder="Enter question title here." type="text"
               class="question-title" maxlength="255" value="{{$accompanying_questions->conf_question->title}}">
            <span class="input-counter">0/255</span>
        </div>
        </div>

    <div>
        <div class="input-counter-div">

        <input placeholder="Enter the explanation for the metrics here." type="text"
               class="question-explanation" maxlength="255" value="{{$accompanying_questions->conf_question->explanation}}">
            <span class="input-counter">0/255</span>
        </div>
    </div>

    <divf class="input-field question-type-dropdown-row">
        <select class="question-type-dropdown">
            <option selected value="2">Rating question</option>
        </select>
        <label>Question type</label>

    </divf>
    <div class="multiple-choice-question">

        <div class="answers-container">
            @foreach($accompanying_questions->conf_question->options as $option)

                <div class="answer-row row">

                    <div class="input-field input-counter-div">
                        <input class="answer-text" type="number" maxlength="3" onkeyup="this.value=this.value.substring(0,3)" data-option-id="{{$option->id}}"
                               value="{{$option->text}}">
                        <span class="input-counter">{{strlen($option->text)}}/3</span>
                    </div>
                    <i class="material-icons delete-answer-icon">close</i>
                </div>
            @endforeach

            <div class="new-answer-row">
                <i class="small material-icons teal-text text-lighten-1 tooltipped" data-position="top"
                   data-tooltip="Add new answer">add</i>
            </div>
        </div>

    </div>
    <div class="text-field-question">
        <div class="row"><textarea class="col s6" disabled style="resize: none;"
                                   placeholder="Participant's answer will be here"></textarea>
        </div>
    </div>
    <div class="questions-container-header">
        Appearance - where/how do you want to use the accompanying question? <i
                class="material-icons small toggle-acc-use-position">unfold_more</i>
    </div>
    <div class="divider"></div>

    <div class="acc-question-location-feedback">

        <div class="question-panel-actions">
            <div class="panel-actions-buttons">
                <div>Question (Initial phase)</div>
                <div>Feedback (Revision phase)</div>
            </div>
        </div>
        <div class="questions-container">

            @if($accompanying_questions->conf_question->feedback === null && $accompanying_questions->conf_question->positions === null)
                {{--fill with the defaults (where all questions are checked)--}}
                @foreach($quiz->questions as $question)
                    <div id="{{$question->id}}"
                         class="question-row">
                        <div class="question-text">{{($loop->index+1).'. '}}@if(strlen($question->question_text) > 50){{substr($question->question_text,0,50).'...'}}
                            @else{{$question->question_text}}@endif</div>
                        <div class="feedback">
                            <label>
                                <input type="checkbox" class="filled-in" checked name="question_feedback"/>
                                <span></span>
                            </label>

                            <label>
                                <input type="checkbox" class="filled-in" checked name="question_location"/>
                                <span></span>
                            </label>
                        </div>
                    </div>
                @endforeach
            @else
                @foreach($quiz->questions as $question)
                    <div id="{{$question->id}}"
                         class="question-row">
                        <div class="question-text">{{($loop->index+1).'. '}}@if(strlen($question->question_text) > 50){{substr($question->question_text,0,50).'...'}}
                            @else{{$question->question_text}}@endif</div>
                        <div class="feedback">
                            <label>
                                <input type="checkbox" class="filled-in"
                                       @if(in_array($question->id,$accompanying_questions->conf_question->feedback)){{'checked'}}@endif name="question_feedback"/>
                                <span></span>
                            </label>

                            <label>
                                <input type="checkbox" class="filled-in"
                                       @if(in_array($question->id,$accompanying_questions->conf_question->positions)){{'checked'}}@endif name="question_location"/>
                                <span></span>
                            </label>
                        </div>
                    </div>
                @endforeach

            @endif
        </div>
    </div>
</div>
