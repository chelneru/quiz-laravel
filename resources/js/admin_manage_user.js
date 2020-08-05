$(document).ready(function () {
    $('select').formSelect();
    $('.role-select').on('change', function () {
       let selected_role = $(this).val();
       if(selected_role == 2) {
        $('.teacher-information').css('display','block');
       }
       else {
           $('.teacher-information').css('display','none');
       }
    });
});
