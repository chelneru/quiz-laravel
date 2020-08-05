<div id="qpanel{{$question_counter}}" class="question-panel">
    <div class="input-counter-div">

        <input placeholder="Enter question title here." type="text"
               class="question-title" maxlength="255" value="{{$accompanying_questions->prep_question->title}}">
        <span class="input-counter">0/255</span>
    </div>
    <div class="input-counter-div">

        <input placeholder="Enter the explanation for the metrics here." type="text"
               class="question-explanation" maxlength="255"
               value="{{$accompanying_questions->prep_question->explanation}}">
        <span class="input-counter">0/255</span>
    </div>
    <div class="question-type-dropdown-row">
        <div class="input-field">
            <select class="question-type-dropdown">
                <option selected value="2">Rating question</option>
            </select>
            <label>Question type</label>
        </div>
    </div>
    <div class="multiple-choice-question">

        <div class="answers-container">
            @foreach($accompanying_questions->prep_question->options as $option)

                <div class="answer-row row">

                    <div class="input-field input-counter-div">
                        <input class="answer-text" type="number" maxlength="3" onkeyup="this.value=this.value.substring(0,3)"  data-option-id="{{$option->id}}"
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
</div>
