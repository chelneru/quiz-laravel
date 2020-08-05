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
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/app.js":
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports) {

var showed_notif = false;
$(document).ready(function () {
  $('.modal').modal();
  toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-top-center",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": 4000,
    "extendedTimeOut": 0,
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut",
    "tapToDismiss": false
  };
  $(".loading-spinner").fadeOut();
  $("html").css('overflow-y', 'unset');
  $('.sidenav').sidenav({
    edge: 'right'
  });
  $('.collapsible').collapsible(); //check if there any any notification message that needs to be shown

  if ($(".notif").length > 0 && showed_notif == false) {
    if ($('.notif').hasClass('success')) {
      ShowGlobalMessage($('.notif').text(), 1);
    } else if ($('.notif').hasClass('fail')) {
      ShowGlobalMessage($('.notif').text(), 2);
    }

    showed_notif = true;
  }

  var succes_message = sessionStorage.getItem("success-message");
  var fail_message = sessionStorage.getItem("fail-message");

  if (succes_message !== null) {
    ShowGlobalMessage(succes_message, 1);
    sessionStorage.removeItem("success-message");
  }

  if (fail_message !== null) {
    ShowGlobalMessage(fail_message, 2);
    sessionStorage.removeItem("fail-message");
  }

  $('.action-buttons-space').css('width', $('.container').css('width'));
  $('.mobile-toggle').on('click', function () {});
  $(".navbar-dropdown-trigger").dropdown({
    coverTrigger: false,
    alignment: 'left',
    // Displays dropdown with edge aligned to the left of button,
    constrainWidth: false
  });
  $('#navbarDropdown').on('click', function () {
    $('.dropdown-menu-left').css('display', 'block');
  });
  $(document).on('input', 'input[maxlength]', function () {
    var $input = $(this);
    $(this).next('.input-counter').text($($input).val().length + '/' + $($input).attr('maxlength'));
  });
  $(document).on('keyup', 'input[maxlength]', function () {
    var $input = $(this);
    $(this).next('.input-counter').text($($input).val().length + '/' + $($input).attr('maxlength'));
  });
  $(document).on('focus', 'input[maxlength]', function () {
    if ($(this).val() !== undefined) {
      $(this).next('.input-counter').text($(this).val().length + '/' + $(this).attr('maxlength'));
    }
  });
  $(document).mouseup(function (e) {
    var $menu = $('.dropdown-menu-left');

    if (!$menu.is(e.target) && $menu.has(e.target).length === 0) {
      $menu.css('display', 'none');
    }
  });
});

String.prototype.trunc = String.prototype.trunc || function (n) {
  return this.length > n ? this.substr(0, n - 1) + '...' : this;
};

window.error_message_timeout = null;

window.validateEmail = function (email) {
  var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
};
/**
 * function to display a dialog at the top of the window
 * @string param message text to be displayed
 * @int param status 1-success green box, 2-fail orange box
 * @constructor
 */


window.ShowGlobalMessage = function (message, status, confirm) {
  if (confirm != undefined) {
    if (status == 1) {
      toastr.success(message);
    } else {
      toastr.options.closeButton = true;
      toastr.options.timeOut = 0;
      toastr.warning(message);
    }
  } else {
    if (status == 1) {
      toastr.success(message);
    } else {
      toastr.options.closeButton = true;
      toastr.options.timeOut = 0;
      toastr.warning(message);
    }
  }

  clearTimeout(error_message_timeout);
  window.error_message_timeout = setTimeout(function () {
    $('.global-message-div').animate({
      opacity: 0
    }).addClass('hidden').removeClass(status == 1 ? 'success' : 'fail');
  }, 4000);
};

window.ClearGlobalMessages = function () {
  toastr.clear();
};

String.prototype.ShortenedString = function (length, trailing_points) {
  if (this.length > length && trailing_points === true) {
    return this.substr(0, length) + '...';
  } else {
    return this.substr(0, length);
  }
};

/***/ }),

/***/ "./resources/sass/accompanying_questions.scss":
/*!****************************************************!*\
  !*** ./resources/sass/accompanying_questions.scss ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/additional_messages.scss":
/*!*************************************************!*\
  !*** ./resources/sass/additional_messages.scss ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/admin_classes.scss":
/*!*******************************************!*\
  !*** ./resources/sass/admin_classes.scss ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/admin_edit_class.scss":
/*!**********************************************!*\
  !*** ./resources/sass/admin_edit_class.scss ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/admin_edit_quiz.scss":
/*!*********************************************!*\
  !*** ./resources/sass/admin_edit_quiz.scss ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/admin_manage_user.scss":
/*!***********************************************!*\
  !*** ./resources/sass/admin_manage_user.scss ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/admin_quizzes.scss":
/*!*******************************************!*\
  !*** ./resources/sass/admin_quizzes.scss ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/admin_user_view.scss":
/*!*********************************************!*\
  !*** ./resources/sass/admin_user_view.scss ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/admin_users.scss":
/*!*****************************************!*\
  !*** ./resources/sass/admin_users.scss ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/app-global.scss":
/*!****************************************!*\
  !*** ./resources/sass/app-global.scss ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/app.scss":
/*!*********************************!*\
  !*** ./resources/sass/app.scss ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/classes.scss":
/*!*************************************!*\
  !*** ./resources/sass/classes.scss ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/dashboard.scss":
/*!***************************************!*\
  !*** ./resources/sass/dashboard.scss ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/export_quiz.scss":
/*!*****************************************!*\
  !*** ./resources/sass/export_quiz.scss ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/landing-page.scss":
/*!******************************************!*\
  !*** ./resources/sass/landing-page.scss ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/leaderboard.scss":
/*!*****************************************!*\
  !*** ./resources/sass/leaderboard.scss ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/login.scss":
/*!***********************************!*\
  !*** ./resources/sass/login.scss ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/materialize.scss":
/*!*****************************************!*\
  !*** ./resources/sass/materialize.scss ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/monitor_quiz.scss":
/*!******************************************!*\
  !*** ./resources/sass/monitor_quiz.scss ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/profile.scss":
/*!*************************************!*\
  !*** ./resources/sass/profile.scss ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/questions.scss":
/*!***************************************!*\
  !*** ./resources/sass/questions.scss ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/quiz_game.scss":
/*!***************************************!*\
  !*** ./resources/sass/quiz_game.scss ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/quiz_presentation.scss":
/*!***********************************************!*\
  !*** ./resources/sass/quiz_presentation.scss ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/quiz_results.scss":
/*!******************************************!*\
  !*** ./resources/sass/quiz_results.scss ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/quiz_scheduling.scss":
/*!*********************************************!*\
  !*** ./resources/sass/quiz_scheduling.scss ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/quiz_start.scss":
/*!****************************************!*\
  !*** ./resources/sass/quiz_start.scss ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/quizzes.scss":
/*!*************************************!*\
  !*** ./resources/sass/quizzes.scss ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/register.scss":
/*!**************************************!*\
  !*** ./resources/sass/register.scss ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/running_quiz.scss":
/*!******************************************!*\
  !*** ./resources/sass/running_quiz.scss ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/scores.scss":
/*!************************************!*\
  !*** ./resources/sass/scores.scss ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/users.scss":
/*!***********************************!*\
  !*** ./resources/sass/users.scss ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** multi ./resources/js/app.js ./resources/sass/app.scss ./resources/sass/landing-page.scss ./resources/sass/app-global.scss ./resources/sass/register.scss ./resources/sass/login.scss ./resources/sass/profile.scss ./resources/sass/materialize.scss ./resources/sass/questions.scss ./resources/sass/quizzes.scss ./resources/sass/dashboard.scss ./resources/sass/classes.scss ./resources/sass/users.scss ./resources/sass/accompanying_questions.scss ./resources/sass/additional_messages.scss ./resources/sass/running_quiz.scss ./resources/sass/monitor_quiz.scss ./resources/sass/export_quiz.scss ./resources/sass/quiz_results.scss ./resources/sass/leaderboard.scss ./resources/sass/quiz_scheduling.scss ./resources/sass/quiz_start.scss ./resources/sass/quiz_presentation.scss ./resources/sass/admin_users.scss ./resources/sass/admin_classes.scss ./resources/sass/admin_quizzes.scss ./resources/sass/admin_manage_user.scss ./resources/sass/admin_user_view.scss ./resources/sass/admin_edit_class.scss ./resources/sass/admin_edit_quiz.scss ./resources/sass/scores.scss ./resources/sass/quiz_game.scss ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! C:\WORK\saga-project\resources\js\app.js */"./resources/js/app.js");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\app.scss */"./resources/sass/app.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\landing-page.scss */"./resources/sass/landing-page.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\app-global.scss */"./resources/sass/app-global.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\register.scss */"./resources/sass/register.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\login.scss */"./resources/sass/login.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\profile.scss */"./resources/sass/profile.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\materialize.scss */"./resources/sass/materialize.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\questions.scss */"./resources/sass/questions.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\quizzes.scss */"./resources/sass/quizzes.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\dashboard.scss */"./resources/sass/dashboard.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\classes.scss */"./resources/sass/classes.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\users.scss */"./resources/sass/users.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\accompanying_questions.scss */"./resources/sass/accompanying_questions.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\additional_messages.scss */"./resources/sass/additional_messages.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\running_quiz.scss */"./resources/sass/running_quiz.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\monitor_quiz.scss */"./resources/sass/monitor_quiz.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\export_quiz.scss */"./resources/sass/export_quiz.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\quiz_results.scss */"./resources/sass/quiz_results.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\leaderboard.scss */"./resources/sass/leaderboard.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\quiz_scheduling.scss */"./resources/sass/quiz_scheduling.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\quiz_start.scss */"./resources/sass/quiz_start.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\quiz_presentation.scss */"./resources/sass/quiz_presentation.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\admin_users.scss */"./resources/sass/admin_users.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\admin_classes.scss */"./resources/sass/admin_classes.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\admin_quizzes.scss */"./resources/sass/admin_quizzes.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\admin_manage_user.scss */"./resources/sass/admin_manage_user.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\admin_user_view.scss */"./resources/sass/admin_user_view.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\admin_edit_class.scss */"./resources/sass/admin_edit_class.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\admin_edit_quiz.scss */"./resources/sass/admin_edit_quiz.scss");
__webpack_require__(/*! C:\WORK\saga-project\resources\sass\scores.scss */"./resources/sass/scores.scss");
module.exports = __webpack_require__(/*! C:\WORK\saga-project\resources\sass\quiz_game.scss */"./resources/sass/quiz_game.scss");


/***/ })

/******/ });