<div class="default-panel acc-question-panel z-depth-2">
    <div class="panel-header"></div>
    <div class="panel-body">

        <div class="header-row">
            <div class="main-question-title" >{{$question['text']}}</div>
        </div>
        <div class="acc-explanations-section">
            <div class="question-explanation">
                {{$question['explanation']}}
            </div>
        </div>
        <form id="question-form" method="post" action="/quiz/submit-answer">
            <input type="hidden" name="acc_question_id" value="{{$question['id']}}">

            <div class="question-choices" style="margin-top: 10px;width:509px">
                @csrf
                <input type="hidden" name="quiz_id" value="{{$progress->quiz_id}}">
                @if($question['structure'] == 2)
                    @foreach($question['answers'] as $answer)
                        <div class="choice-row" data-choice-id="{{$answer['id']}}">
                            @if($question['structure'] == 2)
                                <label>
                                    <input class="with-gap" name="acc_question_answer_id" required
                                           type="radio"
                                           value="{{$answer['id']}}"/>
                                    <span>{{$answer['text']}}</span>
                                </label>

                            @endif
                        </div>
                    @endforeach
                @elseif($question['structure'] == 3)
                    <div class="input-counter-div">
                        <input class="answer-other-question" required
                               maxlength="140"
                               placeholder="Your answer here"
                               name="acc_question_answer"/>
                        <span class="input-counter">0/140</span>
                    </div>
                    </label>
                @endif
            </div>
            <div class="panel-footer">
                <button type="submit" class="btn submit-answer">next question</button>
            </div>
        </form>

    </div>

</div>