$(document).ready(function () {
    console.log('working .');

    $('.register-tab').on('click', function () {
        $(this).siblings().removeClass('active');
        $(this).addClass('active');


        if ($(this).hasClass('teacher-reg')) {
            $('.teacher-information').css('display', 'block');
            $('.teacher-validate-text').css('visibility','visible');
            $('.responsible-text').text('I am solely responsible for the text I will submit on SAGA as class/quiz information, answers, and personal information.');
            $('#user_role').val(2);
        } else {
            $('.teacher-information').css('display', 'none');
            $('.responsible-text').text('I am solely responsible for the text I will submit on SAGA as answers and personal information.');
            $('.teacher-validate-text').css('visibility','hidden');

            $('#user_role').val(1);
        }
    });
    $('#password-confirm').on('focusout', function () {
        if ($(this).val().length > 0) {
            if ($(this).val() != $('#password').val()) {
                $(this).addClass('invalid-field');
                $('#password').addClass('invalid-field');
            } else {
                $(this).removeClass('invalid-field');
                $('#password').removeClass('invalid-field');
            }
        }
    });
    $('#password').on('focusout', function () {
        if ($('#password-confirm').val().length > 0) {
            if ($('#password-confirm').val() != $('#password').val()) {
                $('#password-confirm').addClass('invalid-field');
                $('#password').addClass('invalid-field');
            } else {
                $('#password-confirm').removeClass('invalid-field');
                $('#password').removeClass('invalid-field');
            }
        }
    });
    $('#password-confirm, #password').on('change, keyup', function () {
        $('#password-confirm').removeClass('invalid-field');
        $('#password').removeClass('invalid-field');
    });

    $('#register_form').on('submit', function () {
        return ValidateRegisterForm();
    });
});

function ValidateRegisterForm() {
    let is_valid = true;
    let message = '';
    var response = grecaptcha.getResponse();
    if(response.length == 0) {
        is_valid = false;
        message +="Captcha has not been completed.";
    }

    if(!$('input[name=privacy_policy]').is(':checked')) {
        is_valid = false;
        if(message.length > 1) {
            message+= "<br>";
        }
        message +="You must agree to the privacy policy.";

    }
    if(!$('input[name=responsibility]').is(':checked')) {
        is_valid = false;
        if(message.length > 1) {
            message+= "<br>";
        }
        message +="You must agree to the terms.";

    }
    if ($('#password-confirm').val() != $('#password').val()) {
        is_valid = false;
        $(this).addClass('invalid-field');
        $('#password').addClass('invalid-field');
    } else {
        $(this).removeClass('invalid-field');
        $('#password').removeClass('invalid-field');
    }
    if(is_valid === false ) {
        ShowGlobalMessage(message, 2);

    }
    return is_valid;
}
