$(document).ready(function () {
    $('.modal').modal({onOpenStart:function () {
            $('.add-quizzes-btn').addClass('disabled');

        }});
    $('.tooltipped').tooltip();


    $('#add_quizzes').on('click', function () {
        let class_id = $('.page').attr('data-class-id');
        RetrieveTeacherQuizzes(class_id);

    });

    $(document).on('change', '.available-quizzes-table input[type=checkbox]',function () {
        if ($('input[name=new_quizzes]:checked').length > 0) {
            //activate the submit button
            $('.add-quizzes-btn').removeClass('disabled');

        }
        else {
            //disable the submit button
            $('.add-quizzes-btn').addClass('disabled');
        }
    });

    $('.add-quizzes-btn').on('click',function () {
        $('.add-quizzes-btn').addClass('pending');
        $('.available-quizzes-table input[type=checkbox]').prop('disabled',true);
        $('input[name=new_quizzes]:checked').each(function () {
            let $quiz_input = $('<input>');
            $($quiz_input).attr('name','quizzes_ids[]');
            $($quiz_input).attr('type','hidden');
            $($quiz_input).val($(this).closest('tr').attr('id'));
            $('form.existing-quizzes-form').append($quiz_input);
        });
        $('.add-quizzes-btn').removeClass('pending');

    });

});


function RetrieveTeacherQuizzes(class_id) {
    $.post({
        url: '/get-quizzes-list',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            class_id: class_id
        }
    }).done(function (data) {
        data = JSON.parse(data);
        $('#add_existing_quiz_modal .available-quizzes-table tbody tr').remove();
        $('#add_existing_quiz_modal .available-quizzes-table .add-quizzes-btn').addClass('disabled');
        for (let quizIter = 0; quizIter< data.quizzes.length; quizIter++) {
            let $new_row = $('<tr>');
            $($new_row).attr("id",data.quizzes[quizIter].id);
            let $checkbox_td = $('<td>');


            let $checkbox_label = $('<label>');
            let $checkbox_span = $('<span>');
            $($checkbox_span).text('test');

            let $checkbox_input = $('<input>');
            $($checkbox_input).attr('type','checkbox');
            $($checkbox_input).attr('name','new_quizzes');
            $($checkbox_input).addClass('filled-in');

            $($checkbox_label).append($checkbox_input,$checkbox_span);
            $($checkbox_td).append($checkbox_label);


            $($checkbox_span).text(data.quizzes[quizIter].title.trunc(100));
            $($checkbox_span).addClass('tooltipped');
            $($checkbox_span).attr('data-position','top');
            $($checkbox_span).attr('data-tooltip',data.quizzes[quizIter].description);
            $($new_row).append($checkbox_td);
            $('#add_existing_quiz_modal .available-quizzes-table tbody').append($new_row);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log("Error");
    }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
        // alert("complete");
    });
}
