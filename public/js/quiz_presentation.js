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
/******/ 	return __webpack_require__(__webpack_require__.s = 32);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/quiz_presentation.js":
/*!*******************************************!*\
  !*** ./resources/js/quiz_presentation.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var question_index = 0;
var questions = null;
var right_answer_index = null;
$(document).ready(function () {
  console.log('page loaded');
  AlignTheResponses();
  $('.next-question-btn').on('click', function () {
    question_index++;
    UpdateQuestionPanel();
    UpdateButtonsDisplay();
  });
  $('.previous-question-btn').on('click', function () {
    question_index--;
    UpdateQuestionPanel();
    UpdateButtonsDisplay();
  });
  $('.show-answer-btn').on('click', function () {
    $('.question-choices').find('.choice-div:nth-child(' + right_answer_index + ')').css('font-weight', 'bold');
    $('.responses').find('.choice-div:nth-child(' + right_answer_index + ')').css('font-weight', 'bold');
  });
  questions = JSON.parse($('.quiz-presentation-page').attr('data-questions'));
  right_answer_index = $('.quiz-presentation-panel').attr('data-right-answer');
  UpdateButtonsDisplay();
});
window.onresize = AlignTheResponses;

function UpdateQuestionPanel() {
  $('.question-choices .choice-div').remove();
  $('.responses .choice-div').remove();

  for (var q_iter = 0; q_iter < questions.length; q_iter++) {
    if (q_iter == question_index) {
      //update question title index
      $('.question-index').text('Question ' + (q_iter + 1)); //update question title

      $('.main-question-title').text(questions[q_iter].text); //update image link

      $('.main-question-image img').attr('src', questions[q_iter].image_link);

      if (questions[q_iter].image_link != '' && questions[q_iter].image_link != null) {
        $('.main-question-image').css('display', 'block');
      } else {
        $('.main-question-image').css('display', 'none');
      } //update right answer


      right_answer_index = questions[q_iter].right_answer; //update answers & responses

      for (var ans_iter = 0; ans_iter < questions[q_iter].answers.length; ans_iter++) {
        //create answer row
        var $ans_row = $('<div>', {
          "class": 'choice-div',
          text: String.fromCharCode(64 + (ans_iter + 1)).toUpperCase() + ' : ' + questions[q_iter].answers[ans_iter].text
        });
        $('.question-choices').append($ans_row); //create response row

        var $resp_row = $('<div>', {
          "class": 'choice-div'
        });
        var $init_resp = $('<div>', {
          "class": 'initial-phase-response',
          text: questions[q_iter].answers[ans_iter].init_resp.toFixed(2) + ' %'
        });
        var $rev_resp = $('<div>', {
          "class": 'revision-phase-response',
          text: questions[q_iter].answers[ans_iter].rev_resp.toFixed(2) + ' %'
        });
        $($resp_row).append($init_resp, $rev_resp);
        $('.responses').append($resp_row);
      }
    }
  }

  AlignTheResponses();
}

function UpdateButtonsDisplay() {
  if (question_index <= 0) {
    $('.previous-question-btn').css('visibility', 'hidden');
  } else {
    $('.previous-question-btn').css('visibility', 'visible');
  }

  if (question_index >= questions.length - 1) {
    $('.next-question-btn').css('visibility', 'hidden');
  } else {
    $('.next-question-btn').css('visibility', 'visible');
  }
}

function AlignTheResponses() {
  var panel_body_width = parseFloat($('.panel-body').css('width'));
  var right_panel_width = 0;
  var left_panel_width = panel_body_width - right_panel_width - 20; //normal view

  $('.panel-body-left ').css('width', left_panel_width);
  $('.panel-body-right ').css('width', right_panel_width);
  var left_section_width = parseFloat(left_panel_width) / 2;

  if (parseFloat($('.revision-metrics-section').css('max-width')) <= left_section_width) {
    //the revision section doesnt need more width so we allocate it to the answers section instead.
    $('.question-choices').css('width', left_panel_width - parseFloat($('.revision-metrics-section').css('max-width')) - 2);
    $('.revision-metrics-section').css('width', parseFloat($('.revision-metrics-section').css('max-width')));
  } else {
    $('.revision-metrics-section').css('width', parseFloat(left_panel_width) / 2);
    $('.question-choices').css('width', left_section_width);
  }

  $('.headers-row').css('width', $('.question-choices').css('width'));
  $('.revision-metrics-section .responses .choice-div').each(function () {
    var current_index = $(this).index();
    var height = $('.question-choices .choice-div').eq(current_index).css('height');
    $(this).find('div').css('height', height).css('line-height', height);
  });
}

/***/ }),

/***/ 32:
/*!*************************************************!*\
  !*** multi ./resources/js/quiz_presentation.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\WORK\saga-project\resources\js\quiz_presentation.js */"./resources/js/quiz_presentation.js");


/***/ })

/******/ });