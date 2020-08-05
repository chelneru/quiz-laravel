let editor = null;
$(document).ready(function () {
    $('.dropdown-trigger').dropdown({constrainWidth: false});

    console.log('page loaded');
    $('select').formSelect();

    let toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
        ['blockquote', 'code-block'],
        ['link'],
        [{ 'header': 1 }, { 'header': 2 }],               // custom button values
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
        [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
        [{ 'direction': 'rtl' }],                         // text direction

        [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

        [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
        [{ 'font': [] }],
        [{ 'align': [] }],

        ['clean']                                         // remove formatting button
    ];

    editor = new Quill('#editor', {
        modules: { toolbar: toolbarOptions},
        theme: 'snow'
    });
    if($('.existing-editor-content').text() != '') {
    editor.setContents(JSON.parse($('.existing-editor-content').text()));
    }
    $('.save-additional-messages-btn').on('click',function () {
        $(this).addClass('pending');
        let quiz_id = $('.quiz-additional-messages-page').attr('id');
        let message_delta = editor.getContents();
        console.log(message_delta);

        $.post({
            url: '/quiz/update-additional-message',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                quiz_id: quiz_id,
                message: JSON.stringify(message_delta),
                message_title: $("input[name=message_title]").val()
            }
        }).done(function (data) {

         let result = JSON.parse(data);
            console.log("Success: " + data);

            if (result.status == true) {

                if($('.quiz-additional-messages-page').attr('data-interaction-type')=='create'){
                    window.location = '/quiz/scheduling/'+quiz_id;
                } else {
                    window.location = '/quiz/quiz-info/' + quiz_id;
                }

            } else {
                $('.save-additional-messages-btn').removeClass('pending');
                ShowGlobalMessage('An error occurred while updating the disclaimer message.',2);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log("Error");
        }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
            // alert("complete");
        });
    });
});
