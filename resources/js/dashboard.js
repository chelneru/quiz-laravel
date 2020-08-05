$(document).ready(function () {

    $('.tooltipped').tooltip();
    $('.modal').modal();

    if ($('.participant-page.dashboard-page').length > 0) {
        editor = new Quill('#editor', {
            "modules": {
                // "toolbar": false
            },
            readOnly: true
        });
    }
    $('.participants-table .active-list,' +
        '.participants-table .inactive-list').on("click", function () {
        let active_filter = $(this).hasClass('active-list') ? 2 : 1;
        $('#teacher-participants-list-modal tbody tr').remove();
        $.post({
            url: '/get-participant-list',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                status_filter: active_filter
            }
        }).done(function (data) {
            data = JSON.parse(data);
            if (data.users.length == 0) {
                let $new_row = $('<tr>');
                let $name_td = $('<td>');
                let row_string = active_filter == 2 ? 'No active participants.':'No inactive participants.';
                $($name_td)
                    .css('text-align','center')
                    .text(row_string);
                $($new_row).append($name_td);
                $('#teacher-participants-list-modal .users-table tbody').append($new_row);
            } else {
                for (let userIter = 0; userIter < data.users.length; userIter++) {
                    let $new_row = $('<tr>');
                    let $name_td = $('<td>');
                    $($name_td).text(data.users[userIter].name);
                    $($new_row).append($name_td);
                    $('#teacher-participants-list-modal .users-table tbody').append($new_row);
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log("Error");
        }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
            // alert("complete");
        });
    });

    $('.join-class-btn').on('click', function () {
        $(this).addClass('pending');
        let class_code = $('input.class_code').val();

        if (class_code == '') {
            $('input.class_code').addClass('invalid-field');
            $(this).removeClass('pending');
            ShowGlobalMessage('The class code is missing.', 2);
        } else {
            $('input.class_code').removeClass('invalid-field');

            $.post({
                url: '/class/join-class',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {class_code: class_code}
            }).done(function (data) {

                let result = JSON.parse(data);
                if (result.status == true) {
                    window.location.reload();
                } else {
                    $('.join-class-btn').removeClass('pending');
                    if (result.message == 'no class found') {
                        ShowGlobalMessage('There is no class with this code.', 2);
                    }
                    if (result.message == 'already enrolled') {
                        ShowGlobalMessage('You are already enrolled to this class.', 2);
                        $('#join_class_modal').modal('close');

                    } else {
                        ShowGlobalMessage('An error occurred while joining the class.', 2);

                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log("Error");
            }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
            });
        }
    });

    $('.start-quiz-btn').on('click', function () {
        let quiz_id = $('#quiz_presentation').attr('data-quiz-id');
        $.post({
            url: '/quiz/start-quiz',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {quiz_id: quiz_id, json_response: true}
        }).done(function (data) {

            let result = JSON.parse(data);
            if (result.status == true) {
                if (result.start == true) {
                    window.location = '/quiz/' + quiz_id;
                } else {
                    ShowGlobalMessage('Quiz has not started yet');
                }
            } else {
                ShowGlobalMessage(result.message, 2);
                $('.join-class-btn').css('pointer-events', 'unset');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log("Error");
        }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
        });

    });

    $('#quiz_presentation').find('.modal-close').on('click', function () {
        $(this).closest('#quiz_presentation').attr('data-quiz-id', '');
    });

    $('.get-quiz-info').on('click', function () {
        let quiz_id = $(this).attr('id');

        let has_progress = $(this).attr('data-has-progress');
        let revealed_answers = $(this).attr('data-has-answers-revealed');
        let $quiz_presentation_modal = $('#quiz_presentation');
        $($quiz_presentation_modal).find('.quiz-title').text('');
        $($quiz_presentation_modal).find('.view-results-btn').remove();
        $($quiz_presentation_modal).find('.quiz-description').text('');
        $($quiz_presentation_modal).find('.quiz-message-title').text('');
        $($quiz_presentation_modal).attr('data-quiz-id', quiz_id);
        $($quiz_presentation_modal).find('.view-results-btn').attr('href', '/quiz-result/' + quiz_id);
        $($quiz_presentation_modal).find('.start-quiz-btn').css('display', 'none');

        if (has_progress == 1 && revealed_answers == 1) {
            let $results_button = $('<a/>', {
                class: 'btn view-results-btn',
                text: 'view past results'
            });

            $($results_button).attr('href', '/quiz-result/' + quiz_id);
            $($quiz_presentation_modal).find('.modal-buttons').prepend($results_button);
        }


        editor.setContents([{insert: '\n'}]);
        $.post({
            url: '/quiz/get-dashboard-quiz-info',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {quiz_id: quiz_id}
        }).done(function (data) {

            let result = JSON.parse(data);

            $($quiz_presentation_modal).find('.quiz-title').text(result.title);
            $($quiz_presentation_modal).find('.quiz-description').text(result.description);
            $($quiz_presentation_modal).find('.quiz-message-title').text(result.message_title);
            if (result.ongoing_progress == null) {

                $($quiz_presentation_modal).find('.start-quiz-btn').text('start quiz');
                if ((result.participation_limit > 0 && result.past_completed_participations >= result.participation_limit) || result.status === false) {  //TODO investigate if this is correct
                    $($quiz_presentation_modal).find('.start-quiz-btn').css('display', 'none');
                } else {
                    $($quiz_presentation_modal).find('.start-quiz-btn').css('display', 'block');
                }
            } else {
                $($quiz_presentation_modal).find('.start-quiz-btn').css('display', 'block');

                $($quiz_presentation_modal).find('.start-quiz-btn').text('resume quiz');
            }
            if (result.message != '' && result.message !== undefined) {
                editor.setContents(JSON.parse(result.message));
            }
            if (result.status == true) {
                $('.start-quiz-btn').css('pointer-events', 'unset').removeClass('grey');
                $('#quiz_presentation').modal('open');

            } else {
                ShowGlobalMessage('The ' + result.title + ' quiz is closed.', 2);
                $('.start-quiz-btn').css('pointer-events', 'none').addClass('grey');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log("Error", textStatus);
        }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
        });
    });
});

