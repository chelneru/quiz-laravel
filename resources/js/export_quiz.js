$(document).ready(function () {
    console.log('page loaded');
    $('.modal').modal();

    let modal_elem = document.getElementById('export_session_modal');
    let modal_instance = M.Modal.getInstance(modal_elem);
        $('.modal-trigger').on('click',function () {
          let session_id = $(this).closest('tr').attr('id');
          $('#export_session_modal').find('input[name=session_id]').val(session_id);
        });

        $('.submit-export-form-btn').on('click',function () {
            modal_instance.close();
        });

});
