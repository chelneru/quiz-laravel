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
/******/ 	return __webpack_require__(__webpack_require__.s = 10);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/classes.js":
/*!*********************************!*\
  !*** ./resources/js/classes.js ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var invite_method = 'manual';
var current_modal_quiz_id = null;
var current_modal_participant_id = null;
var class_id_to_delete = null;

if ($('.invite-participants-page').length > 0) {
  Dropzone.autoDiscover = false;
  var myDropzone = new Dropzone('#invite-participants-form', {
    // paramName: "files",
    url: '/upload-invites-participants-csv',
    method: 'post',
    maxFilesize: 25,
    maxFiles: 1,
    parallelUploads: 4,
    uploadMultiple: false,
    autoProcessQueue: false,
    acceptedFiles: ".csv",
    addRemoveLinks: true,
    params: {
      invite_method: 'file_import'
    },
    dictDefaultMessage: "Drop your .CSV file here or click to choose the .CSV file from your computer. The file should have three columns, first two consisting of participants's first name and last name and the third column with the participants's emails."
  });
  $('#btnUpload').on('click', function () {
    myDropzone.processQueue();
  });
  myDropzone.on('sending', function (file, xhr, formData) {
    // Append all form inputs to the formData Dropzone will POST
    var data = $('form').serializeArray();
    $.each(data, function (key, el) {
      formData.append(el.name, el.value);
    });
  });
  myDropzone.on('success', function (file, server_message, formData) {
    if (server_message == 'fail') {
      myDropzone.removeAllFiles();
      ShowGlobalMessage('There has been an issue in processing the file. If the issue persists try another invite method.', 2);
    } else if (server_message == 'success') {
      // window.location = '/classes';
      ShowGlobalMessage('The invites have been sent successfully.', 1);
    }
  });
}

$(document).ready(function () {
  $('.modal').modal();
  $('.tooltipped').tooltip();
  $(' #modal1.modal').modal({
    onCloseEnd: function onCloseEnd() {
      current_modal_quiz_id = null;
    }
  });
  $(' #modal2.modal').modal({
    onCloseEnd: function onCloseEnd() {
      current_modal_participant_id = null;
    }
  });
  $('select').formSelect();
  $('.tabs').tabs();
  $('.dropdown-trigger').dropdown({
    constrainWidth: false
  });
  console.log('page loaded');
  $('.create-class-page .create-class-btn').on('click', function () {
    $(this).addClass('pending');
    var is_valid = ValidateCreateForm();

    if (is_valid) {
      $('.create-class-page #create-class-form').submit();
    } else {
      $(this).removeClass('pending');
      console.log('form is invalid');
    }
  });
  $(".copy-class-page .class-select").on('change', function () {
    var quiz_class = $('.class-select').find('option:selected').text();
  });
  $('.copy-class-page .copy-class-btn').on('click', function () {
    if (ValidateCopyForm()) {
      console.log('submitting form...');
      $('#copy-class-form').submit();
    } else {
      ShowGlobalMessage('The form is invalid', 2);
    }
  });
  $('.invite-participants-page .new-participant-button').on('click', function () {
    var $new_participant_row = $('.participant_row').first().clone();
    $($new_participant_row).find('input').val('');
    $('.participants_rows_container').append($new_participant_row);
    UpdateDeleteIconsForParticipantRows();
  });
  $(document).on('click', '.delete-participant-row', function () {
    $(this).parent().remove();
    UpdateDeleteIconsForParticipantRows();
  });
  $('.invite-participants-page .invite-participants-btn').on('click', function () {
    if (invite_method == 'manual') {
      var is_valid = ValidateManualInviteForm();

      if (is_valid == true) {
        $('.participant_row').each(function () {
          if (!($(this).find('#participant_first_name').val() == '' && $(this).find('#participant_last_name').val() == '' && $(this).find('#participant_email').val() == '')) {
            var first_name_input = $("<input>").attr("type", "hidden").attr("name", "participant_first_name[]").val($(this).find('#participant_first_name').val());
            var last_name_input = $("<input>").attr("type", "hidden").attr("name", "participant_last_name[]").val($(this).find('#participant_last_name').val());
            var email_input = $("<input>").attr("type", "hidden").attr("name", "participant_email[]").val($(this).find('#participant_email').val());
            $('#invite-participants-form').append(first_name_input, last_name_input, email_input);
          }
        });
        var method = $("<input>").attr("type", "hidden").attr("name", "invite_method").val(invite_method);
        $('#invite-participants-form').append(method);
        $('#invite-participants-form').submit();
      }
    } else if (invite_method == 'file_import') {
      var invi_method = $("<input>").attr("type", "hidden").attr("name", "invite_method").val(invite_method);
      $('#file_import_form').append(invi_method);
      $('#file_import_form').submit();
    }
  });
  $('.file_import_tab').on('click', function () {
    $('.dz-default').css('display', 'block');
    invite_method = 'file_import';
  });
  $('.tab').on('click', function () {
    if (!$(this).hasClass('file_import_tab')) {
      $('.dz-default').css('display', 'none');
      $('#btnUpload').css('display', 'none');
      $('.invite-participants-btn').css('display', 'block');
    } else {
      $('#btnUpload').css('display', 'block');
      $('.invite-participants-btn').css('display', 'none');
    }
  });
  $("#class-select").on('change', function () {
    var class_id = $(this).find('option:selected').attr('value');
    $('.class-select[name=class_id]').val(class_id);
    $('#invite-participants-form').css('display', 'block');
    $.post({
      url: '/get-class-code',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        class_id: class_id
      }
    }).done(function (data) {
      data = JSON.parse(data);
      var tabsInstance = M.Tabs.getInstance($('.tabs'));
      $('.manual_invite_tab a').addClass('active');
      $('.invite-operations-div').css('display', 'block');
      tabsInstance.updateTabIndicator();
      $('#code_invite .code-div').text(data.class_code);
    }).fail(function (jqXHR, textStatus, errorThrown) {
      console.log("Error");
    }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {// alert("complete");
    });
  });
  $('.edit-class-page .quiz-container .quiz-row .remove-icon').on('click', function () {
    current_modal_quiz_id = $(this).closest('div.quiz-row').attr('id');
  });
  $('.edit-class-page .participants-container .participant-row .remove-icon').on('click', function () {
    current_modal_participant_id = $(this).closest('div.participant-row').attr('id');
  });
  $('.edit-class-page .edit-class-btn').on('click', function () {
    $(this).addClass('pending');
    var is_valid = ValidateCreateForm();

    if (is_valid) {
      $('.edit-class-page #edit-class-form').submit();
    } else {
      $(this).removeClass('pending');
      console.log('form is invalid');
    }
  });
  $('#modal1 .unlink-quiz').on('click', function () {
    UnlinkQuizFromClass(current_modal_quiz_id);
    current_modal_quiz_id = null;
    $('#modal1').find('.modal-close').click();
  });
  $('#modal1 .delete-quiz').on('click', function () {
    DeleteQuiz(current_modal_quiz_id);
    $('#modal1').find('.modal-close').click();
    current_modal_quiz_id = null;
  });
  $('#modal2 .dismiss-participant').on('click', function () {
    var class_id = $('.edit-class-page').attr('data-class-id');
    DismissParticipant(current_modal_participant_id, class_id);
    $('#modal2').find('.modal-close').click();
  });
  $('.delete-class-menu-btn').on('click', function () {
    class_id_to_delete = $(this).attr('data-class-id');
  });
  $('#class-delete-confirm-modal .confirm-btn').click(function () {
    var class_id = class_id_to_delete; //delete quiz

    $.post({
      url: '/delete-class',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        class_id: class_id
      }
    }).done(function (data) {
      console.log("Success: " + data);
      var result = JSON.parse(data);

      if (result.status == true) {
        $('.quizzes-table tr#' + class_id).remove();
        ShowGlobalMessage('The class has been removed successfully.', 1);
      } else {
        ShowGlobalMessage('An error occurred while removing the class.', 2);
      }
    }).fail(function (jqXHR, textStatus, errorThrown) {
      console.log("Error");
      $('.create-quiz-btn').removeClass('pending');
      ShowGlobalMessage('An error occurred while creating the quiz.', 2);
    }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
      var modal_elem = document.getElementById('class-delete-confirm-modal');
      var modal_instance = M.Modal.getInstance(modal_elem);
      modal_instance.close();
      class_id_to_delete = null;
    });
  });
});

function UpdateDeleteIconsForParticipantRows() {
  if ($('.participant_row').length > 1) {
    $('.participant_row .delete-participant-row').css('visibility', 'visible');
  } else {
    $('.participant_row .delete-participant-row').css('visibility', 'hidden');
  }
}

function DismissParticipant(participant_id, class_id) {
  $.post({
    url: '/user/dismiss-participant',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
      user_id: participant_id,
      class_ids: [class_id],
      ajax_call: true
    }
  }).done(function (data) {
    console.log("Success: " + data);
    var result = JSON.parse(data);

    if (result.status == true) {
      $('.participants-container #' + participant_id).remove();
      ShowGlobalMessage('The participant has been successfully dismissed.', 1);
    } else {
      $('#modal2').close();
      ShowGlobalMessage('There was an error in dismissing the participant.', 2);
    }
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.log("Error");
  }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {// alert("complete");
  });
}

function ValidateManualInviteForm() {
  var is_valid = true;
  $('.participant_row').each(function () {
    if (!($(this).find('#participant_first_name').val() == '' && $(this).find('#participant_last_name').val() == '' && $(this).find('#participant_email').val() == '')) {
      if ($(this).find('#participant_first_name').val() == '') {
        is_valid = false;
        $(this).find('#participant_first_name').addClass('invalid-field');
      } else {
        $(this).find('#participant_first_name').removeClass('invalid-field');
      }

      if ($(this).find('#participant_last_name').val() == '') {
        is_valid = false;
        $(this).find('#participant_last_name').addClass('invalid-field');
      } else {
        $(this).find('#participant_last_name').removeClass('invalid-field');
      }

      if ($(this).find('#participant_email').val() == '' || !validateEmail($(this).find('#participant_email').val())) {
        is_valid = false;
        $(this).find('#participant_email').addClass('invalid-field');
      } else {
        $(this).find('#participant_email').removeClass('invalid-field');
      }
    }
  });
  return is_valid;
}

function ValidateCreateForm() {
  var is_valid = true,
      $name_field = $('#class_name');

  if ($($name_field).val().trim() == '') {
    is_valid = false;
    $($name_field).addClass('invalid-field');
    ShowGlobalMessage('The name of the class is missing.', 2);
  } else {
    $($name_field).removeClass('invalid-field');
  }

  return is_valid;
}

function ValidateCopyForm() {
  var is_valid = true; //validate select

  var message = '';

  if ($(".copy-class-page .class-select").find('option:selected').val() == '') {
    is_valid = false;
    message = 'You need to select a class<br>';
    $(".copy-class-page .class-select").addClass('invalid-field');
  } else {
    $(".copy-class-page .class-select").removeClass('invalid-field');
  } //validate new name
  //validate copy options.


  if (!$('input[name=copy_teachers]').prop('checked') && !$('input[name=copy_participants]').prop('checked') && !$('input[name=copy_quizzes]').prop('checked')) {
    is_valid = false;
    message += 'At least one copy option should be selected.<br>';
  }

  if (message !== '') {
    ShowGlobalMessage(message, 2);
  }

  return is_valid;
}

function UnlinkQuizFromClass(id) {
  $.post({
    url: '/quiz/delete-quiz',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
      quiz_id: id,
      just_unlink: true
    }
  }).done(function (data) {
    console.log("Success: " + data);
    var result = JSON.parse(data);

    if (result.status == true) {
      $('div#' + id + '.quiz-row').remove();
      ShowGlobalMessage('The quiz has been successfully unlinked from the class.', 1);
    } else {
      ShowGlobalMessage('An error occurred while attempting to unlink the quiz from the class.', 2);
    }
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.log("Error");
  }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {// alert("complete");
  });
}

function DeleteQuiz(id) {
  $.post({
    url: '/quiz/delete-quiz',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
      quiz_id: id
    }
  }).done(function (data) {
    console.log("Success: " + data);
    var result = JSON.parse(data);

    if (result.status == true) {
      $('div#' + id + '.quiz-row').remove();
      ShowGlobalMessage('The quiz has been successfully deleted.', 1);
    } else {
      ShowGlobalMessage('An error occurred during the quiz removal.', 2);
    }
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.log("Error");
  }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {// alert("complete");
  });
}

/***/ }),

/***/ 10:
/*!***************************************!*\
  !*** multi ./resources/js/classes.js ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\WORK\saga-project\resources\js\classes.js */"./resources/js/classes.js");


/***/ })

/******/ });