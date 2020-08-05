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
/******/ 	return __webpack_require__(__webpack_require__.s = 13);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/users.js":
/*!*******************************!*\
  !*** ./resources/js/users.js ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('select').formSelect();
  $('.modal').modal();
  $('.dropdown-trigger').dropdown({
    constrainWidth: false
  });
  $('#query-participants-form').on('submit', function () {
    $(this).find('input[name=participant_filter]').val($('#autocomplete-participant-input').val());
  });
  $('#class-dropdown').on('change', function () {
    if ($('#query-participants-form').find('input[name="class_filter"]').length == 0) {
      $('#query-participants-form').append('<input type="hidden" name="class_filter"/>');
    }

    $('#query-participants-form').find('input[name="class_filter"]').val($(this).find('option:selected').val());
    $('#query-participants-form').submit();
  });
  $('#role-dropdown').on('change', function () {
    if ($('#query-participants-form').find('input[name="role_filter"]').length == 0) {
      $('#query-participants-form').append('<input type="hidden" name="role_filter"/>');
    }

    $('#query-participants-form').find('input[name="role_filter"]').val($(this).find('option:selected').val());
    $('#query-participants-form').submit();
  });
  $('.participants-page .inactive-button').on('click', function () {
    var $modal = $('#dismiss-participant-modal');
    var participant_id = $(this).closest('tr').attr('id');
    var participant_name = $(this).closest('tr').find('.name-td').text();
    var participant_classes_names = $(this).closest('tr').find('.class-td').attr('data-class-names').split(',');
    var participant_classes_ids = $(this).closest('tr').find('.class-td').attr('data-class-ids').split(','); //fill the modal

    if (participant_classes_names.length == 1) {
      //we have only one class
      $($modal).find('.single-class').css('display', 'block');
      $($modal).find('.multiple-class').css('display', 'none');
      $($modal).find('.user-name').text(participant_name);
      $($modal).find('.class-name').text(participant_classes_names[0]); //update FORM

      $('#dismiss_participant_form').find('input[name=user_id]').val(participant_id);
      var $input = $('<input/>', {
        type: 'hidden',
        name: 'class_ids[]',
        value: participant_classes_ids[0]
      });
      $('#dismiss_participant_form').append($input);
    } else {
      //we have multiple classes
      $($modal).find('.single-class').css('display', 'none');
      $($modal).find('.multiple-class').css('display', 'block');
      $($modal).find('.user-name').text(participant_name);
      $($modal).find('.rows-container').find('.class-row').remove();

      for (var classIter = 0; classIter < participant_classes_ids.length; classIter++) {
        var $class_row = $('<div/>', {
          "class": 'class-row'
        });
        var $label = $('<label/>');

        var _$input = $('<input/>', {
          type: 'checkbox',
          "class": 'filled-in',
          'data-class-id': parseInt(participant_classes_ids[classIter].replace('"', ''))
        });

        var $span = $('<span/>', {
          text: participant_classes_names[classIter].replace('"', '')
        });
        $($label).append(_$input, $span);
        $($class_row).append($label);
        $($modal).find('.rows-container').append($class_row);
      }

      $($modal).find('form').find('input[name=user_id]').val(participant_id);
    }
  });
  $('#dismiss-participant-modal .action-submit').on('click', function () {
    var $modal = $('#dismiss-participant-modal');

    if ($('.multiple-class').css('display') == 'block') {
      if ($($modal).find('.class-row').length > 0) {
        //we have multiple classes so we add them to the form
        $($modal).find('.class-row').each(function () {
          if ($(this).find('input[type=checkbox]').prop('checked')) {
            var class_id = $(this).find('input[type=checkbox]').attr('data-class-id');
            var $input = $('<input/>', {
              type: 'hidden',
              name: 'class_ids[]',
              value: class_id
            });
            $('#dismiss_participant_form').append($input);
          }
        });
      }
    }
  }); // $(document).on('click', 'th', function () {
  //
  //     let prop = $(this).attr('data-function').split('-');
  //     let dir = prop[1];
  //     let sort_by = prop[2];
  //     $('table.scores-table th span i').css('display','none');
  //     $(this).find('span i.'+dir).css('display','block');
  //
  //     let new_dir = dir =='asc'?'desc':'asc';
  //     $(this).attr('data-function','sort-'+new_dir+'-'+sort_by);
  //     SortContent(sort_by, dir);
  //
  // });

  $('.participants-page .participants-table th').click(function () {
    var prop = $(this).attr('data-function').split('-');
    var dir = prop[1];
    var sort_by = prop[2]; // $('table.scores-table th span i').css('display','none');
    // $(this).find('span i.'+dir).css('display','block');

    var new_dir = dir == 'asc' ? 'desc' : 'asc';
    $(this).attr('data-function', 'sort-' + new_dir + '-' + sort_by);
    var icon_function = $(this).attr('data-function');

    if ($('#query-participants-form').find('input[name="order_by_filter"]').length == 0) {
      $('#query-participants-form').append('<input type="hidden" name="order_by_filter"/>');
    }

    if ($('#query-participants-form').find('input[name="order_dir_filter"]').length == 0) {
      $('#query-participants-form').append('<input type="hidden" name="order_dir_filter"/>');
    }

    switch (icon_function) {
      case 'sort-asc-name':
        $('#query-participants-form').find('input[name="order_by_filter"]').val('name');
        $('#query-participants-form').find('input[name="order_dir_filter"]').val('asc');
        break;

      case 'sort-asc-email':
        $('#query-participants-form').find('input[name="order_by_filter"]').val('email');
        $('#query-participants-form').find('input[name="order_dir_filter"]').val('asc');
        break;

      case 'sort-asc-class':
        $('#query-participants-form').find('input[name="order_by_filter"]').val('class_names');
        $('#query-participants-form').find('input[name="order_dir_filter"]').val('asc');
        break;

      case 'sort-desc-name':
        $('#query-participants-form').find('input[name="order_by_filter"]').val('name');
        $('#query-participants-form').find('input[name="order_dir_filter"]').val('desc');
        break;

      case 'sort-desc-email':
        $('#query-participants-form').find('input[name="order_by_filter"]').val('email');
        $('#query-participants-form').find('input[name="order_dir_filter"]').val('desc');
        break;

      case 'sort-desc-class':
        $('#query-participants-form').find('input[name="order_by_filter"]').val('class_names');
        $('#query-participants-form').find('input[name="order_dir_filter"]').val('desc');
        break;
    }

    $('#query-participants-form').submit();
  });
  var participants = JSON.parse($('.participants-page').attr('data-participants'));
  var data = {};

  for (var i = 0; i < participants.length; i++) {
    //console.log(countryArray[i].name);
    data[participants[i]] = null; //countryArray[i].flag or null
  }

  console.log(data);
  $('input.autocomplete').autocomplete({
    data: data,
    limit: 5,
    onAutocomplete: function onAutocomplete(txt) {
      FilterByParticipant(txt);
    }
  });
});

function FilterByParticipant(text) {
  if ($('#query-participants-form').find('input[name="participant_filter"]').length == 0) {
    $('#query-participants-form').append('<input type="hidden" name="participant_filter"/>');
  }

  $('#query-participants-form').find('input[name="participant_filter"]').val(text);
  $('#query-participants-form').submit();
}

function insertSortParam(order_by, direction) {
  var key = encodeURI('order_by');
  var value = encodeURI(order_by);
  var kvp = document.location.search.substr(1).split('&');
  var i = kvp.length;
  var x;

  while (i--) {
    x = kvp[i].split('=');

    if (x[0] == key) {
      x[1] = value;
      kvp[i] = x.join('=');
      break;
    }
  }

  if (i < 0) {
    kvp[kvp.length] = [key, value].join('=');
  } //this will reload the page, it's likely better to store this until finished


  document.location.search = kvp.join('&');
}

/***/ }),

/***/ 13:
/*!*************************************!*\
  !*** multi ./resources/js/users.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\WORK\saga-project\resources\js\users.js */"./resources/js/users.js");


/***/ })

/******/ });