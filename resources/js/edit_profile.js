$(document).ready(function () {

    $('#edit-profile').on('submit', function () {
        if (!ValidateForm()) {
            return false;
        }
    });
});

function ValidateForm() {
    let valid = true;
    let message = '';
    if ($('.new-password-field').val().length > 0) {

        if ($('.existing-password-field').length == 0) {
            $('.existing-password-field').addClass('invalid-field');
            message += 'Existing password field is empty.<br>';
            valid = false;
        } else {
            $('.existing-password-field').addClass('invalid-field');
        }
        if ($('.confirm-password-field').length == 0) {
            message += 'Confirm password field is empty.<br>';
            valid = false;
            $('.existing-password-field').addClass('invalid-field');
        } else if ($('.confirm-password-field').val() !== $('.new-password-field').val()) {
            message += 'Both new password field and confirm password field need to have the same value.<br>';
            valid = false;
            $('.existing-password-field').addClass('invalid-field');

        } else {
            $('.existing-password-field').removeClass('invalid-field');
        }
    }
    if (message.length > 0) {
        ShowGlobalMessage(message, 2);
    }
    return valid;
}