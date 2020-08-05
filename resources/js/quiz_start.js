let quiz_message_editor = null;
let  editor = new Quill('#editor', {
    "modules": {
        // "toolbar": false
    },
    readOnly: true
});
$(document).ready(function () {
    console.log('page loaded');

    let message = $('#editor').attr('data-message');
    if (message.length > 0) {
        message = JSON.parse(message);

        if(message.ops !== undefined) {
            editor.setContents(message.ops);

        }
    }
});