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
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/register.js":
/*!**********************************!*\
  !*** ./resources/js/register.js ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  console.log('working .');
  $('.register-tab').on('click', function () {
    $(this).siblings().removeClass('active');
    $(this).addClass('active');

    if ($(this).hasClass('teacher-reg')) {
      $('.teacher-information').css('display', 'block');
      $('.teacher-validate-text').css('visibility', 'visible');
      $('.responsible-text').text('I am solely responsible for the text I will submit on SAGA as class/quiz information, answers, and personal information.');
      $('#user_role').val(2);
    } else {
      $('.teacher-information').css('display', 'none');
      $('.responsible-text').text('I am solely responsible for the text I will submit on SAGA as answers and personal information.');
      $('.teacher-validate-text').css('visibility', 'hidden');
      $('#user_role').val(1);
    }
  });
  $('#password-confirm').on('focusout', function () {
    if ($(this).val().length > 0) {
      if ($(this).val() != $('#password').val()) {
        $(this).addClass('invalid-field');
        $('#password').addClass('invalid-field');
      } else {
        $(this).removeClass('invalid-field');
        $('#password').removeClass('invalid-field');
      }
    }
  });
  $('#password').on('focusout', function () {
    if ($('#password-confirm').val().length > 0) {
      if ($('#password-confirm').val() != $('#password').val()) {
        $('#password-confirm').addClass('invalid-field');
        $('#password').addClass('invalid-field');
      } else {
        $('#password-confirm').removeClass('invalid-field');
        $('#password').removeClass('invalid-field');
      }
    }
  });
  $('#password-confirm, #password').on('change, keyup', function () {
    $('#password-confirm').removeClass('invalid-field');
    $('#password').removeClass('invalid-field');
  });
  $('#register_form').on('submit', function () {
    return ValidateRegisterForm();
  });
});

function ValidateRegisterForm() {
  var is_valid = true;
  var message = '';
  var response = grecaptcha.getResponse();

  if (response.length == 0) {
    is_valid = false;
    message += "Captcha has not been completed.";
  }

  if (!$('input[name=privacy_policy]').is(':checked')) {
    is_valid = false;

    if (message.length > 1) {
      message += "<br>";
    }

    message += "You must agree to the privacy policy.";
  }

  if (!$('input[name=responsibility]').is(':checked')) {
    is_valid = false;

    if (message.length > 1) {
      message += "<br>";
    }

    message += "You must agree to the terms.";
  }

  if ($('#password-confirm').val() != $('#password').val()) {
    is_valid = false;
    $(this).addClass('invalid-field');
    $('#password').addClass('invalid-field');
  } else {
    $(this).removeClass('invalid-field');
    $('#password').removeClass('invalid-field');
  }

  if (is_valid === false) {
    ShowGlobalMessage(message, 2);
  }

  return is_valid;
}

/***/ }),

/***/ 1:
/*!****************************************!*\
  !*** multi ./resources/js/register.js ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\WORK\saga-project\resources\js\register.js */"./resources/js/register.js");


/***/ })

/******/ });