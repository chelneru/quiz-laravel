let questions = [];

$(document).ready(function () {
    $('select').formSelect();
    let el = document.getElementById('answers');

    Sortable.create(el, {
        sort: true,  // sorting inside list
        delay: 0, // time in milliseconds to define when the sorting should start
        touchStartThreshold: 0, // px, how many pixels the point should move before cancelling a delayed drag event
        disabled: false, // Disables the sortable if set to true.
        store: null,  // @see Store
        animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation
        handle: ".sortable-handle",  // Drag handle selector within list items
        filter: ".ignore-elements",  // Selectors that do not lead to dragging (String or Function)
        preventOnFilter: true, // Call `event.preventDefault()` when triggered `filter`
        draggable: ".answer-row",  // Specifies which items inside the element should be draggable
        ghostClass: "sortable-ghost",  // Class name for the drop placeholder
        chosenClass: "sortable-chosen",  // Class name for the chosen item
        dragClass: "sortable-drag",  // Class name for the dragging item
        dataIdAttr: 'data-id',

        forceFallback: false,  // ignore the HTML5 DnD behaviour and force the fallback to kick in

        fallbackClass: "sortable-fallback",  // Class name for the cloned DOM Element when using forceFallback
        fallbackOnBody: false,  // Appends the cloned DOM Element into the Document's Body
        fallbackTolerance: 0, // Specify in pixels how far the mouse should move before it's considered as a drag.

        scroll: true, // or HTMLElement
        scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
        scrollSpeed: 10, // px

        setData: function (/** DataTransfer */dataTransfer, /** HTMLElement*/dragEl) {
            dataTransfer.setData('Text', dragEl.textContent); // `dataTransfer` object of HTML5 DragEvent
        },

        // Element is chosen
        onChoose: function (/**Event*/evt) {
            evt.oldIndex;  // element index within parent
        },

        // Element dragging started
        onStart: function (/**Event*/evt) {
            HideDeleteButtons();
            evt.oldIndex;  // element index within parent
        },

        // Changed sorting within list
        onUpdate: function (/**Event*/evt) {
            // same properties as onEnd
        },

        // Called by any change to the list (add / update / remove)
        onSort: function (/**Event*/evt) {
            // same properties as onEnd
        },

    });

    $('.add-new-answer-btn').on('click', function () {
        AddNewAnswerRow();
    });
    $(document).on('click', '.remove-answer-icon', function () {


        if (confirm("Are you sure you want to remove the answer ?")) {
            $(this).parent().remove();
            let no_of_answers = $('.answer-row').length;

            if (no_of_answers == 1) {
                HideDeleteButtons();
            }
            if ($('.add-new-answer-btn').css('display') === 'none') {
                ShowNewAnswerButton();
            }
        } else {
            //nothing
        }
    });
});


function HideDeleteButtons() {
    $('.remove-answer-icon').css('display', 'none');
}

function ShowDeleteButtons() {
    $('.remove-answer-icon').css('display', 'inline-block');
}




function AddNewAnswerRow() {
    let no_of_answers = $('.answer-row').length;
    //check for maximum number of answers rows
    if (no_of_answers < 10) {
        let element = '<div class="row answer-row" style="">' +
            '    <div class="input-field col s5 inline">' +
            '        <i class="material-icons prefix sortable-handle noselect">drag_handle</i>' +
            '        <input placeholder="Lorem ipsum dolor." id="answer_text" type="text">' +
            '    </div>' +
            '    <i class="small material-icons red-text lighten-1 remove-answer-icon noselect" style="display: inline-block;">clear</i>' +
            '</div>';
        $('.answers-container').append(element);
        //check if the remove buttons were hidden previously (only one row was present)
        if ($('.remove-answer-icon').css('display') === 'none') {
            ShowDeleteButtons();
        }
        if (no_of_answers == 9) {
            HideNewAnswerButton();
        }
    }
}



