<div class="popup-overlay"></div>
<!-- Modal Structure -->
<div id="add_existing_quiz_modal" class="modal">
    <i class="material-icons right modal-close">close</i>

    <div class="modal-content">
        <form class="existing-quizzes-form" method="POST" action="{{ route('class-add-quizzes-action') }}">
            @csrf
            <input type="hidden" name="class_id" value="{{$class_id}}">

            <table class="available-quizzes-table">
                <tbody></tbody>
            </table>

            <div class="panel-footer sticky-modal-footer">
                <button class="btn add-quizzes-btn disabled" type="submit">add quizzes to the class</button>
                <button class="btn grey modal-close" type="button">cancel</button>
            </div>
        </form>
    </div>

</div>
<script src="{{ asset('js/add_quizzes_modal.js',config('app.secure', null)) }}"></script>

