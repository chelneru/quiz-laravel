$(document).ready(function () {
    $('.tooltipped').tooltip();
    $('.dropdown-trigger').dropdown({ constrainWidth: false });

    $('select').formSelect();

    console.log('document loaded');

    $('.session-select').on('change', function () {
        let quiz_id = $('.quiz-result-page').attr('data-quiz-id');

        let progress_id = $(this).find('option:selected').attr('value');
        window.location = '/quiz-result/' + quiz_id + '/' + progress_id;
    });
});