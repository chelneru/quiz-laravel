<div id="delete-account-modal" class="modal">
    <div class="modal-content">
        <div class="popup-message">@lang('profile.profile-confirm-delete-account')</div>
        <form method="POST" class="delete-account-form" action="{{ route('delete-account') }}">
            @csrf
            <div class="popup-footer">

                <button class="btn grey cancel-button modal-close" type="button">@lang('profile.cancel-button')</button>
                <button class="btn red delete-button" type="submit">@lang('profile.confirm-account-deletion')</button>
            </div>

        </form>
    </div>
</div>
