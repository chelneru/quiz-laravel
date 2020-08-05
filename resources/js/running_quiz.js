$(document).ready(function () {
    $('select').formSelect();
    let page_load = Date.now();

    $('#question-form').submit(function () {
        let $form = $(this);
        $('.submit-answer').addClass('pending');
        if ($('.acc-question-panel').length > 0) {
            //we have an acc question
            if ($('.acc-question-panel input[name=acc_question_answer_id]').length > 0) {
                //we have an acc question with options

                if ($('.acc-question-panel input[name=acc_question_answer_id]:checked').length > 0) {
                    let text = $('.acc-question-panel input[name=acc_question_answer_id]:checked').closest('label').find('span').text();

                    let $input = $('<input/>', {
                        type: 'hidden',
                        value: text,
                        name: 'acc_question_answer'
                    });
                    $('#question-form').append($input);
                }
            }
        }
        if($('.running-quiz-panel').length > 0) {
            //we have normal questions

            // we need to build the other questions answers.
            $(".other-question-section input[name^='other_question_ids']").each(function () {
                let other_question_id= $(this).val();
                let $section = $(this).closest('.other-question-section');
                let value = $($section).find("input[name='other_question_answers_content_"+other_question_id+"']:checked", ).val();
                if(value === null || value === undefined ) {
                    value = $($section).find("input[name='other_question_answers_content_"+other_question_id+"']", ).val();
                }
                let $input = $('<input/>', {
                    type: 'hidden',
                    value: value,
                    name: 'other_question_answers[]'
                });
                $('#question-form').append($input);
            });

        }

        let $response_duration_input = $('<input/>', {
            name: 'response_duration',
            type: 'hidden',
            'value': ((Date.now() - page_load) / 1000.0).toFixed(3)
        });
        $($form).append($response_duration_input);
    });

    $('.justif-answers-row .answer-letter').on('click', function () {
        $(this).addClass('selected');
        $(this).siblings().removeClass('selected');
        let answer_index = $(this).attr('data-index');
        let just_answers_data = JSON.parse($('.justification-section').attr('data-just-question-answers'));
        just_answers_data = Object.keys(just_answers_data).map(function (key) {
            //convert object to array and also calculate average
            return [key, just_answers_data[key]];
        });
        $('.justification-section').find('.just-question-answers .just-answer-row').remove();
        let question_id = $('#question-form input[name=question_id]').val();
        for (let answer_iter = 0; answer_iter < just_answers_data.length; answer_iter++) {
            if (just_answers_data[answer_iter][1].question_id == question_id && just_answers_data[answer_iter][1].answer_index == parseInt(answer_index) + 1) {
                let $answer_row = $('<div/>', {
                    class: 'just-answer-row',
                    text: just_answers_data[answer_iter][1].answer_content
                });
                $('.justification-section .just-question-answers').append($answer_row);

            }
        }


    });

    $('.other-questions-answers-row .answer-letter').on('click', function () {
        let $section = $(this).closest('.other-question-responses-section');
        $(this).addClass('selected');
        $(this).siblings().removeClass('selected');
        let answer_index = $(this).attr('data-index');
        let other_question_answers_data = JSON.parse($($section).attr('data-other-question-answers'));
        other_question_answers_data = Object.keys(other_question_answers_data).map(function (key) {
            //convert object to array and also calculate average
            return [key, other_question_answers_data[key]];
        });
        $($section).find('.other-questions-answers .other-questions-answer-row').remove();
        let question_id = $('#question-form input[name=question_id]').val();
        for (let answer_iter = 0; answer_iter < other_question_answers_data.length; answer_iter++) {
            if (other_question_answers_data[answer_iter][1].question_id == question_id && other_question_answers_data[answer_iter][1].answer_index == parseInt(answer_index) + 1) {
                let $answer_row = $('<div/>', {
                    class: 'other-questions-answer-row',
                    text: other_question_answers_data[answer_iter][1].answer_content
                });
                $($section).find('.other-questions-answers').append($answer_row);

            }
        }


    });
    //some CSS modifications
    if ($('.revision-metrics-section').length > 0) {
        AlignTheResponses();
        AlignPercentagesTable();

        window.onresize = function (event) {
            AlignTheResponses();
            AlignPercentagesTable();

        };
    }

});

function AlignTheResponses() {


    let panel_body_width = parseFloat($('.panel-body').css('width'));
    let right_panel_width = 0;
    if ($('.justification-section').length == 0 && $('.other-question-responses-section').length == 0) {
        //if we dont have justifications then we push a little more the left panel and hide the vertical dividing line
        $('.panel-body-left ').css('border-right', 'none');
        right_panel_width = panel_body_width / 5;
    } else {
        right_panel_width = panel_body_width / 3;
    }

    if(panel_body_width <=613) {
        //mobile view
        $('.panel-body-left ').css('width','100%' );
        $('.panel-body-right ').css('width','100%' );
        let left_panel_width = parseFloat($('.panel-body-left ').css('width'));

        let left_section_width = parseFloat(left_panel_width) / 2;

        if (parseFloat($('.revision-metrics-section').css('max-width')) <= left_section_width) {
            //the revision section doesnt need more width so we allocate it to the answers section instead.
            $('.question-choices').css('width', left_panel_width - parseFloat($('.revision-metrics-section').css('max-width')) - 2);

            $('.revision-metrics-section').css('width', parseFloat($('.revision-metrics-section').css('max-width')));

        } else {
            $('.revision-metrics-section').css('width', parseFloat(left_panel_width) / 2);
            $('.question-choices').css('width', left_section_width);

        }
    }
    else {
        let left_panel_width = panel_body_width - right_panel_width - 20;

        //normal view
        $('.panel-body-left ').css('width', left_panel_width);
        $('.panel-body-right ').css('width', right_panel_width);

        let left_section_width = parseFloat(left_panel_width) / 2;
        if (parseFloat($('.revision-metrics-section').css('max-width')) <= left_section_width) {
            //the revision section doesnt need more width so we allocate it to the answers section instead.
            $('.question-choices').css('width', left_panel_width - parseFloat($('.revision-metrics-section').css('max-width')) - 2);
            $('.revision-metrics-section').css('width', parseFloat($('.revision-metrics-section').css('max-width')));

        } else {
            $('.revision-metrics-section').css('width', parseFloat(left_panel_width) / 2);
            $('.question-choices').css('width', left_section_width);

        }
    }
    $('.headers-row').css('width',$('.question-choices').css('width'));

    $('.revision-metrics-section .responses .choice-div').each(function () {
        let current_index = $(this).index();
        let height = $('.question-choices .choice-div').eq(current_index).css('height');
        $(this).find('div').css('height', height)
            .css('line-height', height);
    });
}

function AlignPercentagesTable() {
    $('.responses-headers div').each(function (index) {
        let header_width = parseFloat($(this).css('width'));
        $('.choice-div div:nth-child('+(index+1)+')').css('width',header_width);
    });
}
