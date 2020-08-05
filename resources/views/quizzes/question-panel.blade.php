@if(isset($quiz->questions) && is_array($quiz->questions->toArray()))
    @foreach($quiz->questions as $question)

        <div id="qpanel{{$question_counter}}" class="question-panel" question_id="{{$question->id}}">
            <div class="question-drag-handle">
                <span class="counter">Question {{$loop->index+1}}</span><i class="material-icons">drag_handle</i></div>
            <div class="input-counter-div">
                <input placeholder="Enter question title here" type="text"
                       class="question-title" maxlength="255" value="{{$question->question_text}}">
                <span class="input-counter">0/255</span>
            </div>
            <div class="input-counter-div image-link-div">
                <input placeholder="Enter image link here" type="text"
                       class="question-image-link" maxlength="255" value="{{$question->image_link}}">
                <span class="input-counter">0/255</span>
            </div>
            <span class="right" style="width: 120px">OneDrive links are not supported</span>
            <a class="image-link-preview teal-text text-lighten-1 btn-flat" href="#image-preview-modal">preview image</a>
            <div class="answers-headers">
                <div class="correct-answer-header">CORRECT</div>
                <div class="remove-answer-header">REMOVE</div>
            </div>
            <div class="answers-container">

                @foreach($question->question_answers as $answer)
                    <div class="answer-row row" id="{{$answer->id}}">
                        <i class="material-icons answer-drag-handle">drag_handle</i>

                        <div class="input-field input-counter-div">
                            <input type="text" class="answer-text" maxlength="255" value="{{$answer->answer_text}}">
                            <span class="input-counter">0/255</span>
                        </div>
                        <div class="answer-options-row">

                            <i class="material-icons delete-answer-icon">close</i>

                            <div class="correct-answer-icon">
                                <label>
                                    <input name="correct_answer{{$question_counter}}"
                                           type="radio" @if($loop->index+1 == $question->question_correct_answer){{'checked'}}@endif/>
                                    <span></span>
                                </label>
                            </div>

                        </div>
                    </div>

                @endforeach


                <div class="new-answer-row">
                    <i class="small material-icons teal-text text-lighten-1 tooltipped"
                       @if(count($question->question_answers) == 10)
                           style="visibility: hidden"
                           @endif
                       data-position="top"
                       data-tooltip="Add new answer">add</i>
                </div>
            </div>
            <div class="question-panel-actions">
                <div class="panel-actions-buttons">
                    <i class="material-icons copy-question-icon tooltipped" data-position="top"
                       data-tooltip="Copy question">content_copy</i>
                    <i class="material-icons delete-question-icon tooltipped" data-position="top"
                       data-tooltip="Delete question"
                       @if(count($quiz->questions) == 0)style="visibility: hidden"@endif>delete</i>
                </div>
            </div>

        </div>


        @php
            $question_counter++;
        @endphp

    @endforeach
@endif

@if(!isset($quiz->id) && $question_counter ==0)
    <div id="qpanel{{$question_counter}}" class="question-panel">
        <div class="question-drag-handle">
            <span class="counter">Question {{$question_counter+1}}</span>
            <i class="material-icons">drag_handle</i></div>
        <div class="input-counter-div">
        <input placeholder="Enter question title here" type="text"
               class="question-title" maxlength="255" value="">
            <span class="input-counter">0/255</span>
        </div>
        <div class="input-counter-div image-link-div">
            <input placeholder="Enter image link here" type="text"
                   class="question-image-link" maxlength="255" value="">
            <span class="input-counter">0/255</span>
        </div>
        <span class="right" style="width: 120px">OneDrive links are not supported</span>

        <a class="image-link-preview teal-text text-lighten-1   btn-flat" href="#image-preview-modal">preview image</a>

        <div class="answers-headers">
            <div class="correct-answer-header">CORRECT</div>
            <div class="remove-answer-header">REMOVE</div>
        </div>
        <div class="answers-container">
            <div class="answer-row row">
                <i class="material-icons answer-drag-handle">drag_handle</i>

                <div class="input-field input-counter-div">
                    <input type="text" class="answer-text" maxlength="255" value="Option 1">
                    <span class="input-counter"></span>
                </div>
                <div class="answer-options-row">
                    <i class="material-icons delete-answer-icon">close</i>

                    <div class="correct-answer-icon">
                        <label>
                            <input name="correct_answer{{$question_counter}}" type="radio"/>
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="new-answer-row">
                <i class="small material-icons teal-text text-lighten-1 tooltipped" data-position="top"
                   data-tooltip="Add new answer">add</i>
            </div>
        </div>
        <div class="question-panel-actions">
            <div class="panel-actions-buttons">
                <i class="material-icons copy-question-icon tooltipped" data-position="top"
                   data-tooltip="Copy question">content_copy</i>

                <i class="material-icons delete-question-icon tooltipped" data-position="top"
                   data-tooltip="Delete question"
                   @if($question_counter == 0)style="visibility: hidden"@endif>delete</i>
            </div>
        </div>

    </div>
@endif
