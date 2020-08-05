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
/******/ 	return __webpack_require__(__webpack_require__.s = 6);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/create_question.js":
/*!*****************************************!*\
  !*** ./resources/js/create_question.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var questions = [];
$(document).ready(function () {
  $('select').formSelect();
  var el = document.getElementById('answers');
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
    handle: ".sortable-handle",
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
    scrollSpeed: 10,
    // px
    setData: function setData(
    /** DataTransfer */
    dataTransfer,
    /** HTMLElement*/
    dragEl) {
      dataTransfer.setData('Text', dragEl.textContent); // `dataTransfer` object of HTML5 DragEvent
    },
    // Element is chosen
    onChoose: function onChoose(
    /**Event*/
    evt) {
      evt.oldIndex; // element index within parent
    },
    // Element dragging started
    onStart: function onStart(
    /**Event*/
    evt) {
      HideDeleteButtons();
      evt.oldIndex; // element index within parent
    },
    // Changed sorting within list
    onUpdate: function onUpdate(
    /**Event*/
    evt) {// same properties as onEnd
    },
    // Called by any change to the list (add / update / remove)
    onSort: function onSort(
    /**Event*/
    evt) {// same properties as onEnd
    }
  });
  $('.add-new-answer-btn').on('click', function () {
    AddNewAnswerRow();
  });
  $(document).on('click', '.remove-answer-icon', function () {
    if (confirm("Are you sure you want to remove the answer ?")) {
      $(this).parent().remove();
      var no_of_answers = $('.answer-row').length;

      if (no_of_answers == 1) {
        HideDeleteButtons();
      }

      if ($('.add-new-answer-btn').css('display') === 'none') {
        ShowNewAnswerButton();
      }
    } else {//nothing
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
  var no_of_answers = $('.answer-row').length; //check for maximum number of answers rows

  if (no_of_answers < 10) {
    var element = '<div class="row answer-row" style="">' + '    <div class="input-field col s5 inline">' + '        <i class="material-icons prefix sortable-handle noselect">drag_handle</i>' + '        <input placeholder="Lorem ipsum dolor." id="answer_text" type="text">' + '    </div>' + '    <i class="small material-icons red-text lighten-1 remove-answer-icon noselect" style="display: inline-block;">clear</i>' + '</div>';
    $('.answers-container').append(element); //check if the remove buttons were hidden previously (only one row was present)

    if ($('.remove-answer-icon').css('display') === 'none') {
      ShowDeleteButtons();
    }

    if (no_of_answers == 9) {
      HideNewAnswerButton();
    }
  }
}

/***/ }),

/***/ 6:
/*!***********************************************!*\
  !*** multi ./resources/js/create_question.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\WORK\saga-project\resources\js\create_question.js */"./resources/js/create_question.js");


/***/ })

/******/ });