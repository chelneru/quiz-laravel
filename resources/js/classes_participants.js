$(document).ready(function () {
    $('.modal').modal();
    $('.join-class-btn').on('click', function () {
        $(this).addClass('pending');
        let class_code = $('input.class_code').val();

        if (class_code == '') {
            $('input.class_code').addClass('invalid-field');
            $(this).removeClass('pending');

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
                    sessionStorage.setItem("success-message", "You joined the class. ");
                    window.location.reload();
                } else {
                    $('.join-class-btn').removeClass('pending');
                    if(result.message == 'no class found') {
                        ShowGlobalMessage('There is no class with this code.', 2);
                    }
                    else {
                        ShowGlobalMessage('An error occurred while joining the class.', 2);

                    }
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log("Error");
            }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
            });
        }
    });

});
