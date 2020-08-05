$(document).ready(function () {
    $('.dropdown-trigger').dropdown({constrainWidth: false});

    console.log('page loaded');
    $('select').formSelect();
    $('.tooltipped').tooltip();

    $('select').on('contentChanged', function () {
        $(this).formSelect();
    });
    $('input[type=checkbox]').on('change', function () {
        if ($(this).is(':checked')) {
            $(this)
                .closest('.row')
                .next('.question_options')
                .slideDown("fast", function () {
                });
        } else {
            $(this)
                .closest('.row')
                .next('.question_options')
                .slideUp("fast", function () {
                });

        }
    });

    $(document).on('change', '.question-position-row .question-position', function () {
        let selected_type = $(this).find('option:selected').attr('value');



        if (selected_type == "2") {
            $(this).closest('.question-panel').find('.questions-container').css('display', 'block');
            $(this).closest('.question-panel').find('.question-inside-phase-display').css('display', 'block');
            $(this).closest('.question-panel').find('.questions-container-header').css('display', 'block');

            $(this).closest('.question-panel').find(".question-type-dropdown option").removeAttr("disabled");


        } else {
            $(this).closest('.question-panel').find('.questions-container').css('display', 'none');
            $(this).closest('.question-panel').find('.questions-container-header').css('display', 'none');
            $(this).closest('.question-panel').find('.question-inside-phase-display').css('display', 'none');

            $(this).closest('.question-panel').find(".question-type-dropdown option[value='3']")
                .prop("disabled", 'true')
                .siblings().removeAttr("disabled");
        }
        $(this).closest('.question-panel').find(".question-type-dropdown").formSelect();

    });

    $('.question-type-dropdown').on('change', function () {
        let selected_type = $(this).find('option:selected').attr('value');

        switch (selected_type) {
            case "1":
                $(this).closest('.question-panel').find('.multiple-choice-question').css('display', 'block');
                $(this).closest('.question-panel').find('.text-field-question').css('display', 'none');
                $(this).closest('.question-panel').find('.rating-question').css('display', 'none');

                break;
            case "2":
                $(this).closest('.question-panel').find('.multiple-choice-question').css('display', 'none');
                $(this).closest('.question-panel').find('.text-field-question').css('display', 'none');
                $(this).closest('.question-panel').find('.rating-question').css('display', 'block');
                break;

            case "3":
                $(this).closest('.question-panel').find('.multiple-choice-question').css('display', 'none');
                $(this).closest('.question-panel').find('.text-field-question').css('display', 'block');
                $(this).closest('.question-panel').find('.rating-question').css('display', 'none');

                break;
        }
        $(this).closest(".question-panel")
    });

    $(document).on('click', '.new-answer-row i', function () {
        let $answers$container = $(this).closest('.answers-container');
        let $question_panel = $(this).closest('.question-panel');
        AddNewAnswerRow($answers$container);
        if($($answers$container).find('.answer-row').length >=10) {
            HideNewAnswerButton($question_panel);
        }
    });

    $('.add-new-other-question').on('click', function () {
            let $new_panel = $('.question_options.to-be-cloned').clone();
        $($new_panel).removeClass('to-be-cloned');
        $($new_panel).css("display", 'block');

        $($new_panel).find('select').remove();
        //add a position select
        $('.hidden-selects .hidden-sel-position')
            .find('.select-wrapper').first()
            .detach().appendTo($($new_panel).find('.question-position-row .position-select-div'));

        //add a question type select
        $('.hidden-selects .hidden-sel-type')
            .find('.select-wrapper').first()
            .detach().css('display', 'block').prependTo($($new_panel).find('.question-type-row').find('div'));


        $($new_panel).find('input.answer-text').bind('keypress', function(e) {
            var code = e.keyCode || e.which;
            let index = $(this).closest('.answer-row').index()+1;
            let $question_panel = $(this).closest('.question-panel');
            if(code == 13) { //Enter keycode
                if ($($question_panel).find('.answer-row').length < 10) {
                    AddNewAnswerRow($question_panel, index);
                }
            }
        });
        $($new_panel).find('.remove-other-question').tooltip();
        $('.other-questions-container').append($new_panel);

        //hide the delete answer icons since there will be only one anwer row for each type of question
        $($new_panel).find('.answers-container').each(function () {
            HideDeleteAnswerButton($(this));
        });
    });
    $(document).on('click', '.delete-answer-icon', function () {
        let $answer_row = $(this).closest('.answer-row');
        let $answers_container = $(this).closest('.answers-container');
        $($answer_row).remove();
        if ($($answers_container).find('.answer-row').length == 1) {
            HideDeleteAnswerButton($answers_container);
        }
        let $question_panel = $($answers_container).closest('.question-panel');
        if ($($question_panel).find('.new-answer-row').css('visibility') == 'hidden') {
            ShowNewAnswerButton($question_panel);
        }
    });


    $(document).on('change', 'input[name=enable_other_question]', function () {
        if ($(this).is(':checked')) {
            $(this)
                .closest('.other-question-row')
                .next('.question-panel')
                .slideDown("fast", function () {
                });
        } else {
            $(this)
                .closest('.other-question-row')
                .next('.question-panel')
                .slideUp("fast", function () {
                });
        }
    });
    $(document).on('click', '.remove-other-question', function () {
        $(this).closest('.other-question-panel').remove();
        let  tooltip_instance = M.Tooltip.getInstance($(this));
        tooltip_instance.close();
    });

    $('.save-accompanying-questions-btn').on('click', function () {
        $(this).addClass('pending');
        if (ValidateAccompanyingQuestionsForm()) {
            let data = GetPageFormData();
            let location = '/quiz/update-accompanying-questions';
            $.post({
                url: location,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: data
            }).done(function (data) {

                let result = JSON.parse(data);
                console.log("Success: " + data);

                if (result.status == true) {
                        if($('.quiz-accompanying-questions-page').attr('data-interaction-type')=='create'){
                        window.location = '/quiz/additional-messages/' + $('.page').attr('id');
                    }
                    else {
                        window.location = '/quiz/quiz-info/' + $('.page').attr('id');
                    }

                } else {
                    $('.save-accompanying-questions-btn').removeClass('pending');

                    ShowGlobalMessage('An error occurred while updating the accompanying questions.', 2);

                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log("Error");
                ShowGlobalMessage('An error occurred while updating the accompanying questions.', 2);
                $('.save-accompanying-questions-btn').removeClass('pending');

            }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
                // alert("complete");
            });

        } else {
            $(this).removeClass('pending');

        }
    });

    $('input.answer-text').bind('keypress', function(e) {
        var code = e.keyCode || e.which;
        let index = $(this).closest('.answer-row').index()+1;
        let $question_panel = $(this).closest('.question-panel');
        if(code == 13) { //Enter keycode
            if ($($question_panel).find('.answer-row').length < 10) {
                AddNewAnswerRow($question_panel, index);
            }
        }
    });
    $(document).on('click','.toggle-acc-use-position', function () {
        if ($(this).text() == 'unfold_more') {
            $(this).parent().parent().find('.acc-question-location-feedback')
                .slideDown("fast", function () {
                });
            $(this).text('unfold_less');
        } else {
            $(this).parent().parent().find('.acc-question-location-feedback')
                .slideUp("fast", function () {
                });
            $(this).text('unfold_more');

        }
    })

    $(document).on('change','input[name=question_feedback]',function () {
        UpdatePositioningCheckboxes();
    });
    UpdatePositioningCheckboxes();
    $(document).on('change','input[name=question_initial_phase_display]',function () {
        let $other_question_panel = $(this).closest('.other-question-panel').find('.question-panel');

        if($(this).prop('checked') === false) {
            $($other_question_panel).find('input[name=question_feedback]').prop('checked',false).prop('disabled',true);
            $($other_question_panel).find('input[name=question_location]').prop('checked',false).prop('disabled',true);

        }
        else {
            $($other_question_panel).find('input[name=question_feedback]').prop('disabled',false);

        }
    });
});

function AddFilledOtherQuestionsDropdowns() {
    $('.other-filled').each(function () {
        //add a position select
        let position_value = $('.position-select-div').attr('data-selected-value'),
            type_value = $('.question-type-row .input-field').attr('data-selected-value');
        let $position_select = $('.hidden-selects .hidden-sel-position')
            .find('.select-wrapper')
            .first()
            .detach();
        $($position_select).find('option[value="' + position_value + '"]').attr('selected', true);
        $($position_select).appendTo($(this).find('.question-position-row .position-select-div'));

        //add a question type select
        let $type_select = $('.hidden-selects .hidden-sel-type')
            .find('.select-wrapper')
            .first()
            .detach();
        $($type_select).find('option[value="' + type_value + '"]').attr('selected', true);
        $($type_select).appendTo($(this).find('.question-position-row .position-select-div'));

        $($type_select).css('display', 'block').appendTo($(this).find('.question-type-row').find('div'));


    });
}

function ValidateOtherAccompanyingQuestions() {
    let message = '',
        is_valid = true;
    $('.other-questions-container .other-question-panel').each(function () {
        let $panel = $(this);
        if ($($panel).find('input[name=enable_other_question]').prop('checked') == true) {
            // validate question title
            if ($($panel).find(".new-accompanying-question-name").val().trim() == '') {
                $($panel).find(".new-accompanying-question-name").addClass('invalid-field');
                is_valid = false;
                message += 'The text for the other question  ' + parseInt($($panel).index() + 1) + ' \'s name is missing.<br>';
            } else {
                $($panel).find(".new-accompanying-question-name").removeClass('invalid-field');
            }
            if ($($panel).find('.question-title').val().trim() == '') {
                $($panel).find('.question-title').addClass('invalid-field');
                is_valid = false;
                message += 'The text for the other question  ' + parseInt($($panel).index() + 1) + ' \'s title is missing.<br>';

            } else {
                $($panel).find(".question-title").removeClass('invalid-field');

            }
            //validate question content
            let question_type = $($panel).find('.question-type-dropdown option:selected').attr('value');
            if (question_type == 1) {
                //multiple choice
                $($panel).find('.multiple-choice-question .answer-row').each(function () {

                    if ($(this).find('.answer-text').val().trim() == '') {
                        $(this).find('.answer-text').addClass('invalid-field');
                        is_valid = false;
                        message += 'The text for the option ' + parseInt($(this).index() + 1) + ' of the  other question' + parseInt($($panel).index() + 1) + ' is missing.<br>';

                    } else {
                        $(this).find('.answer-text').removeClass('invalid-field');
                    }
                });
            } else if (question_type == 2) {
                //rating question
                $($panel).find('.rating-question .answer-row').each(function () {

                    if (isNaN(parseInt($(this).find('.answer-text').val()))) {
                        $(this).find('.answer-text').addClass('invalid-field');
                        is_valid = false;
                        message += 'The text for the option ' + parseInt($(this).index() + 1) + ' of the  other question' + parseInt($($panel).index() + 1) + ' is invalid.<br>';

                    } else {
                        $(this).find('.answer-text').removeClass('invalid-field');
                    }
                });
            }
        }

    });
    return {message: message, is_valid: is_valid}
}

function ValidatePrepQuestion() {
    let message = '',
        is_valid = true;
    //validate the preparation question form
    let $prep_panel = $('input[name=enable_prep_question]').closest('.row').next('.question_options').find('.question-panel');
    if ($($prep_panel).find('.question-title').val().trim() == '') {
        $($prep_panel).find('.question-title').addClass('invalid-field');
        is_valid = false;
        message += 'Preparation question\'s title is missing<br>';

    } else {
        $($prep_panel).find('.question-title').removeClass('invalid-field');
    }
    $($prep_panel).find('.answer-row').each(function () {
        if ($(this).find('.answer-text').val().trim() == '') {
            $(this).find('.answer-text').addClass('invalid-field');
            is_valid = false;
            message += 'The text for the option ' + parseInt($(this).index() + 1) + ' of preparation question is missing<br>';

        } else {
            $(this).find('.answer-text').removeClass('invalid-field');
        }


    });

    return {message: message, is_valid: is_valid}
}

function ValidateConfQuestion() {
    let message = '',
        is_valid = true;
    let $conf_panel = $('input[name=enable_conf_question]').closest('.row').next('.question_options').find('.question-panel');
    if ($($conf_panel).find('.question-title').val().trim() == '') {
        $($conf_panel).find('.question-title').addClass('invalid-field');
        is_valid = false;
        message += 'Confidence question\'s title is missing<br>';

    } else {
        $($conf_panel).find('.question-title').removeClass('invalid-field');
    }


    $($conf_panel).find('.answer-row').each(function () {
        if ($(this).find('.answer-text').val().trim() == '') {
            $(this).find('.answer-text').addClass('invalid-field');
            is_valid = false;
            message += 'The text for the option ' + parseInt($(this).index() + 1) + ' of confidence question is missing<br>';

        } else {
            $(this).find('.answer-text').removeClass('invalid-field');
        }
    });
    return {message: message, is_valid: is_valid}

}

function ValidateJustQuestion() {
    let message = '',
        is_valid = true;

    let $just_panel = $('input[name=enable_just_question]').closest('.row').next('.question_options').find('.question-panel');
    if ($($just_panel).find('.question-title').val().trim() == '') {
        $($just_panel).find('.question-title').addClass('invalid-field');
        is_valid = false;
        message += 'Justification question\'s title is missing<br>';

    } else {
        $($just_panel).find('.question-title').removeClass('invalid-field');
    }

    return {message: message, is_valid: is_valid}

}

function ValidateAccompanyingQuestionsForm() {
    let message = '',
        is_valid = true;
    if ($('input[name=enable_prep_question]').prop('checked') == true) {
        //validate the preparation question form
        let prep_question_validation_result = ValidatePrepQuestion();
        message += prep_question_validation_result.message;
        if (prep_question_validation_result.is_valid == false) {
            is_valid = false;
        }
    }

    if ($('input[name=enable_conf_question]').prop('checked') == true) {
        //validate the confidence question form
        let conf_question_validation_result = ValidateConfQuestion();
        message += conf_question_validation_result.message;
        if (conf_question_validation_result.is_valid == false) {
            is_valid = false;
        }
    }

    if ($('input[name=enable_just_question]').prop('checked') == true) {
        //validate the justification question form
        let just_question_validation_result = ValidateJustQuestion();
        message += just_question_validation_result.message;
        if (just_question_validation_result.is_valid == false) {
            is_valid = false;
        }
    }

    let other_questions_validation_result = ValidateOtherAccompanyingQuestions();
    message += other_questions_validation_result.message;

    if (other_questions_validation_result.is_valid == false) {
        is_valid = false;
    }
    if (message != '') {
        ShowGlobalMessage(message, 2);
        $("html, body").animate({scrollTop: 0});
    }
    console.log('the form ', is_valid == true ? 'valid' : 'invalid');
    return is_valid;
}


function GetPageFormData() {
    let form_object = {};
    form_object.quiz_id = $('.page').attr('id');
    if ($('input[name=enable_prep_question]').prop('checked') == true) {
        form_object.prep_question = {};
        let $prep_panel = $('input[name=enable_prep_question]').closest('.row').next('.question_options').find('.question-panel');
        form_object.prep_question.question_title = $($prep_panel).find('.question-title').val();
        form_object.prep_question.question_explanation = $($prep_panel).find('.question-explanation').val();
        form_object.prep_question.question_type = $($prep_panel).find('.question-type-dropdown option:selected').attr('value');
        form_object.prep_question.question_options = [];
        $($prep_panel).find('.answer-row').each(function () {
            form_object.prep_question.question_options.push({
                text: $(this).find('.answer-text').val(),
                id: $(this).find('.answer-text').attr('id'),
                index: $(this).index() + 1
            });
        });
    } else {
        form_object.prep_question = null;
    }

    if ($('input[name=enable_conf_question]').prop('checked') == true) {
        form_object.conf_question = {};
        let $conf_panel = $('input[name=enable_conf_question]').closest('.row').next('.question_options').find('.question-panel');
        form_object.conf_question.question_title = $($conf_panel).find('.question-title').val();
        form_object.conf_question.question_explanation = $($conf_panel).find('.question-explanation').val();
        form_object.conf_question.question_type = $($conf_panel).find('.question-type-dropdown option:selected').attr('value');
        form_object.conf_question.question_options = [];
        $($conf_panel).find('.answer-row').each(function () {
            form_object.conf_question.question_options.push({
                text: $(this).find('.answer-text').val(),
                id: $(this).find('.answer-text').attr('id'),
                index: $(this).index() + 1
            });
        });

        form_object.conf_question.init_ph_display = [];
        form_object.conf_question.rev_ph_display = [];
        $($conf_panel).find('.question-row').each(function () {
            if ($(this).find('input[name=question_feedback]').is(':checked')) {
                form_object.conf_question.init_ph_display.push($(this).attr('id'));
            }
            if ($(this).find('input[name=question_location]').is(':checked')) {
                form_object.conf_question.rev_ph_display.push($(this).attr('id'));
            }
        });
    } else {
        form_object.conf_question = null;
    }

    if ($('input[name=enable_just_question]').prop('checked') == true) {
        form_object.just_question = {};
        let $just_panel = $('input[name=enable_just_question]').closest('.row').next('.question_options').find('.question-panel');
        form_object.just_question.question_title = $($just_panel).find('.question-title').val();
        form_object.just_question.question_explanation = $($just_panel).find('.question-explanation').val();
        form_object.just_question.question_type = $($just_panel).find('.question-type-dropdown option:selected').attr('value');
        form_object.just_question.question_options = [];
        $($just_panel).find('.answer-row').each(function () {
            form_object.just_question.question_options.push({
                text: $(this).find('.answer-text').val(),
                id: $(this).find('.answer-text').attr('id'),
                index: $(this).index() + 1
            });
        });

        form_object.just_question.init_ph_display = [];
        form_object.just_question.rev_ph_display = [];
        $($just_panel).find('.question-row').each(function () {
            if ($(this).find('input[name=question_feedback]').is(':checked')) {
                form_object.just_question.init_ph_display.push($(this).attr('id'));
            }
            if ($(this).find('input[name=question_location]').is(':checked')) {
                form_object.just_question.rev_ph_display.push($(this).attr('id'));
            }
        });
    } else {
        form_object.just_question = null;
    }

    let $checked_other_question = $('input[name=enable_other_question]:checked');
    if ($checked_other_question.length > 0) {
        form_object.other_questions = [];
        $($checked_other_question).each(function () {
            let other_question_form_object = {};
            let $other_question_panel = $(this).closest('.other-question-panel').find('.question-panel');

            other_question_form_object.id = $(this).closest('.other-question-panel').attr('id');
            other_question_form_object.question_name = $(this).closest('.other-question-panel').find('.new-accompanying-question-name').val();
            other_question_form_object.question_title = $($other_question_panel).find('.question-title').val();
            other_question_form_object.question_explanation = $($other_question_panel).find('.question-explanation').val();
            other_question_form_object.question_position = $($other_question_panel).find('.question-position option:selected').attr('value');

            if (other_question_form_object.question_position == 2) {
                other_question_form_object.initial_phase_display = $($other_question_panel).find('input[name=question_initial_phase_display]').is(':checked');
                other_question_form_object.revision_phase_display = $($other_question_panel).find('input[name=question_revision_phase_display]').is(':checked');
            }
            other_question_form_object.question_type = $($other_question_panel).find('.question-type-dropdown option:selected').attr('value');
            other_question_form_object.question_options = [];
            if (other_question_form_object.question_type == 1) {
                $($other_question_panel).find('.multiple-choice-question .answer-row').each(function () {
                    other_question_form_object.question_options.push({
                        text: $(this).find('.answer-text').val(),
                        id: $(this).find('.answer-text').attr('id'),
                        index: $(this).index() + 1
                    });
                });
            } else if (other_question_form_object.question_type == 2) {
                $($other_question_panel).find('.rating-question .answer-row').each(function () {
                    other_question_form_object.question_options.push({
                        text: $(this).find('.answer-text').val(),
                        id: $(this).find('.answer-text').attr('id'),
                        index: $(this).index() + 1
                    });
                });
            }


            if (other_question_form_object.question_position == "2") {
                other_question_form_object.init_ph_display = [];
                other_question_form_object.rev_ph_display = [];
                $($other_question_panel).find('.question-row').each(function () {
                    if ($(this).find('input[name=question_feedback]').is(':checked')) {
                        other_question_form_object.init_ph_display.push($(this).attr('id'));
                    }
                    if ($(this).find('input[name=question_location]').is(':checked')) {
                        other_question_form_object.rev_ph_display.push($(this).attr('id'));
                    }
                });
            }
            form_object.other_questions.push(other_question_form_object);
        });
    } else {
        form_object.other_questions = null;
    }
    return form_object;
}

function HideNewAnswerButton($q_panel) {
    $($q_panel).find('.new-answer-row').css('visibility', 'hidden');
}

function ShowNewAnswerButton($q_panel) {
    $($q_panel).find('.new-answer-row').css('visibility', 'visible');
}

function GetQuestionFormData($panel) {
    let result = {};
    //preparation question
    result.question_title = $($panel).find('.question-title').val();
    result.question_position = $($panel).find('.question-location option:selected').attr('value');
    result.question_postion_index = $($panel).find('.question-index option:selected').index() + 1;
    result.question_type = $($panel).find('.question-type-dropdown option:selected').attr('value');
    result.question_options = null;
    if (result.question_type == "1" || result.question_type == "2") {
        result.question_options = [];
        $($panel).find('.answer-row').each(function () {
            result.question_options.push($(this).find('.answer-text').val());
        });
    }

    return result;
}

function AddNewAnswerRow($answers$container) {
    let $panel = $($answers$container).closest('.question-panel');
    let $new_row = $('.answer-row.to-be-cloned').clone();
    $($new_row).removeClass('to-be-cloned');

    let input_type = $($answers$container).find('input').first().attr("type");

    let option_counter = $($answers$container).find('.answer-row').length;

    if (input_type == 'number') {
        $($new_row).find('.input-field input').val((option_counter + 1));
        $($new_row).find('.input-field input').attr('type', 'number');
    } else {
        $($new_row).find('.input-field input').val((option_counter + 1));
        $($new_row).find('.input-field input').attr('type', 'text');
    }
    $($new_row).find('input.answer-text').bind('keypress', function(e) {
        var code = e.keyCode || e.which;
        let index = $(this).closest('.answer-row').index()+1;
        let $question_panel = $(this).closest('.question-panel');
        if(code == 13) { //Enter keycode
            if ($($question_panel).find('.answer-row').length < 10) {
                AddNewAnswerRow($question_panel, index);
            }
        }
    }).bind('keyup', function(e) {
       $(this).val($(this).val().substring(0,3));
    });


    $($new_row).find('.correct-answer-icon input').prop('checked', false).attr("name", 'correct_answer' + $($panel).index());
    $($new_row).insertBefore($($answers$container).find('.new-answer-row'));

    if ($($answers$container).find('.answers-container').children('.answer-row').length >= 10) {
        HideNewAnswerButton($panel);
    }

    if ($($answers$container).find('.delete-answer-icon').css('visibility') == 'hidden') {
        ShowDeleteAnswerButton($answers$container);
    }
}

function HideDeleteAnswerButton($answers_container) {
    $($answers_container).find('.delete-answer-icon').css('visibility', 'hidden');
}

function ShowDeleteAnswerButton($answers_container) {
    $($answers_container).find('.delete-answer-icon').css('visibility', 'visible');

}

function UpdatePositioningCheckboxes() {
    $('.questions-container').each(function () {
        $(this).find('.feedback').each(function () {

                if($(this).find('input[name=question_feedback]').prop('checked') === true) {
                    $(this).find('input[name=question_location]').attr('disabled',false);
                }
                else{
                    $(this).find('input[name=question_location]').prop('checked',false);

                    $(this).find('input[name=question_location]').attr('disabled',true);

                }
        });
    });
}
