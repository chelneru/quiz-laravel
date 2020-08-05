


$(document).ready(function(){
    $('.tooltipped').tooltip();

    $('select').formSelect();
    $('#class-dropdown').on('change',function () {
        applyFilters();
    });

    $('#quiz-dropdown').on('change',function () {
        applyFilters();
    });
});


function applyFilters() {
    let quiz_filter =  $('#quiz-dropdown').find('option:selected').val();
    let class_filter = $('#class-dropdown').find('option:selected').val();
    console.log('quiz-filter',quiz_filter);
    console.log('class-filter',class_filter);
    let query_string = '';
    if(quiz_filter != '') {
        query_string += '?quiz_id='+quiz_filter;
    }

    if(class_filter !='') {
        if(query_string == '') {
            query_string += '?class_id='+class_filter;
        }
        else {
            query_string += '&class_id='+class_filter;

        }
    }
    window.location = '/questions'+query_string;

}