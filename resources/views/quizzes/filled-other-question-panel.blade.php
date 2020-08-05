<div class="other-question-panel" id="{{$other_question->id}}">
    <div class="other-question-row">
        <label>
            <input type="checkbox" class="filled-in" checked name="enable_other_question"/>
            <span></span>
        </label>
        <div>
            <span>Addition question:</span>

            <input placeholder="other" type="text"
                   class="new-accompanying-question-name" value="{{$other_question->name}}">
        </div>
        <div class="remove-other-question tooltipped" data-position="top" data-tooltip="Remove the addition question">
            <i class="material-icons">close</i>
        </div>

    </div>
    <div id="qpanel{{$question_counter}}" class="question-panel other-filled">
        <div class="input-counter-div">
                <input placeholder="Enter question title here" type="text"
                       class="question-title" maxlength="100" value="{{$other_question->title}}">
            <span class="input-counter">0/100</span>
        </div>
        <div class="input-counter-div">

        <input placeholder="Enter the explanation for the metrics here" type="text"
                       class="question-explanation" maxlength="255" value="{{$other_question->explanation}}">
            <span class="input-counter">0/255</span>
        </div>
        <div class="question-position-row">
            <div class="inline position-text">Display this question</div>
            <div class="position-select-div inline">
                <select class="question-position" name="question_location">
                    <option value="1" @if($other_question->position == 1){{'selected'}}@endif>before</option>
                    <option value="2" @if($other_question->position == 2){{'selected'}}@endif>inside</option>
                    <option value="3" @if($other_question->position == 3){{'selected'}}@endif>after</option>
                </select>
            </div>
            <div class="inline position-text-after">the quiz</div>

        </div>
        <div class="question-inside-phase-display" @if($other_question->position == 2)style="display: block;" @endif>
            <div>
                <label>
                    <input type="checkbox" class="filled-in"
                           @if($other_question->init_display == 1){{'checked'}}@endif name="question_initial_phase_display"/>
                    <span>Question available for answering in the initial phase</span>

                </label>
            </div>
            <div>

                <label class="hide">
                    <input type="checkbox" class="filled-in" name="question_revision_phase_display"/>
                    <span>Question available for answering in the revision phase</span>

                </label>
            </div>
        </div>
        <div class="row question-type-row">
            <div class="input-field s3">
                <select class="question-type-dropdown">
                    <option value="2" @if($other_question->structure == 2){{"selected"}}@endif>Rating question</option>
                    <option value="3" @if($other_question->structure == 3 && $other_question->position == 2){{"selected"}}@endif @if($other_question->position != 2){{'disabled'}}@endif >Text field question
                    </option>
                </select>
                <label>Question type</label>
            </div>
        </div>


        <div class="rating-question" @if($other_question->structure == 2)style="display: block;"@endif>

            <div class="answers-container">
                @foreach($other_question->options as $answer)
                <div class="answer-row row">

                    <div class="input-field input-counter-div">
                        <input class="answer-text" type="number"  maxlength="3"  onkeyup="this.value=this.value.substring(0,3)"  value="{{$answer->text}}">
                        <span class="input-counter">{{strlen($answer->text)}}/3</span>
                    </div>

                    <i class="material-icons delete-answer-icon">close</i>
                </div>
@endforeach
                <div class="new-answer-row">
                    <i class="small material-icons teal-text text-lighten-1 tooltipped" data-position="top" data-tooltip="Add new answer">add</i>
                </div>
            </div>
            <div class="question-panel-actions">
                <div class="panel-actions-buttons">

                </div>
            </div>
        </div>
        <div class="text-field-question" @if($other_question->structure == 3)style="display: block;"@endif>
            <div class="row"><textarea class="s6" disabled style="resize: none;"
                                       placeholder="Participant's answer will be here"></textarea>
            </div>
        </div>

        <div class="questions-container-header" @if($other_question->position == 2)style="display: block;"@endif>
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
        <div class="questions-container" >


            @foreach($quiz->questions as $question)
                <div id="{{$question->id}}"
                     class="question-row">
                    <div class="question-text">@if(strlen($question->question_text) > 50){{substr($question->question_text,0,50).'...'}}
                        @else{{$question->question_text}}@endif</div>
                    <div class="feedback">
                        <label>
                            @if($other_question->init_display == 0)
                                <input type="checkbox" class="filled-in" disabled name="question_feedback"/>
                            @else
                            <input type="checkbox" class="filled-in"
                                   @if(in_array($question->id,$other_question->feedback)){{'checked'}}@endif name="question_feedback"/>
                            @endif
                            <span></span>
                        </label>

                        <label>
                            @if($other_question->init_display == 0)
                                <input type="checkbox" class="filled-in" disabled name="question_location"/>
                            @else
                            <input type="checkbox" class="filled-in"
                                   @if(in_array($question->id,$other_question->positions)){{'checked'}}@endif name="question_location"/>
                            @endif
                            <span></span>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
        </div>
    </div>
</div>
