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
/******/ 	return __webpack_require__(__webpack_require__.s = 14);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/add_quizzes_modal.js":
/*!*******************************************!*\
  !*** ./resources/js/add_quizzes_modal.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('.modal').modal({
    onOpenStart: function onOpenStart() {
      $('.add-quizzes-btn').addClass('disabled');
    }
  });
  $('.tooltipped').tooltip();
  $('#add_quizzes').on('click', function () {
    var class_id = $('.page').attr('data-class-id');
    RetrieveTeacherQuizzes(class_id);
  });
  $(document).on('change', '.available-quizzes-table input[type=checkbox]', function () {
    if ($('input[name=new_quizzes]:checked').length > 0) {
      //activate the submit button
      $('.add-quizzes-btn').removeClass('disabled');
    } else {
      //disable the submit button
      $('.add-quizzes-btn').addClass('disabled');
    }
  });
  $('.add-quizzes-btn').on('click', function () {
    $('.add-quizzes-btn').addClass('pending');
    $('.available-quizzes-table input[type=checkbox]').prop('disabled', true);
    $('input[name=new_quizzes]:checked').each(function () {
      var $quiz_input = $('<input>');
      $($quiz_input).attr('name', 'quizzes_ids[]');
      $($quiz_input).attr('type', 'hidden');
      $($quiz_input).val($(this).closest('tr').attr('id'));
      $('form.existing-quizzes-form').append($quiz_input);
    });
    $('.add-quizzes-btn').removeClass('pending');
  });
});

function RetrieveTeacherQuizzes(class_id) {
  $.post({
    url: '/get-quizzes-list',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
      class_id: class_id
    }
  }).done(function (data) {
    data = JSON.parse(data);
    $('#add_existing_quiz_modal .available-quizzes-table tbody tr').remove();
    $('#add_existing_quiz_modal .available-quizzes-table .add-quizzes-btn').addClass('disabled');

    for (var quizIter = 0; quizIter < data.quizzes.length; quizIter++) {
      var $new_row = $('<tr>');
      $($new_row).attr("id", data.quizzes[quizIter].id);
      var $checkbox_td = $('<td>');
      var $checkbox_label = $('<label>');
      var $checkbox_span = $('<span>');
      $($checkbox_span).text('test');
      var $checkbox_input = $('<input>');
      $($checkbox_input).attr('type', 'checkbox');
      $($checkbox_input).attr('name', 'new_quizzes');
      $($checkbox_input).addClass('filled-in');
      $($checkbox_label).append($checkbox_input, $checkbox_span);
      $($checkbox_td).append($checkbox_label);
      $($checkbox_span).text(data.quizzes[quizIter].title.trunc(100));
      $($checkbox_span).addClass('tooltipped');
      $($checkbox_span).attr('data-position', 'top');
      $($checkbox_span).attr('data-tooltip', data.quizzes[quizIter].description);
      $($new_row).append($checkbox_td);
      $('#add_existing_quiz_modal .available-quizzes-table tbody').append($new_row);
    }
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.log("Error");
  }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {// alert("complete");
  });
}

/***/ }),

/***/ 14:
/*!*************************************************!*\
  !*** multi ./resources/js/add_quizzes_modal.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\WORK\saga-project\resources\js\add_quizzes_modal.js */"./resources/js/add_quizzes_modal.js");


/***/ })

/******/ });