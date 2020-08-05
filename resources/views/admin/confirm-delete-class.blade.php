<div id="delete-class-modal" class="modal">
    <div class="modal-content">
        <div class="single-class">@lang('admin-classes.confirm-delete-class') <span class="class-name"></span> ?
        </div>

    </div>
    <div class="popup-footer">
        <form id="delete_class_form" method="POST" action="{{ route('admin-delete-class') }}">
            @csrf
            <input type="hidden" name="class_id" value="">
            <button class="btn modal-close cancel-inactive-process grey" type="button">@lang('profile.cancel-button')</button>
            <button class="btn action-submit" type="submit">@lang('admin-classes.delete-class')</button>

        </form>
    </div>
</div>
