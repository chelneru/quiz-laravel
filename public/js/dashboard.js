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
/******/ 	return __webpack_require__(__webpack_require__.s = 18);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/dashboard.js":
/*!***********************************!*\
  !*** ./resources/js/dashboard.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('.tooltipped').tooltip();
  $('.modal').modal();

  if ($('.participant-page.dashboard-page').length > 0) {
    editor = new Quill('#editor', {
      "modules": {// "toolbar": false
      },
      readOnly: true
    });
  }

  $('.participants-table .active-list,' + '.participants-table .inactive-list').on("click", function () {
    var active_filter = $(this).hasClass('active-list') ? 2 : 1;
    $('#teacher-participants-list-modal tbody tr').remove();
    $.post({
      url: '/get-participant-list',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        status_filter: active_filter
      }
    }).done(function (data) {
      data = JSON.parse(data);

      if (data.users.length == 0) {
        var $new_row = $('<tr>');
        var $name_td = $('<td>');
        var row_string = active_filter == 2 ? 'No active participants.' : 'No inactive participants.';
        $($name_td).css('text-align', 'center').text(row_string);
        $($new_row).append($name_td);
        $('#teacher-participants-list-modal .users-table tbody').append($new_row);
      } else {
        for (var userIter = 0; userIter < data.users.length; userIter++) {
          var _$new_row = $('<tr>');

          var _$name_td = $('<td>');

          $(_$name_td).text(data.users[userIter].name);
          $(_$new_row).append(_$name_td);
          $('#teacher-participants-list-modal .users-table tbody').append(_$new_row);
        }
      }
    }).fail(function (jqXHR, textStatus, errorThrown) {
      console.log("Error");
    }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {// alert("complete");
    });
  });
  $('.join-class-btn').on('click', function () {
    $(this).addClass('pending');
    var class_code = $('input.class_code').val();

    if (class_code == '') {
      $('input.class_code').addClass('invalid-field');
      $(this).removeClass('pending');
      ShowGlobalMessage('The class code is missing.', 2);
    } else {
      $('input.class_code').removeClass('invalid-field');
      $.post({
        url: '/class/join-class',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          class_code: class_code
        }
      }).done(function (data) {
        var result = JSON.parse(data);

        if (result.status == true) {
          window.location.reload();
        } else {
          $('.join-class-btn').removeClass('pending');

          if (result.message == 'no class found') {
            ShowGlobalMessage('There is no class with this code.', 2);
          }

          if (result.message == 'already enrolled') {
            ShowGlobalMessage('You are already enrolled to this class.', 2);
            $('#join_class_modal').modal('close');
          } else {
            ShowGlobalMessage('An error occurred while joining the class.', 2);
          }
        }
      }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log("Error");
      }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {});
    }
  });
  $('.start-quiz-btn').on('click', function () {
    var quiz_id = $('#quiz_presentation').attr('data-quiz-id');
    $.post({
      url: '/quiz/start-quiz',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        quiz_id: quiz_id,
        json_response: true
      }
    }).done(function (data) {
      var result = JSON.parse(data);

      if (result.status == true) {
        if (result.start == true) {
          window.location = '/quiz/' + quiz_id;
        } else {
          ShowGlobalMessage('Quiz has not started yet');
        }
      } else {
        ShowGlobalMessage(result.message, 2);
        $('.join-class-btn').css('pointer-events', 'unset');
      }
    }).fail(function (jqXHR, textStatus, errorThrown) {
      console.log("Error");
    }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {});
  });
  $('#quiz_presentation').find('.modal-close').on('click', function () {
    $(this).closest('#quiz_presentation').attr('data-quiz-id', '');
  });
  $('.get-quiz-info').on('click', function () {
    var quiz_id = $(this).attr('id');
    var has_progress = $(this).attr('data-has-progress');
    var revealed_answers = $(this).attr('data-has-answers-revealed');
    var $quiz_presentation_modal = $('#quiz_presentation');
    $($quiz_presentation_modal).find('.quiz-title').text('');
    $($quiz_presentation_modal).find('.view-results-btn').remove();
    $($quiz_presentation_modal).find('.quiz-description').text('');
    $($quiz_presentation_modal).find('.quiz-message-title').text('');
    $($quiz_presentation_modal).attr('data-quiz-id', quiz_id);
    $($quiz_presentation_modal).find('.view-results-btn').attr('href', '/quiz-result/' + quiz_id);
    $($quiz_presentation_modal).find('.start-quiz-btn').css('display', 'none');

    if (has_progress == 1 && revealed_answers == 1) {
      var $results_button = $('<a/>', {
        "class": 'btn view-results-btn',
        text: 'view past results'
      });
      $($results_button).attr('href', '/quiz-result/' + quiz_id);
      $($quiz_presentation_modal).find('.modal-buttons').prepend($results_button);
    }

    editor.setContents([{
      insert: '\n'
    }]);
    $.post({
      url: '/quiz/get-dashboard-quiz-info',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        quiz_id: quiz_id
      }
    }).done(function (data) {
      var result = JSON.parse(data);
      $($quiz_presentation_modal).find('.quiz-title').text(result.title);
      $($quiz_presentation_modal).find('.quiz-description').text(result.description);
      $($quiz_presentation_modal).find('.quiz-message-title').text(result.message_title);

      if (result.ongoing_progress == null) {
        $($quiz_presentation_modal).find('.start-quiz-btn').text('start quiz');

        if (result.participation_limit > 0 && result.past_completed_participations >= result.participation_limit || result.status === false) {
          //TODO investigate if this is correct
          $($quiz_presentation_modal).find('.start-quiz-btn').css('display', 'none');
        } else {
          $($quiz_presentation_modal).find('.start-quiz-btn').css('display', 'block');
        }
      } else {
        $($quiz_presentation_modal).find('.start-quiz-btn').css('display', 'block');
        $($quiz_presentation_modal).find('.start-quiz-btn').text('resume quiz');
      }

      if (result.message != '' && result.message !== undefined) {
        editor.setContents(JSON.parse(result.message));
      }

      if (result.status == true) {
        $('.start-quiz-btn').css('pointer-events', 'unset').removeClass('grey');
        $('#quiz_presentation').modal('open');
      } else {
        ShowGlobalMessage('The ' + result.title + ' quiz is closed.', 2);
        $('.start-quiz-btn').css('pointer-events', 'none').addClass('grey');
      }
    }).fail(function (jqXHR, textStatus, errorThrown) {
      console.log("Error", textStatus);
    }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {});
  });
});

/***/ }),

/***/ 18:
/*!*****************************************!*\
  !*** multi ./resources/js/dashboard.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\WORK\saga-project\resources\js\dashboard.js */"./resources/js/dashboard.js");


/***/ })

/******/ });