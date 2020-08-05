let showed_notif = false;


$(document).ready(function () {
    $('.modal').modal();

    toastr.options ={
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-center",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": 4000,
        "extendedTimeOut": 0,
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "tapToDismiss": false
    };
    $(".loading-spinner").fadeOut();
    $("html").css('overflow-y', 'unset');
    $('.sidenav').sidenav({edge: 'right'});
    $('.collapsible').collapsible();
    //check if there any any notification message that needs to be shown
    if ($(".notif").length > 0 && showed_notif == false) {
        if ($('.notif').hasClass('success')) {
            ShowGlobalMessage($('.notif').text(), 1);
        } else if ($('.notif').hasClass('fail')) {
            ShowGlobalMessage($('.notif').text(), 2);
        }
        showed_notif = true;

    }
    let succes_message = sessionStorage.getItem("success-message");
    let fail_message = sessionStorage.getItem("fail-message");
    if(succes_message !== null) {
        ShowGlobalMessage(succes_message, 1);
        sessionStorage.removeItem("success-message");
    }
    if(fail_message !== null) {
        ShowGlobalMessage(fail_message, 2);
        sessionStorage.removeItem("fail-message");
    }

    $('.action-buttons-space').css('width', $('.container').css('width'));
    $('.mobile-toggle').on('click', function () {

    });
    $(".navbar-dropdown-trigger").dropdown({
        coverTrigger: false,
        alignment: 'left', // Displays dropdown with edge aligned to the left of button,
        constrainWidth: false
    });

    $('#navbarDropdown').on('click', function () {
        $('.dropdown-menu-left').css('display', 'block');
    });

    $(document).on('input', 'input[maxlength]', function () {
        let $input = $(this);
        $(this).next('.input-counter').text($($input).val().length + '/' + $($input).attr('maxlength'));
    });
    $(document).on('keyup', 'input[maxlength]', function () {
        let $input = $(this);
        $(this).next('.input-counter').text($($input).val().length + '/' + $($input).attr('maxlength'));
    });
    $(document).on('focus', 'input[maxlength]', function () {
        if ($(this).val() !== undefined) {
            $(this).next('.input-counter').text($(this).val().length + '/' + $(this).attr('maxlength'));

        }
    });

    $(document).mouseup(function (e) {
        let $menu = $('.dropdown-menu-left');
        if (!$menu.is(e.target)
            && $menu.has(e.target).length === 0) {
            $menu.css('display', 'none');
        }
    });
});

String.prototype.trunc = String.prototype.trunc ||
    function(n){
        return (this.length > n) ? this.substr(0, n-1) + '...' : this;
    };
window.error_message_timeout = null;

window.validateEmail = function (email) {
    let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
};
/**
 * function to display a dialog at the top of the window
 * @string param message text to be displayed
 * @int param status 1-success green box, 2-fail orange box
 * @constructor
 */
window.ShowGlobalMessage = function (message, status, confirm) {


    if (confirm != undefined) {

        if (status == 1) {
            toastr.success(message);

        } else {
            toastr.options.closeButton = true;
            toastr.options.timeOut = 0;
            toastr.warning(message);
        }
    } else {
        if (status == 1) {
            toastr.success(message);
        } else {
            toastr.options.closeButton = true;
            toastr.options.timeOut = 0;
            toastr.warning(message);
        }
    }

    clearTimeout(error_message_timeout);
    window.error_message_timeout = setTimeout(function () {
        $('.global-message-div').animate({opacity: 0})
            .addClass('hidden')
            .removeClass(status == 1 ? 'success' : 'fail');
    }, 4000)
};

window.ClearGlobalMessages = function() {
    toastr.clear();

};
String.prototype.ShortenedString = function (length,trailing_points) {
    if(this.length > length && trailing_points === true) {
        return this.substr(0,length)+'...';
    }
    else {
        return this.substr(0,length)
    }

};
