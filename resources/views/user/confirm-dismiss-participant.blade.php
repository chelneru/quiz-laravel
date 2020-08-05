<div id="dismiss-participant-modal" class="modal">
    <div class="modal-content">
        <div class="single-class">
            Are you sure you want to dismiss <span class="user-name"></span> from <span class="class-name"></span> ?
        </div>
        <div class="multiple-class">
            Please select from which class you want to dismiss <span class="user-name"></span>.
        <div class="rows-container"></div>
        </div>
    </div>
    <div class="popup-footer">
        <form id="dismiss_participant_form" method="POST" action="{{ route('dismiss-participant') }}">
            @csrf
            <input type="hidden" name="user_id" value="">
            <button class="btn modal-close cancel-inactive-process grey" type="button">@lang('profile.cancel-button')</button>
            <button class="btn action-submit" type="submit">Dismiss participant</button>

        </form>
    </div>
</div>