$(document).ready(function () {
console.log('document ready');
$('.validate-btn').on('click',function () {
    let $row = $(this).closest('tr');
    let user_id = $($row).attr('id');
    $.post({
        url: '/validate-teacher-action',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            user_id: user_id
        }
    }).done(function (data) {
        data = JSON.parse(data);
        if(data.status == true) {
            ShowGlobalMessage('Teacher has been validated successfully.', 1);
            $($row).remove();
        }
        else {
            ShowGlobalMessage('An error occurred trying to validate the teacher.', 2);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log("Error");
    }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {// alert("complete");
    });
});

});
