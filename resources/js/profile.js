$(document).ready(function () {
    $('.modal').modal();

    $('.delete-button').on('click', function () {
        showPopup();
    });

    $('.cancel-button').on('click', function () {
        hidePopup();
    });

});

function showPopup() {
    $('.popup-overlay').fadeIn();
    $('.popup-panel').fadeIn();
}

function hidePopup() {
    $('.popup-overlay').fadeOut();
    $('.popup-panel').fadeOut();
}

