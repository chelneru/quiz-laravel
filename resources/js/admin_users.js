let $query_form = null;

$(document).ready(function () {
    $query_form = $('#query-admin-users-form');
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

    $('.admin-users-page .users-table th').click(function () {

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
        switch (icon_function) {
            case 'sort-asc-name':
                $($query_form).find('input[name="order_by_filter"]').val('name');
                $($query_form).find('input[name="order_dir_filter"]').val('asc');
                break;
            case 'sort-asc-email':
                $($query_form).find('input[name="order_by_filter"]').val('email');
                $($query_form).find('input[name="order_dir_filter"]').val('asc');
                break;
            case 'sort-asc-class':
                $($query_form).find('input[name="order_by_filter"]').val('class_count');
                $($query_form).find('input[name="order_dir_filter"]').val('asc');
                break;
            case 'sort-asc-quiz':
                $($query_form).find('input[name="order_by_filter"]').val('quiz_count');
                $($query_form).find('input[name="order_dir_filter"]').val('asc');
                break;
            case 'sort-asc-register_date':
                $($query_form).find('input[name="order_by_filter"]').val('created_at');
                $($query_form).find('input[name="order_dir_filter"]').val('asc');
                break;
            case 'sort-asc-last_login':
                $($query_form).find('input[name="order_by_filter"]').val('last_login');
                $($query_form).find('input[name="order_dir_filter"]').val('asc');
                break;
            case 'sort-desc-name':
                $($query_form).find('input[name="order_by_filter"]').val('name');
                $($query_form).find('input[name="order_dir_filter"]').val('desc');
                break;
            case 'sort-desc-email':
                $($query_form).find('input[name="order_by_filter"]').val('email');
                $($query_form).find('input[name="order_dir_filter"]').val('desc');
                break;
            case 'sort-desc-class':
                $($query_form).find('input[name="order_by_filter"]').val('class_count');
                $($query_form).find('input[name="order_dir_filter"]').val('desc');
                break;
            case 'sort-desc-quiz':
                $($query_form).find('input[name="order_by_filter"]').val('quiz_count');
                $($query_form).find('input[name="order_dir_filter"]').val('desc');
                break;
            case 'sort-desc-register_date':
                $($query_form).find('input[name="order_by_filter"]').val('created_at');
                $($query_form).find('input[name="order_dir_filter"]').val('desc');
                break;
            case 'sort-desc-last_login':
                $($query_form).find('input[name="order_by_filter"]').val('last_login');
                $($query_form).find('input[name="order_dir_filter"]').val('desc');
                break;
        }
        $($query_form).submit();

    });
    let users = JSON.parse($('.admin-users-page').attr('data-users'));
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

    $('.reset-password-option').on('click', function () {
        let email = $(this).closest('tr').find('.email-td').text();
        let name = $(this).closest('tr').find('.name-td').text();
        let $reset_pass_modal = $('#reset-user-password-modal');
        $($reset_pass_modal).find('input[name=email]').val(email);
        $($reset_pass_modal).find('.user-name').text(name);
    });
    $('.delete-user-option').on('click', function () {
        let user_id = $(this).closest('tr').attr('id');
        let name = $(this).closest('tr').find('.name-td').text();
        let $delete_user_modal = $('#delete-user-modal');
        $($delete_user_modal).find('input[name=user_id]').val(user_id);
        $($delete_user_modal).find('.user-name').text(name);
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

