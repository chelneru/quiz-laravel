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
/******/ 	return __webpack_require__(__webpack_require__.s = 28);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/admin_quizzes.js":
/*!***************************************!*\
  !*** ./resources/js/admin_quizzes.js ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var $query_form = null;
$(document).ready(function () {
  $query_form = $('#query-admin-quizzes-form');
  $('select').formSelect();
  $('.modal').modal();
  $('.dropdown-trigger').dropdown({
    constrainWidth: false
  });
  $('#role-dropdown').on('change', function () {
    if ($($query_form).find('input[name="role_filter"]').length == 0) {
      $($query_form).append('<input type="hidden" name="role_filter"/>');
    }

    $($query_form).find('input[name="role_filter"]').val($(this).find('option:selected').val());
    $($query_form).submit();
  });
  $($query_form).on('submit', function () {
    $(this).find('input[name=user_filter]').val($('#autocomplete-user-input').val());
  });
  $('.clear-field').on('click', function () {
    var $input = $(this).siblings('input');

    if ($($input[0]).attr('id') == 'autocomplete-user-input') {
      $(this).siblings('input').val('');
      ClearUserField();
    }
  });
  $('.admin-quizzes-page .quizzes-table th').click(function () {
    var prop = $(this).attr('data-function').split('-');
    var dir = prop[1];
    var sort_by = prop[2];
    var new_dir = dir == 'asc' ? 'desc' : 'asc';
    $(this).attr('data-function', 'sort-' + new_dir + '-' + sort_by);
    var icon_function = $(this).attr('data-function');

    if ($($query_form).find('input[name="order_by_filter"]').length == 0) {
      $($query_form).append('<input type="hidden" name="order_by_filter"/>');
    }

    if ($($query_form).find('input[name="order_dir_filter"]').length == 0) {
      $($query_form).append('<input type="hidden" name="order_dir_filter"/>');
    }

    console.log(icon_function);

    switch (icon_function) {
      case 'sort-asc-name':
        $($query_form).find('input[name="order_by_filter"]').val('name');
        $($query_form).find('input[name="order_dir_filter"]').val('asc');
        break;

      case 'sort-desc-name':
        $($query_form).find('input[name="order_by_filter"]').val('name');
        $($query_form).find('input[name="order_dir_filter"]').val('desc');
        break;

      case 'sort-asc-author':
        $($query_form).find('input[name="order_by_filter"]').val('author');
        $($query_form).find('input[name="order_dir_filter"]').val('asc');
        break;

      case 'sort-desc-author':
        $($query_form).find('input[name="order_by_filter"]').val('author');
        $($query_form).find('input[name="order_dir_filter"]').val('desc');
        break;

      case 'sort-asc-status':
        $($query_form).find('input[name="order_by_filter"]').val('status');
        $($query_form).find('input[name="order_dir_filter"]').val('asc');
        break;

      case 'sort-desc-status':
        $($query_form).find('input[name="order_by_filter"]').val('status');
        $($query_form).find('input[name="order_dir_filter"]').val('desc');
        break;
    }

    $($query_form).submit();
  });
  var users = JSON.parse($('.admin-quizzes-page').attr('data-users'));
  var data = {};

  for (var i = 0; i < users.length; i++) {
    data[users[i]] = null; //countryArray[i].flag or null
  }

  $('input.autocomplete').autocomplete({
    data: data,
    limit: 5,
    onAutocomplete: function onAutocomplete(txt) {
      FilterByUser(txt);
    }
  });
  $('.delete-quiz-option').on('click', function () {
    $('#delete-quiz-modal .quiz-name').text($(this).closest('tr').find('td.name-td').text());
    $('#delete-quiz-modal input[name=quiz_id]').val($(this).closest('tr').attr('id'));
  });
});

function FilterByUser(text) {
  if ($($query_form).find('input[name="user_filter"]').length == 0) {
    $($query_form).append('<input type="hidden" name="user_filter"/>');
  }

  $($query_form).find('input[name="user_filter"]').val(text);
  $($query_form).submit();
}

function ClearUserField() {
  $($query_form).find('input[name="user_filter"]').remove();
  $($query_form).submit();
}

/***/ }),

/***/ 28:
/*!*********************************************!*\
  !*** multi ./resources/js/admin_quizzes.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\WORK\saga-project\resources\js\admin_quizzes.js */"./resources/js/admin_quizzes.js");


/***/ })

/******/ });