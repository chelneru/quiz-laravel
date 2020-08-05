<div class="popup-overlay"></div>
<div class="popup-panel">
    <div class="popup-message">@lang('profile.profile-confirm-delete-account')</div>
    <div class="popup-footer">
        <form method="POST" action="{{ route('delete-account') }}">
            @csrf

            <form action="#">

                <p>
                    <label>
                        <input type="checkbox" class="filled-in" checked="checked" name=""/>
                        <span>Filled in</span>
                    </label>
                </p>

            </form>
            <button class="popup-button default-button cancel-button" type="button">@lang('profile.cancel-button')</button>
            <button class="popup-button delete-button" type="submit">@lang('profile.confirm-account-deletion')</button>

        </form>
    </div>
</div>