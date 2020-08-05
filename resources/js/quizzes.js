let quiz_id = null;
$(document).ready(function () {
    $('.dropdown-trigger').dropdown({constrainWidth: false});
    $('.modal').modal();

    $('select').formSelect();

    $('.delete-quiz-button').on('click', function () {
        quiz_id = $(this).closest('tr').attr('id');

        let delelete_quiz_modal_elem = document.getElementById('quiz-delete-confirm-modal');
        let modal_instance = M.Modal.getInstance(delelete_quiz_modal_elem);
        modal_instance.open();
    });
    $('.class-filter').on('change', function () {
        filter_quizzes('class', $(this).find('option:selected').val()
        );
    });

    $('.confirm-btn').on('click', function () {
        if ($('.quiz-details-page').length > 0) {
            //we are in quiz details page, we get the quiz id from the page
            quiz_id = $('.quiz-details-page').attr('data-quiz-id');
        }
        //the quiz id is already set from the click event of .delete-quiz-button
        DeleteQuiz(quiz_id);
    });
    $('.copy-direct-link').on('click', function () {
        var range = document.createRange();
        range.selectNode(document.getElementById("quiz-link"));
        window.getSelection().removeAllRanges(); // clear current selection
        window.getSelection().addRange(range); // to select text
        document.execCommand("copy");
        window.getSelection().removeAllRanges();// to deselect
    })
});

function filter_quizzes(filter_name, filter_id) {
    window.location = '/quizzes?' + filter_name + '=' + filter_id;
}

function DeleteQuiz(quiz_id) {
    let location = '/quiz/delete-quiz';
    $.post({
        url: location,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {quiz_id: quiz_id}
    }).done(function (data) {
        console.log("Success: " + data);

        let result = JSON.parse(data);
        if (result.status == true) {
            sessionStorage.setItem("success-message", 'The quiz has been removed successfully.');
            window.location = '/quizzes';

        } else {
            ShowGlobalMessage('An error occurred during the quiz removal.', 2);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log("Error");
    }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
        // alert("complete");
    });
}
