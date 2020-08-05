<div id="delete-quiz-modal" class="modal">
    <div class="modal-content">
        <div class="single-quiz">
            @lang('admin-quizzes.confirm-delete-quiz') <span class="quiz-name"></span> ?
        </div>

    </div>
    <div class="popup-footer">
        <form id="delete_quiz_form" method="POST" action="{{ route('admin-delete-quiz') }}">
            @csrf
            <input type="hidden" name="quiz_id" value="">
            <button class="btn modal-close cancel-inactive-process grey" type="button">@lang('profile.cancel-button')</button>
            <button class="btn action-submit" type="submit">@lang('admin-quizzes.delete-quiz')</button>

        </form>
    </div>
</div>
