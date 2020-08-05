let $query_form = null;


$(document).ready(function () {
    $query_form = $('#query-admin-quizzes-form');
    $('select').formSelect();
    $('.modal').modal();
    $('.dropdown-trigger').dropdown({constrainWidth: false});
    $('#role-dropdown').on('change', function () {
        if ($($query_form).find('input[name="role_filter"]').length == 0) {
            $($query_form).append('<input type="hidden" name="role_filter"/>');
        }
        $($query_form).find('input[name="role_filter"]').val($(this).find('option:selected').val());
        $($query_form).submit();
    });
    $($query_form).on('submit', function () {
        $(this).find('input[name=user_filter]').val($('#autocomplete-user-input').val());
    });
    $('.clear-field').on('click',function () {
        let $input = $(this).siblings('input');
        if($($input[0]).attr('id') == 'autocomplete-user-input') {
            $(this).siblings('input').val('');
            ClearUserField();
        }
    });
    $('.admin-quizzes-page .quizzes-table th').click(function () {

        let prop = $(this).attr('data-function').split('-');
        let dir = prop[1];
        let sort_by = prop[2];

        let new_dir = dir == 'asc' ? 'desc' : 'asc';
        $(this).attr('data-function', 'sort-' + new_dir + '-' + sort_by);

        let icon_function = $(this).attr('data-function');
        if ($($query_form).find('input[name="order_by_filter"]').length == 0) {
            $($query_form).append('<input type="hidden" name="order_by_filter"/>');
        }
        if ($($query_form).find('input[name="order_dir_filter"]').length == 0) {
            $($query_form).append('<input type="hidden" name="order_dir_filter"/>');

        }
        console.log(icon_function);

        switch (icon_function) {
            case 'sort-asc-name':
                $($query_form).find('input[name="order_by_filter"]').val('name');
                $($query_form).find('input[name="order_dir_filter"]').val('asc');
                break;
            case 'sort-desc-name':
                $($query_form).find('input[name="order_by_filter"]').val('name');
                $($query_form).find('input[name="order_dir_filter"]').val('desc');
                break;
            case 'sort-asc-author':
                $($query_form).find('input[name="order_by_filter"]').val('author');
                $($query_form).find('input[name="order_dir_filter"]').val('asc');
                break;
            case 'sort-desc-author':
                $($query_form).find('input[name="order_by_filter"]').val('author');
                $($query_form).find('input[name="order_dir_filter"]').val('desc');
                break;
            case 'sort-asc-status':
                $($query_form).find('input[name="order_by_filter"]').val('status');
                $($query_form).find('input[name="order_dir_filter"]').val('asc');
                break;
            case 'sort-desc-status':
                $($query_form).find('input[name="order_by_filter"]').val('status');
                $($query_form).find('input[name="order_dir_filter"]').val('desc');
                break;

        }
        $($query_form).submit();

    });
    let users = JSON.parse($('.admin-quizzes-page').attr('data-users'));
    var data = {};
    for (var i = 0; i < users.length; i++) {
        data[users[i]] = null; //countryArray[i].flag or null
    }
    $('input.autocomplete').autocomplete({
        data: data,
        limit: 5,
        onAutocomplete: function (txt) {
            FilterByUser(txt);
        }
    });
    $('.delete-quiz-option').on('click', function () {
        $('#delete-quiz-modal .quiz-name').text($(this).closest('tr').find('td.name-td').text());
        $('#delete-quiz-modal input[name=quiz_id]').val($(this).closest('tr').attr('id'));
    });
});

function FilterByUser(text) {
    if ($($query_form).find('input[name="user_filter"]').length == 0) {
        $($query_form).append('<input type="hidden" name="user_filter"/>');

    }
    $($query_form).find('input[name="user_filter"]').val(text);
    $($query_form).submit();

}

function ClearUserField() {
    $($query_form).find('input[name="user_filter"]').remove();
    $($query_form).submit();
}


