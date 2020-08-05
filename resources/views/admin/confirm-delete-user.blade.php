<div id="delete-user-modal" class="modal">
    <div class="modal-content">
        <div class="single-class">
            @lang('admin-users.confirm-delete-user') <span class="user-name"></span> ?
        </div>

    </div>
    <div class="popup-footer">
        <form id="delete_user_form" method="POST" action="{{ route('admin-delete-user') }}">
            @csrf
            <input type="hidden" name="user_id" value="">
            <button class="btn modal-close cancel-inactive-process grey" type="button">@lang('profile.cancel-button')</button>
            <button class="btn action-submit" type="submit">@lang('admin-users.delete-user')</button>

        </form>
    </div>
</div>
