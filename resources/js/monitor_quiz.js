let _$panel = null,
    _questions_data = null,
    _progress_initial_chart = null,
    _progress_revision_chart = null,
    _prep_pie_chart = null,
    _other_questions_pie_charts = [],
    _show_phase_change_notifcation = true,
    _can_request_data = true,
    updateDataIntervalId = null;


$(document).ready(function () {
    $('.modal').modal();
    $('select').formSelect();

    _$panel = $('.main-panel');
    highlightPercentages();
    $('.modify-quiz-status').on('click', function () {
        let current_status = $(_$panel).attr('data-quiz-status');
        let new_status = current_status == 1 ? 2 : 1;
        let quiz_id = $(_$panel).attr('data-quiz-id');

        ModifyQuizStatus(quiz_id, new_status);
    });
    $('.modify-quiz-reveal-answers').on('click', function () {
        let current_status = $(_$panel).attr('data-quiz-reveal-answers-status');
        let new_status = current_status == 1 ? 0 : 1;

        let quiz_id = $(_$panel).attr('data-quiz-id');

        ModifyQuizRevealAnswersStatus(quiz_id, new_status);
    });


    $('.modify-quiz-phase').on('click', function () {
        let current_phase = $(_$panel).attr('data-quiz-phase');
        let new_phase = current_phase == 1 || current_phase == null ? 2 : 1;

        let quiz_id = $(_$panel).attr('data-quiz-id');

        ModifyQuizPhase(quiz_id, new_phase);
    });

    $(_$panel).find('.percentage-section .choose-phase-tabs div').on('click', function () {
        $(this).addClass('selected');
        $(this).siblings().removeClass('selected');
        if ($(this).hasClass('select-phase-1')) {
            $('.phases .phase-1').css('display', 'block');
            $('.phases .phase-2').css('display', 'none');
        } else if ($(this).hasClass('select-phase-2')) {
            $('.phases .phase-1').css('display', 'none');
            $('.phases .phase-2').css('display', 'block');
        }

    });
    $(document).on('click', '.justifications-section .answer-tab', function () {
        $(this).addClass('selected');
        $(this).siblings().removeClass('selected');

        //update justifications rows content
        let question_id = $('.just-question-select').find('option:selected').val();
        let answer_index = $(this).attr('data-index');

        UpdateJustQuestionResponsesContainer(question_id, answer_index);

    });

    $(document).on('click', '.other-section .answer-tab', function () {
        $(this).addClass('selected');
        let $section = $(this).closest('.other-section');
        $(this).siblings().removeClass('selected');

        //update justifications rows content
        let question_id = $($section).find('.other-question-select').find('option:selected').val();
        let answer_index = $(this).attr('data-index');

        UpdateOtherQuestionResponsesContainer($section, question_id, answer_index);

    });

    $('.just-question-select').on('change', function () {
        let question_id = $('.just-question-select').find('option:selected').val();

        UpdateJustQuestionAnswerTabs(question_id, _questions_data);
        //auto select the first answer tab
        let $first_answer_tab = $('.justifications-section .answer-tab:first-child');
        $($first_answer_tab).click();
    });

    $('.other-question-select').on('change', function () {
        let question_id = $(this).find('option:selected').val();
        let acc_question_id = $(this).closest('.other-section').attr('id');
        UpdateOtherQuestionAnswerTabs(acc_question_id, question_id, _questions_data);
        //auto select the first answer tab
        let $first_answer_tab = $(this).closest('.other-question-section').find('.answer-tab:first-child');
        $($first_answer_tab).click();
    });

    $(_$panel).find('.conf-question-section .phases div').on('click', function () {
        $(this).addClass('selected');
        $(this).siblings().removeClass('selected');

        if ($(this).hasClass('initial-phase-tab')) {
            $(_$panel).find('.conf-question-section .conf-phase-1').css('display', 'block');
            $(_$panel).find('.conf-question-section .conf-phase-2').css('display', 'none');
        } else if ($(this).hasClass('revision-phase-tab')) {
            $(_$panel).find('.conf-question-section .conf-phase-1').css('display', 'none');
            $(_$panel).find('.conf-question-section .conf-phase-2').css('display', 'block');
        }
    });
    $(_$panel).find('.other-section .phases div').on('click', function () {
        $(this).addClass('selected');
        let $current_section = $(this).closest('.other-section');
        $(this).siblings().removeClass('selected');

        if ($(this).hasClass('initial-phase-tab')) {
            $($current_section).find('.conf-phase-1').css('display', 'block');
            $($current_section).find('.conf-phase-2').css('display', 'none');
        } else if ($(this).hasClass('revision-phase-tab')) {
            $($current_section).find('.conf-phase-1').css('display', 'none');
            $($current_section).find('.conf-phase-2').css('display', 'block');
        }
    });

    if ($('.main-panel').attr('data-quiz-status') == 1) {
        updateDataIntervalId = setInterval(GetUpdatedData, 3000); //3000 MS == 3 seconds
        LoadProgressCharts();

    }

    $('#edit-quiz-scheduling-modal .confirm-btn').on('click', function () {
        let quiz_id = $('.default-panel').attr('data-quiz-id');

        let phase = $(_$panel).attr('data-quiz-phase');

        let minutes_amount = $('#edit-quiz-scheduling-modal').find('.extension-amount-select option:selected').val();
        if (ValidateQuizSchedulingModal()) {
            ExtendQuizScheduling(quiz_id, phase, minutes_amount);
        }
    });
    $('.open-schedule-edit-modal').on('click', function () {
        let current_phase = $(_$panel).attr('data-quiz-phase');
        let current_phase_start = $(_$panel).find('.timeline-tab:nth-child(' + current_phase + ') .timeline-tab-value-start').text();
        $('#edit-quiz-scheduling-modal').find('.extension-amount-select option').each(function () {
            if (moment(current_phase_start, 'DD MMM HH:mm').diff(moment(), 'minutes') > $(this).val()) {

                $(this).attr('disabled', false);
            } else {
                $(this).attr('disabled', true);
            }
        });
        $('#edit-quiz-scheduling-modal').find('.extension-amount-select').formSelect();

    });

});

//optimize the transition to other page
window.addEventListener("beforeunload", function (event) {
    console.log('disabling update.');
    clearInterval(updateDataIntervalId);
});

function LoadProgressCharts() {
    // Load the Visualization API and the corechart package.
    google.charts.load('current', {'packages': ['corechart']});

    // Set a callback to run when the Google Visualization API is loaded.

    if (_questions_data == null) {
        _questions_data = JSON.parse($(_$panel).attr('data-quiz'));
    }


    google.setOnLoadCallback(function () {
        DrawProgressChart('progress_initial_chart', 1, _questions_data, false);
    });
    google.setOnLoadCallback(function () {
        DrawProgressChart('progress_revision_chart', 2, _questions_data, false);
    });

    if ($(_$panel).find('#preparation-pie-chart').length > 0) {

        google.setOnLoadCallback(function () {
            DrawPieChart('preparation-pie-chart', _questions_data.prep_question, false);
        });
    }
    if ($(_$panel).find('div[id^="other-question-pie-chart"]').length > 0) {
        $($(_$panel).find('div[id^="other-question-pie-chart"]').each(function () {
            let acc_question_id = $(this).attr('id').replace(/[^\d.]/g, '');
            let acc_question_data = null;
            let acc_question_identifier = $(this).attr('id');

            for (let iter = 0; iter < _questions_data.other_questions.length; iter++) {

                if (_questions_data.other_questions[iter].id == acc_question_id) {
                    acc_question_data = _questions_data.other_questions[iter].responses;
                    break;
                }
            }
            google.setOnLoadCallback(function () {
                DrawPieChart(acc_question_identifier, acc_question_data, false);
            });
        }));

    }

}

function GetUpdatedData() {
    if (_can_request_data == true) {
        _can_request_data = false;
        let quiz_id = $('.default-panel').attr('data-quiz-id');

        $.post({
            url: '/quiz/get-monitoring-info',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {quiz_id: quiz_id}
        }).done(function (data) {
            try {
                let result = JSON.parse(data);
                // console.log(result);
                if (result.status == true) {
                    UpdateMonitoringInfo(result);
                }

            } catch (e) {
                console.log('invalid response from the server.', e.toString(), data);
                return false;
            }


        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log("Error", textStatus, errorThrown);
        }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
            // alert("complete");
            _can_request_data = true;
        });
    }
}

function UpdateQuizPercentages(data) {

    for (let phase_iter = 1; phase_iter <= 2; phase_iter++) {
        for (let question_iter = 0; question_iter < data.questions.length; question_iter++) {

            if (data.questions[question_iter].responses !== undefined) {
                let current_question = data.questions[question_iter].responses[phase_iter];
                if (current_question.answers_values !== undefined) {

                    for (let answer_iter = 0; answer_iter < current_question.answers_values.length; answer_iter++) {

                        let percentage = current_question.answers_values[answer_iter] / current_question.total_responses * 100;
                        if (isNaN(percentage)) {
                            percentage = 0;
                        }
                        let $answer_row = $('.percentage-section .phase-' + phase_iter).find('.row[data-index=' + (question_iter + 1) + ']');
                        let $answer_cell = $($answer_row).find('.answers-count[data-index=' + (answer_iter + 1) + ']');

                        $($answer_cell).text(percentage.toFixed(2) + ' %');
                    }
                }
            }
        }
    }
    highlightPercentages();
}

function UpdateJustQuestion(data) {
    _questions_data.just_question = data.just_question;
    //update the justification section currently displayed
    let current_just_question = $('.just-question-select').find('option:selected').val();
    let current_just_answer_index = $('.justifications-section .answer-tab.selected').index() + 1;

    $(_$panel).find('.justifications-container .justification-content-row').remove();
    if (_questions_data.just_question != undefined) {
        if (_questions_data.just_question.responses[current_just_question] != undefined && _questions_data.just_question.responses[current_just_question][current_just_answer_index] != undefined) {
            for (let answer_iter = 0; answer_iter < _questions_data.just_question.responses[current_just_question][current_just_answer_index].length; answer_iter++) {

                let $new_just_answer_row = $('<div/>', {
                    class: 'justification-content-row',
                    text: _questions_data.just_question.responses[current_just_question][current_just_answer_index][answer_iter]
                });
                $(_$panel).find('.justifications-container').append($new_just_answer_row);
            }
        }

        //update justifications rows content
        let question_id = $('.just-question-select').find('option:selected').val();
        let answer_index = $('.justifications-section .answer-tab.selected').attr('data-index');

        UpdateJustQuestionResponsesContainer(question_id, answer_index);
    }
}

function UpdateOutsideOtherTextQuestions(data) {
    if (data.other_questions !== undefined) {
        let selected_other_questions = data.other_questions.filter(function (other_question) {
            return other_question.structure == 3 && other_question.type == 4 && other_question.other_question_position == 2;
        });
        //iterate over questions

        for (let qiter = 0; qiter < selected_other_questions.length; qiter++) {
            let $section = $('#' + selected_other_questions[qiter].id);
            let currOtherQuestion = selected_other_questions[qiter];
            //update the other question section currently displayed
            let current_other_question = $($section).find('.other-question-select').find('option:selected').val();
            let current_other_answer_index = $($section).find('.answer-tab.selected').index() + 1;

            $($section).find('.other-content-row').remove();

            if (currOtherQuestion != undefined) {
                if (currOtherQuestion.responses[current_other_question] != undefined && currOtherQuestion.responses[current_other_question][current_other_answer_index] != undefined) {
                    for (let answer_iter = 0; answer_iter < currOtherQuestion.responses[current_other_question][current_other_answer_index].length; answer_iter++) {

                        let $new_just_answer_row = $('<div/>', {
                            class: 'other-content-row',
                            text: currOtherQuestion.responses[current_other_question][current_other_answer_index][answer_iter]
                        });
                        $($section).find('.other-question-container').append($new_just_answer_row);
                    }
                }

                //update other question rows content
                let question_id = $($section).find('.other-question-select').find('option:selected').val();
                let answer_index = $($section).find('.answer-tab.selected').attr('data-index');

                UpdateOtherQuestionResponsesContainer($section, question_id, answer_index);

            }
        }
    }
}

function UpdateConfidenceQuestion(data) {

    if (data.conf_question != undefined) {
        for (let phase_iter = 1; phase_iter <= 2; phase_iter++) {
            if (data.conf_question.responses != undefined && data.conf_question.responses[phase_iter] != undefined) {
                let conf_data = Object.keys(data.conf_question.responses[phase_iter]).map(function (key) {
                    //convert object to array and also calculate average
                    return [key, data.conf_question.responses[phase_iter][key]];
                });

                for (let question_iter = 0; question_iter < conf_data.length; question_iter++) {
                    if (conf_data[question_iter] != undefined) {
                        let $question_row = $('.conf-question-section .conf-phase-' + phase_iter + ' .question-row[data-question-id=' + conf_data[question_iter][0] + ']');
                        $($question_row).find('.all-conf-answers').text(conf_data[question_iter][1].all_average.toFixed(2));
                        $($question_row).find('.correct-conf-answers').text(conf_data[question_iter][1].correct_average.toFixed(2));
                        $($question_row).find('.incorrect-conf-answers').text(conf_data[question_iter][1].incorrect_average.toFixed(2));
                    }
                }
            }
        }
    }
}

function UpdateJustQuestionResponsesContainer(question_id, answer_index) {
    $(_$panel).find('.justifications-container .justification-content-row').remove();
    if (_questions_data.just_question.responses[question_id] != undefined && _questions_data.just_question.responses[question_id][answer_index] != undefined) {
        //if everything is ok and we have an array then we sort the answers descending based on the response length
        try {
            _questions_data.just_question.responses[question_id][answer_index].sort(function (a, b) {
                // ASC  -> a.length - b.length
                // DESC -> b.length - a.length
                return b.length - a.length;
            });
        } catch (e) {

        }
        for (let answer_iter = 0; answer_iter < _questions_data.just_question.responses[question_id][answer_index].length; answer_iter++) {

            let $new_just_answer_row = $('<div/>', {
                class: 'justification-content-row',
                text: _questions_data.just_question.responses[question_id][answer_index][answer_iter]
            });
            $(_$panel).find('.justifications-container').append($new_just_answer_row);
        }
    }

}

function UpdateOtherQuestionResponsesContainer($section, question_id, answer_index) {
    $($section).find('.other-question-container .other-content-row').remove();
    let section_id = $($section).attr('id');
    for (let otherIter = 0; otherIter < _questions_data.other_questions.length; otherIter++) {
        if (_questions_data.other_questions[otherIter].id == section_id) {

            let current_other_question = _questions_data.other_questions[otherIter];
            if (current_other_question.responses[question_id] != undefined && current_other_question.responses[question_id][answer_index] != undefined) {
                for (let answer_iter = 0; answer_iter < current_other_question.responses[question_id][answer_index].length; answer_iter++) {

                    let $new_just_answer_row = $('<div/>', {
                        class: 'other-content-row',
                        text: current_other_question.responses[question_id][answer_index][answer_iter]
                    });
                    $($section).find('.other-question-container').append($new_just_answer_row);
                }
            }
        }
    }
}

function UpdateJustQuestionAnswerTabs(question_id, data) {
    $(_$panel).find('.justifications-section .answers-tabs-row .answer-tab').remove();
    $(_$panel).find('.justifications-container .justification-content-row').remove();
    //update just question answers tabs
    for (let question_iter = 0; question_iter < data.questions.length; question_iter++) {
        if (data.questions[question_iter].id == question_id) {
            for (let answer_iter = 0; answer_iter < data.questions[question_iter].answers.length; answer_iter++) {
                let $new_answer_row = $('<div/>', {
                    class: 'answer-tab',
                    'data-index': answer_iter + 1,
                    text: String.fromCharCode(65 + answer_iter)
                });
                $(_$panel).find('.justifications-section .answers-tabs-row').append($new_answer_row);
            }
        }
    }
}

function UpdateOtherQuestionAnswerTabs(acc_question_id, question_id, data) {
    $(_$panel).find('#' + acc_question_id + ' .answers-tabs-row .answer-tab').remove();
    $(_$panel).find('#' + acc_question_id + ' .other-content-row').remove();
    //update just question answers tabs
    for (let question_iter = 0; question_iter < data.questions.length; question_iter++) {
        if (data.questions[question_iter].id == question_id) {
            for (let answer_iter = 0; answer_iter < data.questions[question_iter].answers.length; answer_iter++) {
                let $new_answer_row = $('<div/>', {
                    class: 'answer-tab',
                    'data-index': answer_iter + 1,
                    text: String.fromCharCode(65 + answer_iter)
                });
                $(_$panel).find('#' + acc_question_id + ' .answers-tabs-row').append($new_answer_row);
            }
        }
    }
}

function UpdateMonitoringInfo(data) {

    console.log('updating the data...');

    let current_phase = $(_$panel).attr('data-quiz-phase');

    //updating phase
    if (current_phase != data.phase) {
        //we have different phase , we need to update
        $(_$panel).attr('data-quiz-phase', data.phase);
        current_phase = data.phase;
        //if the phase change has been done manually we already displayed a notification message. If it has been automatically then we need to show a notification
        if (_show_phase_change_notifcation == true) {


            let phase_word = data.phase == 2 ? 'revision' : data.phase == 3 ? 'answers reveal' : 'end';
            ShowGlobalMessage('The quiz is now on the ' + phase_word + ' phase.', 1);
        } else {
            _show_phase_change_notifcation = false;
        }
        if (current_phase == 2) {
            //if we are on the revision phase we disable the ENABLE REVISION button
            $('.modify-quiz-phase').attr('disabled', true);
        }


    }
    //end of phase updating

    //update active participants
    $(_$panel).attr("data-quiz-participants-count", data.active_participants_count);
    $(_$panel).find(".session-active-participants").text('Active Participants : ' + data.active_participants_count + ' (' +
        +data.active_anon_participants_count + ')');
    //update enrolled participants
    $(_$panel).find(".session-enrolled-participants").text('Enrolled Participants : ' + data.enrolled_participants_count);
    //update timeline if exists
    if (data.scheduling != null) {
        UpdateTimelineDates(data.scheduling.init_start, data.scheduling.init_end,
            data.scheduling.rev_start, data.scheduling.rev_end,
            data.scheduling.ans_start, data.scheduling.ans_end);
    }
    //end of updating timeline
    //update quiz progress
    DrawProgressChart('_progress_initial_chart', 1, data, true);
    DrawProgressChart('_progress_revision_chart', 2, data, true);

    UpdateQuizPercentages(data);
    UpdateJustQuestion(data);
    UpdateConfidenceQuestion(data);
    UpdateOutsideOtherTextQuestions(data);

    //update prep responses
    _questions_data.prep_question = data.prep_question;
    DrawPieChart('preparation-pie-chart', _questions_data.prep_question, true);
    // update other questions
    _questions_data.other_questions = data.other_questions;
    if (_questions_data.other_questions != undefined) {
        for (let questionIter = 0; questionIter < _questions_data.other_questions.length; questionIter++) {
            switch (_questions_data.other_questions[questionIter].view_type) {
                case "outside-rating":
                    for (let chartIter = 0; chartIter < _other_questions_pie_charts.length; chartIter++) {
                        let question_data = null;
                        if (_questions_data.other_questions[questionIter].id == _other_questions_pie_charts[chartIter].id) {
                            question_data = _questions_data.other_questions[questionIter].responses;
                            DrawPieChart('other-question-pie-chart' + _other_questions_pie_charts[chartIter].id, question_data, true);

                        }
                    }
                    break;
                case "outside-text":
                    $('#' + _questions_data.other_questions[questionIter].id).find('.other-content-row').remove();
                    let responses = _questions_data.other_questions[questionIter].responses;
                    for (let iter = 0; iter < responses.length; iter++) {
                        let $row = $('<div/>', {
                            class: 'other-content-row',
                            text: responses[iter]
                        });
                        $('#' + _questions_data.other_questions[questionIter].id).find('.other-question-container').append($row);
                    }
                    break;
                case "inside-rating" :
                    let data = _questions_data.other_questions[questionIter].responses;
                    for (let phase_iter = 1; phase_iter <= 2; phase_iter++) {
                        if (data != undefined && data[phase_iter] != undefined) {
                            let resp_data = Object.keys(data[phase_iter]).map(function (key) {
                                //convert object to array and also calculate average
                                return [key, data[phase_iter][key]];
                            });

                            for (let question_iter = 0; question_iter < resp_data.length; question_iter++) {
                                if (resp_data[question_iter] != undefined) {
                                    let $question_row = $('#' + _questions_data.other_questions[questionIter].id + ' .conf-phase-' + phase_iter + ' .question-row[data-question-id=' + resp_data[question_iter][0] + ']');
                                    $($question_row).find('.all-conf-answers').text(resp_data[question_iter][1].all_average.toFixed(2));
                                    $($question_row).find('.correct-conf-answers').text(resp_data[question_iter][1].correct_average.toFixed(2));
                                    $($question_row).find('.incorrect-conf-answers').text(resp_data[question_iter][1].incorrect_average.toFixed(2));
                                }
                            }
                        }
                    }
                    break;

            }

        }
    }
}

function highlightPercentages() {
    $(_$panel).find('.percentage-section .row').each(function () {


        let right_index = $(this).attr('data-right-answer') - 1;
        let max_percentage = -10;
        let max_index = null;

        $(this).find('.question-answer-row').each(function () {
            //reset previous css
            $(this).css('border-color', 'white')
                .css('background-color', 'white');

            let answer_percentage = parseFloat($(this).find('.answers-count').text());

            if (answer_percentage > max_percentage) {
                max_index = $(this).index();
                max_percentage = answer_percentage;
            }
        });

        if (max_percentage > 0) {
            //we have a maximum
            //highlight the most popular answers
            $(this).find('.question-answer-row').each(function () {
                if (parseFloat($(this).find('.answers-count').text()) == max_percentage) {
                    $(this).css("background-color", '#10961870');
                }
            });

            $(this).find('.question-answer-row').eq(right_index).css('border-color', 'green');

        } else {
            //highlight the  right
            $(this).find('.question-answer-row').eq(right_index).css('border-color', 'green');

        }
    });
}

function DrawPieChart(identifier, data, is_update) {
    let total = 0;
    let total_count = 0;
    if (data !== undefined && data !== null) {
        data = Object.keys(data).map(function (key) {
            //convert object to array and also calculate average
            total_count += data[key];
            total += key * data[key];
            return [key, data[key]];
        });
    } else {
        data = [];
    }
    data.unshift(['Response', 'Count']);

    let chart_data = google.visualization.arrayToDataTable(data);
    let total_average = (total / total_count).toFixed(2);
    if (isNaN(total_average)) {
        total_average = '';
    }
    let options = {
        animation: {
            duration: 1000,
            easing: 'out',
        },
        title: 'Average : ' + total_average,
        'width': 400,
        'height': 220,
        chartArea: {left: 46, top: 20, 'height': '100%'},
        titleTextStyle: {
            fontSize: 15,
            bold: false,
        }
    };

    if (identifier == 'preparation-pie-chart') {
        //deal with prep question pie chart.
        if (is_update) {
            if (_prep_pie_chart !== null) {
                _prep_pie_chart.draw(chart_data, options);
            }
        } else {
            _prep_pie_chart = new google.visualization.PieChart(document.getElementById(identifier));
            _prep_pie_chart.draw(chart_data, options);
        }

    } else {
        //deal with the other questions pie charts
        let current_pie_chart = null;
        for (let iterChart = 0; iterChart < _other_questions_pie_charts.length; iterChart++) {
            if (_other_questions_pie_charts[iterChart].id == identifier.replace(/[^\d.]/g, '')) {
                current_pie_chart = _other_questions_pie_charts[iterChart];
            }
        }

        if (is_update) {
            if (current_pie_chart !== null && current_pie_chart.chart !== null) {
                current_pie_chart.chart.draw(chart_data, options);
            }
        } else {

            current_pie_chart = new google.visualization.PieChart(document.getElementById(identifier));
            current_pie_chart.draw(chart_data, options);
            _other_questions_pie_charts.push({chart: current_pie_chart, id: identifier.replace(/[^\d.]/g, '')});
        }

    }
}

function DrawProgressChart(identifier, phase, questions_data, is_update) {
    // Create the data table.
    let data = new google.visualization.DataTable();

    let participants_count = $(_$panel).attr("data-quiz-participants-count");
    data.addColumn('string', 'Question');
    data.addColumn('number', 'responses');
    data.addColumn({type: 'string', role: 'annotation'});
    data.addColumn({type: 'string', role: 'tooltip'}); //
    let rows = [];
    let answer_iter = null;
    let percentage = null;
    let current_question = null;
    let current_answer = null;
    let total_responses_for_current_question = 0;

    for (let question_iter = 0; question_iter < questions_data.questions.length; question_iter++) {
        current_question = questions_data.questions[question_iter];
        total_responses_for_current_question = 0;
        if (current_question['responses'] != undefined && current_question['responses'][phase].answers_values != undefined) {
            current_question['responses'][phase].answers_values = Object.keys(current_question['responses'][phase].answers_values).map(function (key) {
                return current_question['responses'][phase].answers_values[key];
            });

            for (answer_iter = 0; answer_iter < current_question['responses'][phase].answers_values.length; answer_iter++) {
                current_answer = current_question['responses'][phase].answers_values[answer_iter];
                total_responses_for_current_question += current_answer;
            }
        }
        //TODO this is temporary dont let it like this !!!
        if (participants_count == 0) {
            participants_count = 30;
        }
        percentage = total_responses_for_current_question / participants_count * 100;
        rows.push(['Q ' + (question_iter + 1), percentage, percentage.toFixed(0) + '%', total_responses_for_current_question + ' responses']);
    }

    data.addRows(rows);

    let title = '';
    if (phase == 1) {
        title = 'Initial phase';
    } else if (phase == 2) {
        title = 'Revision phase';
    }
    // Set chart options
    let options = {
        animation: {
            duration: 1000,
            easing: 'out',
        },
        'title': title,
        'width': 600,
        'height': 20 * questions_data.questions.length,
        legend: 'none',
        colors: ['#2bbbad'],
        //chartArea: {left: 0},

        annotations: {
            textStyle: {fontSize: 12, color: 'black'}
        },
        titleTextStyle: {
            fontSize: 15,
            bold: false
        },
        hAxis: {
            viewWindow: {
                min: 0,
                max: 100
            },
            ticks: [0, 25, 50, 75, 100] // display labels every 25
        },
        vAxis: {
            textStyle: {
                fontSize: 10
            }
        }
    };
    if (is_update == false) {
        // Instantiate and draw our chart, passing in some options.
        if (phase == 1) {
            _progress_initial_chart = new google.visualization.BarChart(document.getElementById(identifier));
            _progress_initial_chart.draw(data, options);
        } else if (phase == 2) {
            _progress_revision_chart = new google.visualization.BarChart(document.getElementById(identifier));
            _progress_revision_chart.draw(data, options);
        }
    } else {
        if (phase == 1) {
            _progress_initial_chart.draw(data, options);
        } else if (phase == 2) {
            _progress_revision_chart.draw(data, options);
        }
    }
}

function ModifyQuizStatus(quiz_id, status) {

    $('.modify-quiz-status').addClass('disabled');
    $.post({
        url: '/quiz/modify-quiz-status',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {quiz_id: quiz_id, quiz_status: status}
    }).done(function (data) {

        let result = JSON.parse(data);
        console.log("Success: " + data);

        if (result.status == true) {
            sessionStorage.setItem("success-message", result.message);
            clearInterval(updateDataIntervalId);

            location.reload();
            $(_$panel).attr('data-quiz-status', status);
            $(_$panel).find('.modify-quiz-status').text(result.quiz_status == 1 ? 'stop quiz' : 'start quiz');

        } else {
            ShowGlobalMessage(result.message, 2);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log("Error " + textStatus);
        $('.modify-quiz-status').removeClass('disabled');
        let message = status == 1 ? 'An error occurred while starting the quiz.' : 'An error occurred while stopping the quiz.';
        ShowGlobalMessage(message, 2);

    }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {

    });
}

function ModifyQuizRevealAnswersStatus(quiz_id, status) {

    $('.modify-quiz-reveal-answers').addClass('pending');
    $.post({
        url: '/quiz/modify-quiz-reveal-answers',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {quiz_id: quiz_id, quiz_reveal_answers_status: status}
    }).done(function (data) {

        let result = JSON.parse(data);
        console.log("Success: " + data);

        if (result.status == true) {
            if (status == 1) {
                //we started the last phase - the answers reveal phase
                $('.modify-quiz-reveal-answers').text('hide answers');
                //if we have time scheduling we have to update.
                if ($('.timeline-tab.ans-tab').length > 0) {
                    $('.timeline-tab.ans .timeline-tab-value-start').text(moment().format('D MMM HH:mm'));
                }
                $(_$panel).attr('data-quiz-phase', 3);
                $('.modify-quiz-reveal-answers').attr('disabled', true);
                $('.modify-quiz-status').attr('disabled', false);
                //enable the quiz presentation button
                $('.quiz-presentation-btn').removeClass('disabled');

            }

            ShowGlobalMessage('Answers are now revealed.', 1);
            _show_phase_change_notifcation = false;
        } else {
            ShowGlobalMessage('An error occurred while changing the phase to the reveal answers phase.', 2);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log("Error " + textStatus);
    }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
        $('.modify-quiz-reveal-answers').removeClass('pending');
    });
}

function ModifyQuizPhase(quiz_id, phase) {

    $('.modify-quiz-phase').addClass('disabled');

    $.post({
        url: '/quiz/modify-quiz-phase',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {quiz_id: quiz_id, quiz_phase: phase}
    }).done(function (data) {

        let result = JSON.parse(data);
        console.log("Success: " + data);

        if (result.status == true) {

            ShowGlobalMessage('The quiz is now on the revision phase.', 1);
            _show_phase_change_notifcation = false;
            //if there is scheduling update the start date for the revision phase
            if ($('.timeline-tab.rev-tab').length > 0) {
                $('.timeline-tab.rev-tab .timeline-tab-value-start').text(moment().format('D MMM HH:mm'));
            }
            $(_$panel).attr('data-quiz-phase', phase);
            $('.modify-quiz-reveal-answers').attr('disabled', false);
        } else {
            $('.modify-quiz-phase').removeClass('disabled');

            ShowGlobalMessage('An error occurred while changing the phase of the quiz.', 2);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log("Error " + textStatus);
    }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
        // alert("complete");
    });
}

function ExtendQuizScheduling(quiz_id, phase, minutes_amount) {
    $('#edit-quiz-scheduling-modal .confirm-btn').addClass('pending');

    $.post({
        url: '/quiz/extend-quiz-scheduling',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {quiz_id: quiz_id, phase: phase, minutes_amount: minutes_amount}
    }).done(function (data) {

        let result = JSON.parse(data);
        console.log("Success: " + data);
        let phase_word = phase == 1 ? 'initial' : phase == 2 ? 'revision' : 'answers reveal';

        if (result.status == true) {

            $('#edit-quiz-scheduling-modal').modal('close');
            ShowGlobalMessage('The ' + phase_word + ' has been extended by ' + minutes_amount + ' minutes.', 1);
            if (result.current_values !== undefined) {

                UpdateTimelineDates(result.current_values.init_start, result.current_values.init_end,
                    result.current_values.rev_start, result.current_values.rev_end,
                    result.current_values.ans_start, result.current_values.ans_end);
            }

        } else {
            //something went wrong
            console.log(result.message);
            ShowGlobalMessage('An error occurred while extending the ' + phase_word + ' phase.', 2);
        }
        ResetQuizSchedulingModal();
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log("Error " + textStatus);
    }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
        $('#edit-quiz-scheduling-modal .confirm-btn').removeClass('pending');
    });

}

function UpdateTimelineDates(init_start, init_end, rev_start, rev_end, ans_start, ans_end) {
    let $section = $('.quiz-scheduling-section');
    //update initial phase
    $($section).find('.init-tab .timeline-tab-value-start').text(init_start);
    $($section).find('.init-tab .timeline-tab-value-end').text(init_end);
    //update revision phase
    $($section).find('.rev-tab .timeline-tab-value-start').text(rev_start);
    $($section).find('.rev-tab .timeline-tab-value-end').text(rev_end);
    //update answers reveal phase
    $($section).find('.ans-tab .timeline-tab-value-start').text(ans_start);
    $($section).find('.ans-tab .timeline-tab-value-end').text(ans_end);
}

function ResetQuizSchedulingModal() {
    $('#edit-quiz-scheduling-modal').find('.extension-amount-select option:first-child').attr('selected', true);
}

function ValidateQuizSchedulingModal() {
    let is_valid = true;
    let message = '';

    let minutes_amount = $('#edit-quiz-scheduling-modal').find('.extension-amount-select option:selected').val();

    if (minutes_amount == '') {
        is_valid = false;
        $('#edit-quiz-scheduling-modal').find('.extension-amount-select').addClass('invalid-field');
        message += 'A valid number of minutes is needed.';

    } else {
        $('#edit-quiz-scheduling-modal').find('.extension-amount-select').addClass('invalid-field');
    }
    if (message !== '') {
        ShowGlobalMessage(message, 2);
    }
    return is_valid;
}
