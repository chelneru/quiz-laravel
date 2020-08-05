/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 7);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/create_quiz.js":
/*!*************************************!*\
  !*** ./resources/js/create_quiz.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var panel_counter = 0;
var retrieved_questions = null;
var sortable_answers_options = {
  sort: true,
  // sorting inside list
  delay: 0,
  // time in milliseconds to define when the sorting should start
  touchStartThreshold: 0,
  // px, how many pixels the point should move before cancelling a delayed drag event
  disabled: false,
  // Disables the sortable if set to true.
  store: null,
  // @see Store
  animation: 150,
  // ms, animation speed moving items when sorting, `0` — without animation
  handle: ".answer-drag-handle",
  // Drag handle selector within list items
  filter: ".ignore-elements",
  // Selectors that do not lead to dragging (String or Function)
  preventOnFilter: true,
  // Call `event.preventDefault()` when triggered `filter`
  draggable: ".answer-row",
  // Specifies which items inside the element should be draggable
  ghostClass: "sortable-ghost",
  // Class name for the drop placeholder
  chosenClass: "sortable-chosen",
  // Class name for the chosen item
  dragClass: "sortable-drag",
  // Class name for the dragging item
  dataIdAttr: 'data-id',
  forceFallback: false,
  // ignore the HTML5 DnD behaviour and force the fallback to kick in
  fallbackClass: "sortable-fallback",
  // Class name for the cloned DOM Element when using forceFallback
  fallbackOnBody: false,
  // Appends the cloned DOM Element into the Document's Body
  fallbackTolerance: 0,
  // Specify in pixels how far the mouse should move before it's considered as a drag.
  scroll: true,
  // or HTMLElement
  scrollSensitivity: 30,
  // px, how near the mouse must be to an edge to start scrolling.
  scrollSpeed: 10 // px

};
$(document).ready(function () {
  panel_counter = $('.question-panel').length - 2;
  $('select').formSelect();
  $('.tooltipped').tooltip();
  $('.modal').modal();
  $('.answers-container').each(function () {
    if ($(this).children('.answer-row').length == 1) {
      $(this).find('.delete-answer-icon').css('visibility', 'hidden');
    }
  });
  InitSortable(); //init sortable for the initial loaded question panel

  $(document).on('click', '.new-answer-row i', function () {
    var $question_panel = $(this).closest('.question-panel');
    AddNewAnswerRow($question_panel);
  });
  $('.add-new-question .new-question-button').on('click', function () {
    AddNewQuestionPanel();
    UpdateQuestionCounters();
  });
  $(document).on('click', '.delete-answer-icon', function () {
    var $answer_row = $(this).closest('.answer-row');
    var $q_panel = $answer_row.closest('.question-panel');
    $($answer_row).remove();

    if ($($q_panel).find('.answer-row').length == 1) {
      HideDeleteAnswerButton($q_panel);
    }

    if ($('.new-answer-row').css('visibility') == 'hidden') {
      ShowNewAnswerButton();
    }
  });
  $(document).on('click', '.delete-question-icon', function () {
    var tooltip_instance = M.Tooltip.getInstance($(this));

    if (tooltip_instance !== undefined) {
      tooltip_instance.destroy();
    }

    var $question_panel = $(this).closest('.question-panel');
    $($question_panel).remove();

    if ($('#questions-container .question-panel').length == 1) {
      $('.delete-question-icon').css('visibility', 'hidden');
    }

    ShowNewCopyQuestions();
    UpdateQuestionCounters();
  });
  $(document).on('click', '.copy-question-icon', function () {
    var $question_panel = $(this).closest('.question-panel');
    DuplicatePanel($question_panel);

    if ($('.question-panel').length >= 10) {
      HideNewCopyQuestions();
    }

    var $delete_icons = $('.delete-question-icon');

    if ($delete_icons.css('visibility') == 'hidden') {
      $delete_icons.css('visibility', 'visible');
    }

    UpdateQuestionCounters();
  });
  $('.create-quiz-btn').on('click', function () {
    $(this).addClass('pending');
    var result = GetObjectData();

    if (result !== null && validateForm() != false) {
      var location = "/quiz/add-quiz";

      if ($('.create-quiz-page').attr('data-interaction-type') === 'edit') {
        location = "/quiz/edit-quiz";
      }

      $.post({
        url: location,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: result
      }).done(function (data) {
        console.log("Success: " + data);
        var result = JSON.parse(data);

        if (result.status == true) {
          // window.location.reload();
          $(this).removeClass('pending');
          var quiz_id = $('.default-panel').prop('id');

          if ($('.create-quiz-page').attr('data-interaction-type') === 'edit') {
            window.location = '/quiz/quiz-info/' + quiz_id;
          } else if ($('.create-quiz-page').attr('data-interaction-type') === 'create') {
            window.location = result.path;
          }
        } else {
          $('.create-quiz-btn').removeClass('pending');
          ShowGlobalMessage('An error occurred while creating the quiz.', 2);
        }
      }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log("Error");
        $('.create-quiz-btn').removeClass('pending');
        ShowGlobalMessage('An error occurred while creating the quiz.', 2);
      }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {// alert("complete");
      });
    } else {
      $('.create-quiz-btn').removeClass('pending');
      console.log(result);
    }
  });
  $('input.answer-text').bind('keypress', function (e) {
    var code = e.keyCode || e.which;
    var index = $(this).closest('.answer-row').index() + 1;
    var $question_panel = $(this).closest('.question-panel');

    if (code == 13) {
      //Enter keycode
      if ($($question_panel).find('.answer-row').length < 10) {
        AddNewAnswerRow($question_panel, index);
      }
    }
  });
  $(document).on('click', '.image-link-preview', function () {
    var url = $(this).siblings('.image-link-div').find('.question-image-link').val();
    url = PrepareLink(url);
    console.log(url);

    if (validURL(url)) {
      //we have a valid URL we display the image
      $('#image-preview-modal').find('img').attr('src', url);
      $('#image-preview-modal').modal('open');
    } else {
      ShowGlobalMessage('The URL inserted in the image link field is invalid', 2);
    }
  });
  $('.copy-direct-link').on('click', function () {
    var range = document.createRange();
    range.selectNode(document.getElementById("quiz-link"));
    window.getSelection().removeAllRanges(); // clear current selection

    window.getSelection().addRange(range); // to select text

    document.execCommand("copy");
    window.getSelection().removeAllRanges(); // to deselect
  });
  $('.import-question-button').on('click', function () {
    var quiz_id = $('.default-panel').prop('id');
    $('.import-table tbody tr').remove();

    if (retrieved_questions === null) {
      $.post({
        url: '/get-teacher-questions',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: quiz_id
      }).done(function (data) {
        var result = JSON.parse(data);

        if (result.status == true) {
          retrieved_questions = result.questions;
          FillImportTable();
        }
      }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log("Error");
        $('.create-quiz-btn').removeClass('pending');
        ShowGlobalMessage('An error occurred while fetching the questions.', 2);
      }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {// alert("complete");
      });
    } else {
      FillImportTable();
    }
  });
  $('.import-checked-questions-btn').on('click', function () {
    $('#quiz-import-question-modal tbody input.filled-in').each(function () {
      if ($(this).is(':checked')) {
        var question_id = $(this).closest('tr').attr('id');

        for (var iter = 0; iter < retrieved_questions.length; iter++) {
          if (retrieved_questions[iter].id == question_id) {
            AppendFilledQuestion(retrieved_questions[iter]);
            break;
          }
        }
      }
    });
  });
  $(document).on('click', '#quiz-import-question-modal tbody input.filled-in', function () {
    if ($(this).is(':checked')) {
      $(this).closest('tr').find('.quiz-info-div').slideDown("fast", function () {});
    } else {
      $(this).closest('tr').find('.quiz-info-div').slideUp("fast", function () {});
    }
  });
});

function HideDeleteAnswerButton($q_panel) {
  $($q_panel).find('.delete-answer-icon').css('visibility', 'hidden');
}

function ShowDeleteAnswerButton($q_panel) {
  $($q_panel).find('.delete-answer-icon').css('visibility', 'visible');
}

function FillImportTable() {
  for (var iter = 0; iter < retrieved_questions.length; iter++) {
    var $row = $('<tr>');
    $($row).attr('id', retrieved_questions[iter].id);
    var $checkbox_input = $('<input>');
    var $checkbox_label = $('<label>');
    var $checkbox_span = $('<span>');
    var $quiz_info_div = $('<div>', {
      "class": 'quiz-info-div'
    });
    var $answers_div = $('<div>', {
      "class": 'import-quiz-answers-div'
    });

    for (var ansiter = 0; ansiter < retrieved_questions[iter].answers.length; ansiter++) {
      var $answer_row = $('<div>', {
        "class": 'import-quiz-answer-div',
        text: String.fromCharCode(64 + (ansiter + 1)).toUpperCase() + ' : ' + retrieved_questions[iter].answers[ansiter].text.trunc(60)
      });

      if (retrieved_questions[iter].right_answer == ansiter + 1) {
        $($answer_row).css('color', 'black');
      } else {
        $($answer_row).css('color', 'rgb(158, 158, 158)');
      }

      $($answers_div).append($answer_row);
    }

    $($quiz_info_div).append($answers_div);
    $($checkbox_span).text(retrieved_questions[iter].question_text.trunc(80));
    $($checkbox_input).attr('type', 'checkbox');
    $($checkbox_input).attr('name', 'add_question');
    $($checkbox_input).addClass('filled-in');
    $($checkbox_label).append($checkbox_input, $checkbox_span);
    var $cell = $('<td>');
    $($row).append($checkbox_label, $quiz_info_div);
    $('.import-table tbody').append($row);
  }
}

function HideNewAnswerButton() {
  $('.new-answer-row').css('visibility', 'hidden');
}

function ShowNewAnswerButton() {
  $('.new-answer-row').css('visibility', 'visible');
}

function UpdateQuestionCounters() {
  $('.question-panel').each(function () {
    $(this).find('.question-drag-handle .counter').text('Question ' + (parseInt($(this).index()) + 1));
  });
}

function ShowNewCopyQuestions() {
  $('.copy-question-icon').css('visibility', 'visible');
  $('.add-new-question').css('visibility', 'visible');
}

function HideNewCopyQuestions() {
  $('.copy-question-icon').css('visibility', 'hidden');
  $('.add-new-question').css('visibility', 'hidden');
}

function AppendFilledQuestion(question) {
  AddNewQuestionPanel();
  UpdateQuestionCounters();
  var $question_panel = $('#questions-container .question-panel:last'); //remove placeholder answer row

  $($question_panel).find('.answers-container .answer-row:first').remove(); //fill question info

  $($question_panel).find('.question-title').val(question.question_text);
  $($question_panel).find('.question-image-link').val(question.image_link);

  for (var ansiter = 0; ansiter < question.answers.length; ansiter++) {
    AddNewAnswerRow($question_panel);
    var $answer_row = $($question_panel).find('.answer-row:last');
    $($answer_row).find('.answer-text').val(question.answers[ansiter].text);
  } // mark as checked the correct answer


  $($question_panel).find('.answers-container .answer-row:nth-child(' + question.right_answer + ') input[name^="correct_answer"]').prop('checked', true);
}

function AddNewAnswerRow($question_panel, index) {
  var $new_row = $('.answer-row.to-be-cloned').clone();
  $($new_row).removeClass('to-be-cloned');
  var option_counter = $($question_panel).find('.answer-row').length;
  $($new_row).find('.input-field input').val('Option ' + (option_counter + 1));
  $($new_row).find('.correct-answer-icon input').prop('checked', false).attr("name", 'correct_answer' + $($question_panel).index());
  $($new_row).bind('keypress', function (e) {
    var code = e.keyCode || e.which;
    var index = $(this).closest('.answer-row').index() + 1;
    var $question_panel = $(this).closest('.question-panel');

    if (code == 13) {
      //Enter keycode
      if ($($question_panel).find('.answer-row').length < 10) {
        AddNewAnswerRow($question_panel, index);
      }
    }
  });

  if (index !== undefined) {
    $($new_row).insertAfter($($question_panel).find('.answer-row:nth-child(' + index + ')'));
  } else {
    $($new_row).insertBefore($($question_panel).find('.new-answer-row'));
  }

  if ($($question_panel).find('.answers-container').children('.answer-row').length == 10) {
    HideNewAnswerButton();
  }

  if ($($question_panel).find('.delete-answer-icon').css('visibility') == 'hidden') {
    ShowDeleteAnswerButton($question_panel);
  }

  $($new_row).find('.input-field input').select();
}

function AddNewQuestionPanel() {
  panel_counter++;
  var $new_question_panel = $('.question-panel.to-be-cloned').clone();
  $($new_question_panel).removeClass('to-be-cloned');
  $($new_question_panel).find('.tooltipped').tooltip();
  $($new_question_panel).find('.answer-text').bind('keypress', function (e) {
    var code = e.keyCode || e.which;
    var index = $(this).closest('.answer-row').index() + 1;
    var $question_panel = $(this).closest('.question-panel');

    if (code == 13) {
      //Enter keycode
      if ($($question_panel).find('.answer-row').length < 10) {
        AddNewAnswerRow($question_panel, index);
      }
    }
  });
  $($new_question_panel).find('.correct-answer-icon input').prop('checked', false).attr("name", 'correct_answer' + panel_counter);
  InitSortableForAnswers($new_question_panel);
  $($new_question_panel).insertBefore($('.add-new-question'));
  var $delete_icons = $('.delete-question-icon');

  if ($delete_icons.css('visibility') == 'hidden') {
    $delete_icons.css('visibility', 'visible');
  }
}

function DuplicatePanel($question_panel) {
  panel_counter++;
  var $new_question_panel = $($question_panel).clone();
  $($new_question_panel).attr('question_id', '');
  $($new_question_panel).attr('id', 'qpanel' + panel_counter);
  $($new_question_panel).find('.tooltipped').tooltip();
  $($new_question_panel).find('.answer-row.row').attr("id", '');
  $($new_question_panel).find('.correct-answer-icon input').attr("name", 'correct_answer' + panel_counter);
  InitSortableForAnswers($new_question_panel);
  $($new_question_panel).insertBefore($('.add-new-question'));
}

function InitSortableForAnswers($question_panel) {
  Sortable.create($($question_panel).find('.answers-container').get(0), sortable_answers_options);
}

function validateForm() {
  var message = '',
      valid = true; //validate quiz title

  if ($('#quiz_title').val().trim() == '') {
    $('#quiz_title').addClass('invalid-field');
    valid = false;
    message += 'Quiz title has no text set.<br>';
  } else {
    $('#quiz_title').removeClass('invalid-field');
  } //validate title
  // if ($(this).find('.question-title').val().trim() == '') {
  //     $(this).find('.question-title').addClass('invalid-field');
  //     valid = false;
  // } else {
  //     $(this).find('.question-title').removeClass('invalid-field');
  // }
  //validate questions


  $('#questions-container .question-panel').each(function () {
    var valid_panel = true; //check question text

    if ($(this).find('.question-title').val().trim() == '') {
      $(this).find('.question-title').addClass('invalid-field');
      valid = false;
      message += 'Question ' + ($(this).index() + 1) + ' title has no text set.<br>';
      valid_panel = false;
    } else {
      $(this).find('.question-title').removeClass('invalid-field');
    }

    var $question = $(this);
    $(this).find('.answer-row').each(function () {
      if ($(this).find('.answer-text').val().trim() == '') {
        $(this).find('.answer-text').addClass('invalid-field');
        valid = false;
        message += 'Question ' + ($($question).index() + 1) + ' answer ' + ($(this).index() + 1) + ' has no text set.' + '<br>';
        valid_panel = false;
      } else {
        $(this).find('.answer-text').removeClass('invalid-field');
      }
    }); //check if correct answer has been selected

    var right_answer = 0;
    $(this).find('.correct-answer-icon input').each(function () {
      if ($(this).prop('checked') == true) {
        right_answer++;
      }
    });

    if (right_answer == 0) {
      valid = false;
      valid_panel = false;
      message += 'Question ' + ($(this).index() + 1) + ' has no correct answer set.' + '<br>';
    }

    if (valid_panel == false) {
      $(this).css('border-color', 'red');
    } else {
      $(this).css('border-color', '#9e9e9e');
    }
  });

  if (message != '') {
    ShowGlobalMessage(message, 2);
  }

  return valid;
}

function GetObjectData() {
  var questions = [],
      is_valid = true;
  $('#questions-container .question-panel').each(function () {
    var question_text = $(this).find('.question-title').val(),
        image_link = $(this).find('.question-image-link').val(),
        question_answers = [],
        correct_answer = null; //add all the answers

    $(this).find('.answer-row').each(function () {
      var answer_text = $(this).find('.answer-text').val().trim(); //validate answer field

      if (answer_text !== '') {
        //check if this is the correct answer
        if ($(this).find('.correct-answer-icon input').prop('checked') === true) {
          correct_answer = $(this).index() + 1;
        }

        question_answers.push({
          answer_id: $(this).prop('id') != '' ? $(this).prop('id') : null,
          answer_text: answer_text,
          answer_index: $(this).index() + 1
        });
      }
    }); //validate the question

    if (question_answers.length > 0 && question_answers.length < 11) {
      questions.push({
        question_id: $(this).attr('question_id') != '' ? $(this).attr('question_id') : null,
        question_text: question_text,
        image_link: PrepareLink(image_link),
        question_index: $(this).index() + 1,
        question_answers: question_answers,
        question_correct_answer: correct_answer
      });
    }
  });
  var quiz_class = $('.class-select').find('option:selected').val();

  if (questions.length == 0) {
    var message = 'No valid question added!';
    ShowGlobalMessage(message, 2);
  } else {
    return {
      quiz_id: $('.default-panel').prop('id') !== '' ? $('.default-panel').prop('id') : null,
      quiz_class: quiz_class,
      quiz_allow_anonymous: $('.default-panel').find('input[name=anon_participation]').is(':checked') === true ? 1 : 0,
      quiz_is_assessed: $('.default-panel').find('input[name=quiz_is_assessed]').is(':checked') === true ? 1 : 0,
      quiz_text: $('#quiz_title').val(),
      quiz_description: $('#quiz_description').val(),
      quiz_questions: questions
    };
  }
}

function InitSortable() {
  var el = document.getElementById('questions-container');
  Sortable.create(el, {
    sort: true,
    // sorting inside list
    delay: 0,
    // time in milliseconds to define when the sorting should start
    touchStartThreshold: 0,
    // px, how many pixels the point should move before cancelling a delayed drag event
    disabled: false,
    // Disables the sortable if set to true.
    store: null,
    // @see Store
    animation: 150,
    // ms, animation speed moving items when sorting, `0` — without animation
    handle: ".question-drag-handle i",
    // Drag handle selector within list items
    filter: ".ignore-elements",
    // Selectors that do not lead to dragging (String or Function)
    preventOnFilter: true,
    // Call `event.preventDefault()` when triggered `filter`
    draggable: ".question-panel",
    // Specifies which items inside the element should be draggable
    ghostClass: "sortable-ghost",
    // Class name for the drop placeholder
    chosenClass: "sortable-chosen",
    // Class name for the chosen item
    dragClass: "sortable-drag",
    // Class name for the dragging item
    dataIdAttr: 'data-id',
    forceFallback: false,
    // ignore the HTML5 DnD behaviour and force the fallback to kick in
    fallbackClass: "sortable-fallback",
    // Class name for the cloned DOM Element when using forceFallback
    fallbackOnBody: false,
    // Appends the cloned DOM Element into the Document's Body
    fallbackTolerance: 0,
    // Specify in pixels how far the mouse should move before it's considered as a drag.
    scroll: true,
    // or HTMLElement
    scrollSensitivity: 30,
    // px, how near the mouse must be to an edge to start scrolling.
    scrollSpeed: 10,
    // px
    onUpdate: function onUpdate(
    /**Event*/
    evt) {
      UpdateQuestionCounters();
    },
    setData: function setData(
    /** DataTransfer */
    dataTransfer,
    /** HTMLElement*/
    dragEl) {
      dataTransfer.setData('Text', dragEl.textContent); // `dataTransfer` object of HTML5 DragEvent
    }
  });
  var initial_answers_container = document.getElementsByClassName('answers-container')[0];
  Sortable.create(initial_answers_container, sortable_answers_options);
}

function validURL(userInput) {
  var regexQuery = "^(https?://)?(www\\.)?([-a-z0-9]{1,63}\\.)*?[a-z0-9][-a-z0-9]{0,61}[a-z0-9]\\.[a-z]{2,6}(/[:-\\w@\\+\\.~#\\?&/=%]*)?$";
  var url = new RegExp(regexQuery, "i");
  return url.test(userInput);
}

function PrepareLink(url) {
  if (url !== undefined) {
    if (url.indexOf('drive.google.com') > -1) {
      //we have a google drive link
      if (url.indexOf('file/d') > -1) {
        //we need to modify it
        url = url.replace('file/d/', 'uc?id=');

        if (url.indexOf('sharing') > -1) {
          url = url.substr(0, url.lastIndexOf('/'));
        }
      }
    }

    if (url.indexOf('dropbox.com') > -1) {
      //we have a dropbox link
      url = url.replace('?dl=0', '?raw=1');
    }
  }

  return url;
}

/***/ }),

/***/ 7:
/*!*******************************************!*\
  !*** multi ./resources/js/create_quiz.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\WORK\saga-project\resources\js\create_quiz.js */"./resources/js/create_quiz.js");


/***/ })

/******/ });