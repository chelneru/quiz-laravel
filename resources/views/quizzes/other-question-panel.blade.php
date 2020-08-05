
<div class="other-question-row">
    <label>
        <input type="checkbox" class="filled-in" name="enable_other_question"/>
        <span></span>
    </label>
    <div>
        <span>Addition Question :</span>
        <div class="input-counter-div">
        <input placeholder="Enter addition question name here" maxlength="100" type="text"
               class="new-accompanying-question-name" value="">
            <span class="input-counter">0/100</span>

        </div>
    </div>
    <div class="remove-other-question tooltipped" data-position="top" data-tooltip="Remove the addition question">
        <i class="material-icons">close</i>
    </div>

</div>
<div id="qpanel{{$question_counter}}" class="question-panel">

    <div class="input-counter-div">
            <input placeholder="Enter question title here" maxlength="255" type="text"
                   class="question-title" value="">
        <span class="input-counter">0/255</span>

    </div>
    <div class="input-counter-div">
        <div>
            <input placeholder="Enter the explanation for the metrics here" maxlength="255" type="text"
                   class="question-explanation" value="">
            <span class="input-counter">0/255</span>

        </div>
    </div>
    <div class="question-position-row">
        <div class="inline position-text">Display this question</div>
        <div class="position-select-div inline">

        </div>
        <div class="inline position-text-after">the quiz</div>

    </div>
    <div class="question-inside-phase-display">
        <div>
            <label>
                <input type="checkbox" class="filled-in" checked name="question_initial_phase_display"/>
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
        <div class="input-field">
        <label>Question type</label>
        </div>
    </div>

    <div class="rating-question">

        <div class="answers-container">
            <div class="answer-row row">

                <div class="input-field input-counter-div">
                    <input type="number" class="answer-text" maxlength="3" onkeyup="this.value=this.value.substring(0,3)"  value="1">
                    <span class="input-counter">1/3</span>

                </div>

                <i class="material-icons delete-answer-icon">close</i>
            </div>

            <div class="new-answer-row">
                <i class="small material-icons teal-text text-lighten-1 tooltipped" data-position="top" data-tooltip="Add new answer">add</i>
            </div>
        </div>
        <div class="question-panel-actions">
            <div class="panel-actions-buttons">

            </div>
        </div>
    </div>
    <div class="text-field-question">
        <div class="row"><textarea class="col s6" disabled style="resize: none;"
                                   placeholder="Participant's answer will be here"></textarea>
        </div>
    </div>
    <div class="questions-container-header" style="display: none;">
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
    </div>
    </div>
</div>
