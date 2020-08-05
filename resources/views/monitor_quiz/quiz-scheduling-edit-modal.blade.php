<div id="edit-quiz-scheduling-modal" class="modal">
    <i class="material-icons right modal-close">close</i>

    <div class="modal-content">
        Extend current phase by <div class="select-field"><select class="extension-amount-select" name="extension-amount-select">
                <option value="" disabled selected>select amount</option>
                <option value="5" >5 minutes</option>
                <option value="10" >10 minutes</option>
                <option value="15" >15 minutes</option>
                <option value="30" >30 minutes</option>
                <option value="60" >60 minutes</option>
            </select></div>.
        <div style="display: inline-block;
    margin-left: 20px;">
            Disabled options means that the extension is past the starting time of the next phase. if you want to edit further the schedule of the quiz click <a href="/quiz/scheduling/{{$quiz->id}}">here</a>.
        </div>
    </div>

    <div class="modal-footer">

        <button class="btn confirm-btn" type="submit">extend phase</button>

    </div>
</div>