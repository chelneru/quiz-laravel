<div id="quiz-edit-options-modal" class="modal">
    <div class="modal-content">
        <div>
            <div class="btn-flat">
                <a href="/quiz/edit-quiz/{{$quiz->id}}">Edit Quiz</a>
            </div>
        </div>
        <div>

            <div class="btn-flat">
                <a href="/quiz/accompanying-questions/{{$quiz->id}}">Edit Accompanying questions</a>
            </div>
        </div>
        <div>

            <div class="btn-flat">
                <a href="/quiz/additional-messages/{{$quiz->id}}">Edit Starting message</a>
            </div>
        </div>
        <div>

            <div class="btn-flat">
                <a href="/quiz/scheduling/{{$quiz->id}}">Edit Scheduling</a>
            </div>
        </div>
    </div>


</div>