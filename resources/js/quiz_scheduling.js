let init_start_picker,
    init_end_picker,
    rev_start_picker,
    rev_end_picker,
    ans_start_picker,
    ans_end_picker;

$(document).ready(function () {

    let now = moment().toDate();
    $(".datepicker").each(function () {
        let $input = $(this);
        let container_id = $(this).parent().attr('id');
        let $container = $(this).parent();

        flatpickr($input, {
            "enableTime": true,
            "plugins": [new confirmDatePlugin({})],
            "appendTo": $($container).outerHTML,
            minDate: now,
            dateFormat: "d M H:i",
            onChange: function (selectedDates, dateStr, instance) {
                let $input = $(instance.input);
                //deal with the minimum date range for the 'end' pickers
                if ($($input).attr('name').indexOf('initial_phase_start') > -1) {
                    //set init end minimum date to the current date of init start
                    let init_end_picker = document.querySelector("input[name=initial_phase_end]")._flatpickr;
                    init_end_picker.set('minDate', moment(dateStr, 'DD MMM HH:mm').toDate());
                } else if ($($input).attr('name').indexOf('revision_phase_start') > -1) {
                    //set rev end minimum date to the current date of rev start
                    let rev_end_picker = document.querySelector("input[name=revision_phase_end]")._flatpickr;
                    rev_end_picker.set('minDate', moment(dateStr, 'DD MMM HH:mm').toDate());
                    //set init end date to the current date of rev start
                    let init_end_picker = document.querySelector("input[name=initial_phase_end]")._flatpickr;
                    init_end_picker.setDate(moment(dateStr, 'DD MMM HH:mm').toDate());
                } else if ($($input).attr('name').indexOf('reveal_answers_start') > -1) {
                    //set ans end minimum date to the current date of ans start
                    let reveal_end_picker = document.querySelector('input[name=reveal_answers_end]')._flatpickr;
                    reveal_end_picker.set('minDate', moment(dateStr, 'DD MMM HH:mm').toDate());
                    //set rev end date to the current date of ans start
                    let rev_end_picker = document.querySelector("input[name=revision_phase_end]")._flatpickr;
                    rev_end_picker.setDate(moment(dateStr, 'DD MMM HH:mm').toDate());
                }


                if ($($input).attr('name').indexOf('initial_phase_end') > -1) {
                    //set rev start date to the current date of init end
                    let rev_start_picker = document.querySelector("input[name=revision_phase_start]")._flatpickr;
                    rev_start_picker.set('minDate', moment(dateStr, 'DD MMM HH:mm').toDate());
                    rev_start_picker.setDate(moment(dateStr, 'DD MMM HH:mm').toDate());
                    //set min date for rev end
                    let rev_end_picker = document.querySelector("input[name=revision_phase_end]")._flatpickr;
                    rev_end_picker.set('minDate', moment(rev_start_picker.selectedDates[0], 'DD MMM HH:mm').toDate());
                } else if ($($input).attr('name').indexOf('revision_phase_end') > -1) {
                    let reveal_start_picker = document.querySelector("input[name=reveal_answers_start]")._flatpickr;
                    reveal_start_picker.set('minDate', moment(dateStr, 'DD MMM HH:mm').toDate());
                    reveal_start_picker.setDate(moment(dateStr, 'DD MMM HH:mm').toDate());
                    //set min date for reveal ans end
                    let reveal_end_picker = document.querySelector("input[name=reveal_answers_end]")._flatpickr;
                    reveal_end_picker.set('minDate', moment(reveal_start_picker.selectedDates[0], 'DD MMM HH:mm').toDate());

                }

            }
        });
    });

    $('input[type=radio][name=quiz_participation_count]').change(function () {
        if (this.value == 3) {
            $('.participation-input-div input').attr('disabled', false);
        } else {
            $('.quiz_participation_input').removeClass('invalid-field');
            $('.participation-input-div input').attr('disabled', true);

        }
    });

    $('input[type=radio][name=quiz_participation_count]').change(function () {
        if (this.value == 2) {

            $('.participation-input-div input').attr('disabled', false);
        } else {
            $('.participation-input-div input').attr('disabled', true);

        }
    });

    $('input[type=radio][name=quiz_availability]').change(function () {
        if (this.value == 2) {
            ManageDatePickerSection('enable');
        } else {
            ManageDatePickerSection('disable');
        }
        $("input[name='quiz_availability']").parent('label').removeClass('invalid-field');

    });
    $('#scheduling_form').on('submit', function () {
        let $form = $(this);

        init_start_picker = document.querySelector("input[name=initial_phase_start]")._flatpickr;
        init_end_picker = document.querySelector("input[name=initial_phase_end]")._flatpickr;
        rev_start_picker = document.querySelector("input[name=revision_phase_start]")._flatpickr;
        rev_end_picker = document.querySelector("input[name=revision_phase_end]")._flatpickr;
        ans_start_picker = document.querySelector("input[name=reveal_answers_start]")._flatpickr;
        ans_end_picker = document.querySelector("input[name=reveal_answers_end]")._flatpickr;

        $('.datepicker').attr('disabled', true);
        if (ValidateForm()) {
            //add the correct date format as fields for the form
            $($form).append("<input type='text' class='temp-input' name='initial_phase_start' value='" + moment(init_start_picker.selectedDates[0]).format('YYYY-MM-DD h:mm:ss') + "'/>");
            $($form).append("<input type='text' class='temp-input' name='initial_phase_end' value='" + moment(init_end_picker.selectedDates[0]).format('YYYY-MM-DD h:mm:ss') + "'/>");

            $($form).append("<input type='text' class='temp-input' name='revision_phase_start' value='" + moment(rev_start_picker.selectedDates[0]).format('YYYY-MM-DD h:mm:ss') + "'/>");
            $($form).append("<input type='text' class='temp-input' name='revision_phase_end' value='" + moment(rev_end_picker.selectedDates[0]).format('YYYY-MM-DD h:mm:ss') + "'/>");

            $($form).append("<input type='text' class='temp-input' name='reveal_answers_start' value='" + moment(ans_start_picker.selectedDates[0]).format('YYYY-MM-DD h:mm:ss') + "'/>");
            $($form).append("<input type='text' class='temp-input' name='reveal_answers_end' value='" + moment(ans_end_picker.selectedDates[0]).format('YYYY-MM-DD h:mm:ss') + "'/>");
            return true;
        }
        $('.datepicker').attr('disabled', false);

        return false;
    });
    $('.update-scheduling').on('click', function () {
        $('#scheduling_form').submit();
    })
});


function ManageDatePickerSection(status) {
    let $section = $('.quiz-scheduling-section');

    if (status === 'enable') {
        $($section).find('.datepicker').attr('disabled', false);
        $($section).find('.datepicker-icon').addClass('black-text');
        $($section).find('.datepicker-icon').removeClass('grey-text');

    } else if (status === 'disable') {
        $($section).find('.datepicker').attr('disabled', true);
        $($section).find('.datepicker-icon').addClass('grey-text');
        $($section).find('.datepicker-icon').removeClass('black-text');
    }
}

function ValidateForm() {
    let is_valid = true;
    let message = '';
    //check if any option for participation count has been selected
    if ($("input[name='quiz_participation_count']:checked").length == 0) {
        is_valid = false;
        message += 'You need to select how many times a participant can take the quiz.<br>';
        $("input[name='quiz_participation_count']").parent('label').addClass('invalid-field');
    } else {
        $("input[name='quiz_participation_count']").parent('label').removeClass('invalid-field');
    }
    //if the teacher selected the specific times option check if he inserted a valid number
    if ($("input[name='quiz_participation_count']:checked").val() == 2 && ($('.quiz_participation_input').val() === '' || $('.quiz_participation_input').val() <= 0)) {
        is_valid = false;
        message += 'You need to mention a specific number of times a participant can take the quiz.<br>';
        $('.quiz_participation_input').addClass('invalid-field');
    } else {
        $('.quiz_participation_input').removeClass('invalid-field');
    }


    //validate the quiz availability section
    if ($("input[name='quiz_availability']:checked").length == 0) {
        is_valid = false;
        message += 'You need to specify when the quiz will be available.<br>';
        $("input[name='quiz_availability']").parent('label').addClass('invalid-field');
    }
    //inside the quiz availability section validate the date pickers
    if ($("input[name='quiz_availability']:checked").val() == 2) {


        if ($(init_start_picker.element).val() === '') {
            message += 'You need to select a valid date for initial phase start.<br>';
            $(init_start_picker.element).addClass('invalid-field');
            is_valid = false;
        } else if (moment($(init_start_picker.element).val(), 'DD MMM HH:mm').isBefore(moment())) {
            message += 'You need to select a date in the future for initial phase start.<br>';
            $(init_start_picker.element).addClass('invalid-field');
            is_valid = false;
        } else {
            $(init_start_picker.element).removeClass('invalid-field');
        }
        if ($(init_end_picker.element).val() === '') {
            message += 'You need to select a valid date for initial phase end.<br>';
            $(init_end_picker.element).addClass('invalid-field');
            is_valid = false;
        } else if (moment($(init_end_picker.element).val(), 'DD MMM HH:mm').isBefore(moment())) {
            message += 'You need to select a date in the future for initial phase end.<br>';
            $(init_end_picker.element).addClass('invalid-field');
            is_valid = false;
        } else {
            $(init_end_picker.element).removeClass('invalid-field');
        }
        if ($(rev_start_picker.element).val() === '') {
            message += 'You need to select a valid date for revision phase start.<br>';
            $(rev_start_picker.element).addClass('invalid-field');
            is_valid = false;
        } else if (moment($(rev_start_picker.element).val(), 'DD MMM HH:mm').isBefore(moment())) {
            message += 'You need to select a date in the future for revision phase start.<br>';
            $(rev_start_picker.element).addClass('invalid-field');
            is_valid = false;
        } else {
            $(rev_start_picker).removeClass('invalid-field');
        }
        if ($(rev_end_picker.element).val() === '') {
            message += 'You need to select a valid date for revision phase end.<br>';
            $(rev_end_picker.element).addClass('invalid-field');
            is_valid = false;
        } else if (moment($(rev_end_picker.element).val(), 'DD MMM HH:mm').isBefore(moment())) {
            message += 'You need to select a date in the future for revision phase end.<br>';
            $(rev_end_picker.element).addClass('invalid-field');
            is_valid = false;
        } else {
            $(rev_end_picker.element).removeClass('invalid-field');

        }
        if ($(ans_start_picker.element).val() === '') {
            message += 'You need to select a valid date for answers reveal phase start.<br>';
            $(ans_start_picker.element).addClass('invalid-field');
            is_valid = false;
        } else if (moment($(ans_start_picker.element).val(), 'DD MMM HH:mm').isBefore(moment())) {
            message += 'You need to select a date in the future for answers reveal phase start.<br>';
            $(ans_start_picker.element).addClass('invalid-field');
            is_valid = false;
        } else {
            $(ans_start_picker.element).removeClass('invalid-field');
        }
        if ($(ans_end_picker.element).val() === '') {
            message += 'You need to select a valid date for answers reveal phase end.<br>';

            $(ans_end_picker.element).addClass('invalid-field');
            is_valid = false;
        } else if (moment($(ans_end_picker.element).val(), 'DD MMM HH:mm').isBefore(moment())) {
            message += 'You need to select a date in the future for answers reveal phase start.<br>';
            $(ans_end_picker.element).addClass('invalid-field');
            is_valid = false;
        } else {
            $(ans_end_picker.element).removeClass('invalid-field');
        }
        if (is_valid === true) {
            //we have valid dates so we need to check the chronological order
            //check init start and init end
            if (moment(init_start_picker.selectedDates[0]).isBefore(moment(init_end_picker.selectedDates[0])) === false) {
                is_valid = false;
                message += 'Initial phase start date needs to be before the initial phase end date.<br>';
                $(init_start_picker.element).addClass('invalid-field');
                $(init_end_picker.element).addClass('invalid-field');
            } else {
                $(init_start_picker.element).removeClass('invalid-field');
                $(init_end_picker.element).removeClass('invalid-field');
            }
            //check init end and rev start
            if (moment(init_end_picker.selectedDates[0]).isSame(moment(rev_start_picker.selectedDates[0]), 'minute') === false) {
                is_valid = false;
                message += 'Initial phase end date needs to be the same as the revision phase start date.<br>';
                $(init_end_picker.element).addClass('invalid-field');
                $(rev_start_picker.element).addClass('invalid-field');
            } else {
                $(init_end_picker.element).removeClass('invalid-field');
                $(rev_start_picker.element).removeClass('invalid-field');
            }
            //check rev start and rev end
            if (moment(rev_start_picker.selectedDates[0]).isBefore(moment(rev_end_picker.selectedDates[0])) === false) {
                is_valid = false;
                message += 'Revision phase start date needs to be before the revision phase end date.<br>';
                $(rev_start_picker.element).addClass('invalid-field');
                $(rev_end_picker.element).addClass('invalid-field');
            } else {
                $(rev_start_picker.element).removeClass('invalid-field');
                $(rev_end_picker.element).removeClass('invalid-field');
            }
            //check rev end and ans start
            if (moment(rev_end_picker.selectedDates[0]).isSame(moment(ans_start_picker.selectedDates[0]), 'minute') === false) {
                is_valid = false;
                message += 'Revision phase end date needs to be the same as the Answers reveal phase start date.<br>';
                $(rev_end_picker.element).addClass('invalid-field');
                $(ans_start_picker.element).addClass('invalid-field');
            } else {
                $(rev_end_picker.element).removeClass('invalid-field');
                $(ans_start_picker.element).removeClass('invalid-field');
            }
            //check ans start and ans end
            if (moment(ans_start_picker.selectedDates[0]).isBefore(moment(ans_end_picker.selectedDates[0])) === false) {
                is_valid = false;
                message += 'Answers reveal phase start date needs to be before the Answers reveal phase end date.<br>';
                $(ans_start_picker.element).addClass('invalid-field');
                $(ans_end_picker.element).addClass('invalid-field');
            } else {
                $(ans_start_picker.element).removeClass('invalid-field');
                $(ans_end_picker.element).removeClass('invalid-field');
            }

        }
    }

    if (message.length > 0) {
        ShowGlobalMessage(message, 2);
    }
    return is_valid;
}