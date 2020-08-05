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
/******/ 	return __webpack_require__(__webpack_require__.s = 4);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/quizzes.js":
/*!*********************************!*\
  !*** ./resources/js/quizzes.js ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var quiz_id = null;
$(document).ready(function () {
  $('.dropdown-trigger').dropdown({
    constrainWidth: false
  });
  $('.modal').modal();
  $('select').formSelect();
  $('.delete-quiz-button').on('click', function () {
    quiz_id = $(this).closest('tr').attr('id');
    var delelete_quiz_modal_elem = document.getElementById('quiz-delete-confirm-modal');
    var modal_instance = M.Modal.getInstance(delelete_quiz_modal_elem);
    modal_instance.open();
  });
  $('.class-filter').on('change', function () {
    filter_quizzes('class', $(this).find('option:selected').val());
  });
  $('.confirm-btn').on('click', function () {
    if ($('.quiz-details-page').length > 0) {
      //we are in quiz details page, we get the quiz id from the page
      quiz_id = $('.quiz-details-page').attr('data-quiz-id');
    } //the quiz id is already set from the click event of .delete-quiz-button


    DeleteQuiz(quiz_id);
  });
  $('.copy-direct-link').on('click', function () {
    var range = document.createRange();
    range.selectNode(document.getElementById("quiz-link"));
    window.getSelection().removeAllRanges(); // clear current selection

    window.getSelection().addRange(range); // to select text

    document.execCommand("copy");
    window.getSelection().removeAllRanges(); // to deselect
  });
});

function filter_quizzes(filter_name, filter_id) {
  window.location = '/quizzes?' + filter_name + '=' + filter_id;
}

function DeleteQuiz(quiz_id) {
  var location = '/quiz/delete-quiz';
  $.post({
    url: location,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
      quiz_id: quiz_id
    }
  }).done(function (data) {
    console.log("Success: " + data);
    var result = JSON.parse(data);

    if (result.status == true) {
      sessionStorage.setItem("success-message", 'The quiz has been removed successfully.');
      window.location = '/quizzes';
    } else {
      ShowGlobalMessage('An error occurred during the quiz removal.', 2);
    }
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.log("Error");
  }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {// alert("complete");
  });
}

/***/ }),

/***/ 4:
/*!***************************************!*\
  !*** multi ./resources/js/quizzes.js ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\WORK\saga-project\resources\js\quizzes.js */"./resources/js/quizzes.js");


/***/ })

/******/ });