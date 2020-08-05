<div id="reset-user-password-modal" class="modal">
    <div class="modal-content">
        <div class="single-class">
            @lang('admin-users.confirm-reset-password') <span class="user-name"></span> ?
        </div>
    </div>
    <div class="popup-footer">
        <form id="reset_password_form" method="POST" action="{{ route('admin-password-reset') }}">
            @csrf
            <input type="hidden" name="email" value="">
            <button class="btn modal-close cancel-inactive-process grey" type="button">@lang('profile.cancel-button')</button>
            <button class="btn action-submit" type="submit">@lang('admin-users.reset-password-btn')</button>

        </form>
    </div>
</div>
