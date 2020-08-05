let question_index = 0;
let questions = null;
let right_answer_index = null;
$(document).ready(function () {
    console.log('page loaded');
    AlignTheResponses();

    $('.next-question-btn').on('click', function () {
        question_index++;
        UpdateQuestionPanel();
        UpdateButtonsDisplay();
    });
    $('.previous-question-btn').on('click', function () {
        question_index--;
        UpdateQuestionPanel();
        UpdateButtonsDisplay();
    });

    $('.show-answer-btn').on('click',function () {
        $('.question-choices').find('.choice-div:nth-child('+right_answer_index+')').css('font-weight','bold');
        $('.responses').find('.choice-div:nth-child('+right_answer_index+')').css('font-weight','bold');
    });
    questions = JSON.parse($('.quiz-presentation-page').attr('data-questions'));
    right_answer_index = $('.quiz-presentation-panel').attr('data-right-answer');
    UpdateButtonsDisplay();
});

window.onresize = AlignTheResponses;
function UpdateQuestionPanel() {
    $('.question-choices .choice-div').remove();
    $('.responses .choice-div').remove();

    for (let q_iter = 0; q_iter < questions.length; q_iter++) {
            if(q_iter == question_index) {
                //update question title index
                $('.question-index').text('Question '+(q_iter+1));
                //update question title
                $('.main-question-title').text(questions[q_iter].text);
                //update image link
                $('.main-question-image img').attr('src',questions[q_iter].image_link);
                if(questions[q_iter].image_link != '' && questions[q_iter].image_link != null) {
                    $('.main-question-image').css('display','block');
                }
                else {
                    $('.main-question-image').css('display','none');

                }
                //update right answer
                right_answer_index = questions[q_iter].right_answer;
                //update answers & responses
                for(let ans_iter=0;ans_iter<questions[q_iter].answers.length; ans_iter++) {
                    //create answer row
                    let $ans_row = $('<div>',{
                        class: 'choice-div',
                        text: String.fromCharCode(64 + (ans_iter + 1)).toUpperCase() + ' : ' + questions[q_iter].answers[ans_iter].text
                    });
                    $('.question-choices').append($ans_row);
                    //create response row
                    let $resp_row = $('<div>',{
                        class: 'choice-div'
                    });
                    let $init_resp = $('<div>',{
                        class:'initial-phase-response',
                        text:questions[q_iter].answers[ans_iter].init_resp.toFixed(2) + ' %'
                    });
                    let $rev_resp = $('<div>',{
                        class:'revision-phase-response',
                        text:questions[q_iter].answers[ans_iter].rev_resp.toFixed(2) + ' %'
                    });
                    $($resp_row).append($init_resp,$rev_resp);
                    $('.responses').append($resp_row);
                }

            }
    }
    AlignTheResponses();
}

function UpdateButtonsDisplay() {
    if (question_index <= 0) {
        $('.previous-question-btn').css('visibility', 'hidden');
    } else {
        $('.previous-question-btn').css('visibility', 'visible');

    }
    if (question_index >= questions.length - 1) {
        $('.next-question-btn').css('visibility', 'hidden');
    } else {
        $('.next-question-btn').css('visibility', 'visible');

    }
}

function AlignTheResponses() {


    let panel_body_width = parseFloat($('.panel-body').css('width'));
    let right_panel_width = 0;

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

    $('.headers-row').css('width', $('.question-choices').css('width'));

    $('.revision-metrics-section .responses .choice-div').each(function () {
        let current_index = $(this).index();
        let height = $('.question-choices .choice-div').eq(current_index).css('height');
        $(this).find('div').css('height', height)
            .css('line-height', height);
    });
}
