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
/******/ 	return __webpack_require__(__webpack_require__.s = 19);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/running_quiz.js":
/*!**************************************!*\
  !*** ./resources/js/running_quiz.js ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('select').formSelect();
  var page_load = Date.now();
  $('#question-form').submit(function () {
    var $form = $(this);
    $('.submit-answer').addClass('pending');

    if ($('.acc-question-panel').length > 0) {
      //we have an acc question
      if ($('.acc-question-panel input[name=acc_question_answer_id]').length > 0) {
        //we have an acc question with options
        if ($('.acc-question-panel input[name=acc_question_answer_id]:checked').length > 0) {
          var text = $('.acc-question-panel input[name=acc_question_answer_id]:checked').closest('label').find('span').text();
          var $input = $('<input/>', {
            type: 'hidden',
            value: text,
            name: 'acc_question_answer'
          });
          $('#question-form').append($input);
        }
      }
    }

    if ($('.running-quiz-panel').length > 0) {
      //we have normal questions
      // we need to build the other questions answers.
      $(".other-question-section input[name^='other_question_ids']").each(function () {
        var other_question_id = $(this).val();
        var $section = $(this).closest('.other-question-section');
        var value = $($section).find("input[name='other_question_answers_content_" + other_question_id + "']:checked").val();

        if (value === null || value === undefined) {
          value = $($section).find("input[name='other_question_answers_content_" + other_question_id + "']").val();
        }

        var $input = $('<input/>', {
          type: 'hidden',
          value: value,
          name: 'other_question_answers[]'
        });
        $('#question-form').append($input);
      });
    }

    var $response_duration_input = $('<input/>', {
      name: 'response_duration',
      type: 'hidden',
      'value': ((Date.now() - page_load) / 1000.0).toFixed(3)
    });
    $($form).append($response_duration_input);
  });
  $('.justif-answers-row .answer-letter').on('click', function () {
    $(this).addClass('selected');
    $(this).siblings().removeClass('selected');
    var answer_index = $(this).attr('data-index');
    var just_answers_data = JSON.parse($('.justification-section').attr('data-just-question-answers'));
    just_answers_data = Object.keys(just_answers_data).map(function (key) {
      //convert object to array and also calculate average
      return [key, just_answers_data[key]];
    });
    $('.justification-section').find('.just-question-answers .just-answer-row').remove();
    var question_id = $('#question-form input[name=question_id]').val();

    for (var answer_iter = 0; answer_iter < just_answers_data.length; answer_iter++) {
      if (just_answers_data[answer_iter][1].question_id == question_id && just_answers_data[answer_iter][1].answer_index == parseInt(answer_index) + 1) {
        var $answer_row = $('<div/>', {
          "class": 'just-answer-row',
          text: just_answers_data[answer_iter][1].answer_content
        });
        $('.justification-section .just-question-answers').append($answer_row);
      }
    }
  });
  $('.other-questions-answers-row .answer-letter').on('click', function () {
    var $section = $(this).closest('.other-question-responses-section');
    $(this).addClass('selected');
    $(this).siblings().removeClass('selected');
    var answer_index = $(this).attr('data-index');
    var other_question_answers_data = JSON.parse($($section).attr('data-other-question-answers'));
    other_question_answers_data = Object.keys(other_question_answers_data).map(function (key) {
      //convert object to array and also calculate average
      return [key, other_question_answers_data[key]];
    });
    $($section).find('.other-questions-answers .other-questions-answer-row').remove();
    var question_id = $('#question-form input[name=question_id]').val();

    for (var answer_iter = 0; answer_iter < other_question_answers_data.length; answer_iter++) {
      if (other_question_answers_data[answer_iter][1].question_id == question_id && other_question_answers_data[answer_iter][1].answer_index == parseInt(answer_index) + 1) {
        var $answer_row = $('<div/>', {
          "class": 'other-questions-answer-row',
          text: other_question_answers_data[answer_iter][1].answer_content
        });
        $($section).find('.other-questions-answers').append($answer_row);
      }
    }
  }); //some CSS modifications

  if ($('.revision-metrics-section').length > 0) {
    AlignTheResponses();
    AlignPercentagesTable();

    window.onresize = function (event) {
      AlignTheResponses();
      AlignPercentagesTable();
    };
  }
});

function AlignTheResponses() {
  var panel_body_width = parseFloat($('.panel-body').css('width'));
  var right_panel_width = 0;

  if ($('.justification-section').length == 0 && $('.other-question-responses-section').length == 0) {
    //if we dont have justifications then we push a little more the left panel and hide the vertical dividing line
    $('.panel-body-left ').css('border-right', 'none');
    right_panel_width = panel_body_width / 5;
  } else {
    right_panel_width = panel_body_width / 3;
  }

  if (panel_body_width <= 613) {
    //mobile view
    $('.panel-body-left ').css('width', '100%');
    $('.panel-body-right ').css('width', '100%');
    var left_panel_width = parseFloat($('.panel-body-left ').css('width'));
    var left_section_width = parseFloat(left_panel_width) / 2;

    if (parseFloat($('.revision-metrics-section').css('max-width')) <= left_section_width) {
      //the revision section doesnt need more width so we allocate it to the answers section instead.
      $('.question-choices').css('width', left_panel_width - parseFloat($('.revision-metrics-section').css('max-width')) - 2);
      $('.revision-metrics-section').css('width', parseFloat($('.revision-metrics-section').css('max-width')));
    } else {
      $('.revision-metrics-section').css('width', parseFloat(left_panel_width) / 2);
      $('.question-choices').css('width', left_section_width);
    }
  } else {
    var _left_panel_width = panel_body_width - right_panel_width - 20; //normal view


    $('.panel-body-left ').css('width', _left_panel_width);
    $('.panel-body-right ').css('width', right_panel_width);

    var _left_section_width = parseFloat(_left_panel_width) / 2;

    if (parseFloat($('.revision-metrics-section').css('max-width')) <= _left_section_width) {
      //the revision section doesnt need more width so we allocate it to the answers section instead.
      $('.question-choices').css('width', _left_panel_width - parseFloat($('.revision-metrics-section').css('max-width')) - 2);
      $('.revision-metrics-section').css('width', parseFloat($('.revision-metrics-section').css('max-width')));
    } else {
      $('.revision-metrics-section').css('width', parseFloat(_left_panel_width) / 2);
      $('.question-choices').css('width', _left_section_width);
    }
  }

  $('.headers-row').css('width', $('.question-choices').css('width'));
  $('.revision-metrics-section .responses .choice-div').each(function () {
    var current_index = $(this).index();
    var height = $('.question-choices .choice-div').eq(current_index).css('height');
    $(this).find('div').css('height', height).css('line-height', height);
  });
}

function AlignPercentagesTable() {
  $('.responses-headers div').each(function (index) {
    var header_width = parseFloat($(this).css('width'));
    $('.choice-div div:nth-child(' + (index + 1) + ')').css('width', header_width);
  });
}

/***/ }),

/***/ 19:
/*!********************************************!*\
  !*** multi ./resources/js/running_quiz.js ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\WORK\saga-project\resources\js\running_quiz.js */"./resources/js/running_quiz.js");


/***/ })

/******/ });