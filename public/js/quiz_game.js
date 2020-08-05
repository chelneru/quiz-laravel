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
/******/ 	return __webpack_require__(__webpack_require__.s = 31);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/quiz_game.js":
/*!***********************************!*\
  !*** ./resources/js/quiz_game.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var quiz_charts = [];
var test_data = [];
var colors = ['#29A2C6', '#FFCB18', '#73B66B', '#FF6D31'];
var first_draw = true;
var quizzes_ids = [];
var group_names = [];
var lead_position_init = [];
var lead_position_rev = [];
var quizzes_initial_info = [];
var updateDataIntervalId = null;
var debug = false;
var chart_data = null;

document.onkeydown = function (evt) {
  evt = evt || window.event;

  if (evt.ctrlKey && evt.keyCode == 90) {
    if (debug == false) {
      debug = true;
      InitTestData();
    } else {
      debug = false;
    }
  }
};

$(document).ready(function () {
  $('select').formSelect();
  $('.groups-count select').change(function () {
    var value = $(this).val();
    $('.quiz-row').css('display', 'none');
    $('.quiz-row:nth-child(-n+' + value + ')').css('display', 'block');
  });
  $('input.quiz-input').on('change textInput input', function () {
    $(this).attr('data-valid', '');
    $(this).removeClass('invalid-input');
    $(this).next('.quiz-status').removeClass('open').removeClass('closed').removeClass('not-found').text('');
    var value = $(this).val();

    if (ValidateQuizLink(value)) {
      $(this).addClass('disabled');
      var quiz_id = value.substr(value.lastIndexOf("/") + 1);
      GetQuizStatus($(this), quiz_id);
    }
  });
  $('.generate-graph-view-btn').on('click', function () {
    $('#game-run-form').find('input[name="group_names[]"]').remove();
    $('#game-run-form').find('input[name="quizzes_ids[]"]').remove();

    if (ValidateLinkRows()) {
      $('.quiz-input').each(function () {
        if ($(this).closest('.quiz-row').css('display') == 'block') {
          var link = $(this).val();
          var $input = $('<input/>', {
            type: 'hidden',
            value: link.substr(link.lastIndexOf("/") + 1),
            name: 'quizzes_ids[]'
          });
          var group_name = $(this).prev('.group-input').val();
          var $group_name_input = $('<input/>', {
            type: 'hidden',
            value: group_name,
            name: 'group_names[]'
          });
          $('#game-run-form').append($input, $group_name_input);
        }
      });
      $('#game-run-form').submit();
      ClearGlobalMessages();
      $('.quiz-input').addClass('disabled');
    }
  });

  if ($('.run-page').length > 0) {
    //we are on the game run page
    ExtractQuizzesIdsOnGameRunPage();
    CreateGraphs();
  }

  $('.start-quiz').on('click', function () {
    StartQuizzes();
  });
  $('.start-revision').on('click', function () {
    StartRevisionForQuizzes();
  });
  $('.reveal-answers').on('click', function () {
    RevealAnswersForQuizzes();
  });
});

function ExtractQuizzesIdsOnGameRunPage() {
  quizzes_ids = [];
  var initial_responses = JSON.parse($('.run-page').attr('data-responses'));

  for (var iter = 0; iter < initial_responses.value.length; iter++) {
    quizzes_ids.push(initial_responses.value[iter].id);
  }
}

function ExtractQuizzesIdsOnGameSetUp() {
  quizzes_ids = [];
  $('.quiz-input').each(function (index, value) {
    if ($(value).closest('.quiz-row').css('display') == 'block') {
      quizzes_ids.push($(value).val().substr($(value).val().lastIndexOf("/") + 1));
    }
  });
}

function ValidateQuizLink(link) {
  var example = $('.quiz-link-container').attr('data-quiz-link-example').slice(0, -1);
  var pattern = example.replace(/\//g, "\\/") + '[0-9]*'; // console.log('regex pattern', pattern);

  var regex = RegExp(pattern);
  return regex.test(link);
}

function GetQuizStatus($quiz_input, quiz_id) {
  $.post({
    url: '/quiz/get-quiz-status',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
      quiz_id: quiz_id
    }
  }).done(function (data) {
    data = JSON.parse(data);

    if (data.status == 1) {
      $($quiz_input).attr('data-valid', true);
      $($quiz_input).attr('data-status', 'open');
      $($quiz_input).attr('data-phase', data.phase);
      $($quiz_input).next('.quiz-status').removeClass('closed');
      $($quiz_input).next('.quiz-status').addClass('open').text('QUIZ IS OPEN');
    } else if (data.status == 2) {
      $($quiz_input).attr('data-valid', false);
      $($quiz_input).next('.quiz-status').removeClass('open');
      $($quiz_input).attr('data-status', 'closed');
      $($quiz_input).next('.quiz-status').addClass('closed').text('QUIZ IS CLOSED');
    } else {
      $($quiz_input).next('.quiz-status').removeClass('open').removeClass('closed').text('QUIZ NOT FOUND');
      $($quiz_input).attr('data-status', 'not_found');
    }

    if (data.reveal_answers !== undefined) {
      $($quiz_input).attr('data-reveal', data.reveal_answers);
    }
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.log("Error");
  }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
    // alert("complete");
    $($quiz_input).removeClass('disabled');
    CheckStartQuizBtnAvailability();
    CheckStartRevisionBtnAvailability();
    CheckShowAnswersBtnAvailability();
    ExtractQuizzesIdsOnGameSetUp();
  });
}

function ValidateLinkRows() {
  var valid = true;
  var messages = '';
  var group_names = [];
  var links_valid = true;
  $('.quiz-input').each(function (index, value) {
    if ($(value).closest('.quiz-row').css('display') == 'block') {
      if ($(value).attr('data-valid') !== "true") {
        $(value).addClass('invalid-input');
        valid = false;
        links_valid = false;
        messages += '\nQuiz link in row ' + index + ' is invalid.';
      } else {
        $(value).removeClass('invalid-input');
      }

      var group_name = $(value).prev('.group-input').val().trim();

      if (group_name.length == 0 || group_names.indexOf(group_name) > -1) {
        $(value).prev('.group-input').addClass('invalid-input');
        valid = false;
        messages += '\nGroup name in row ' + index + ' is invalid.';
      } else {
        $(value).prev('.group-input').removeClass('invalid-input');
      }

      group_names.push(group_name);
    }
  });

  if (valid == false) {
    ShowGlobalMessage(messages, 2);
  }

  if (links_valid == false) {
    $('.start-quiz').addClass('disabled');
    $('.start-revision').addClass('disabled');
    $('.reveal-answers').addClass('disabled');
  }

  if (valid == true) {
    CheckStartQuizBtnAvailability();
    CheckStartRevisionBtnAvailability();
    CheckShowAnswersBtnAvailability();
  }

  return valid;
}

function FetchQuizzesResponsesInfo() {
  $.post({
    url: '/get-quiz-session-responses',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
      quizzes_ids: quizzes_ids
    }
  }).done(function (data) {
    data = JSON.parse(data);

    if (data.status == true) {
      chart_data = data.value;
      ProcessResponses(chart_data, true);
    } else {
      ShowGlobalMessage('An error occurred while fetching the responses.', 2);
      clearInterval(updateDataIntervalId);
    }
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.log("Error");
  }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {// alert("complete");
  });
}

function CheckStartQuizBtnAvailability() {
  var valid = true;
  var closed_count = 0;
  $('.quiz-input').each(function (index, value) {
    if ($(value).closest('.quiz-row').css('display') == 'block') {
      if ($(value).attr('data-status') == 'not_found' || $(value).attr('data-status') == '') {
        valid = false;
      }

      if ($(value).attr('data-status') == 'closed') {
        closed_count++;
      }
    }
  });

  if (valid == true && closed_count > 0) {
    $('.start-quiz').removeClass('disabled');
  } else {
    $('.start-quiz').addClass('disabled');
  }
}

function CheckStartRevisionBtnAvailability() {
  var valid = true;
  var initial_ph_count = 0;
  $('.quiz-input').each(function (index, value) {
    if ($(value).closest('.quiz-row').css('display') == 'block') {
      if ($(value).attr('data-phase') == '') {
        valid = false;
      }

      if ($(value).attr('data-phase') == 1) {
        initial_ph_count++;
      }
    }
  });

  if (valid == true && initial_ph_count > 0) {
    $('.start-revision').removeClass('disabled');
  } else {
    $('.start-revision').addClass('disabled');
  }
}

function CheckShowAnswersBtnAvailability() {
  var valid = true;
  var rev_ph_count = 0;
  $('.quiz-input').each(function (index, value) {
    if ($(value).closest('.quiz-row').css('display') == 'block') {
      if ($(value).attr('data-phase') == 1 || $(value).attr('data-phase') == "" || $(value).attr('data-reveal') == '') {
        valid = false;
      }

      if ($(value).attr('data-reveal') == 0) {
        rev_ph_count++;
      }
    }
  });

  if (valid == true && rev_ph_count > 0) {
    $('.reveal-answers').removeClass('disabled');
  } else {
    $('.reveal-answers').addClass('disabled');
  }
}

function RevealAnswersForQuizzes() {
  $.post({
    url: '/quiz/modify-quiz-reveal-answers',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
      quiz_id: quizzes_ids,
      quiz_reveal_answers_status: 1
    }
  }).done(function (data) {
    data = JSON.parse(data);

    if (data.status == true) {
      ShowGlobalMessage('Answers are now revealed.', 1);
      $('.quiz-input').each(function (index, value) {
        if ($(value).closest('.quiz-row').css('display') == 'block') {
          //update DOM quiz phase for each quiz row
          if ($(value).attr('data-phase') == 2) {
            $(value).attr('data-phase', 3);
          }
        }
      });
    } else {
      ShowGlobalMessage('An error occurred while changing the phase to the reveal answers phase.', 2);
    }

    CheckStartQuizBtnAvailability();
    CheckStartRevisionBtnAvailability();
    CheckShowAnswersBtnAvailability();
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.log("Error : ", textStatus);
  }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {// alert("complete");
  });
}

function StartRevisionForQuizzes() {
  $.post({
    url: '/quiz/modify-quiz-phase',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
      quiz_id: quizzes_ids,
      quiz_phase: 2
    }
  }).done(function (data) {
    data = JSON.parse(data);

    if (data.status == true) {
      if (quizzes_ids.length > 1) {
        ShowGlobalMessage('The quizzes are now on the revision phase.', 1);
      } else {
        ShowGlobalMessage('The quiz is now on the revision phase.', 1);
      }

      $('.quiz-input').each(function (index, value) {
        if ($(value).closest('.quiz-row').css('display') == 'block') {
          //update DOM quiz phase for each quiz row
          if ($(value).attr('data-phase') == 1) {
            $(value).attr('data-phase', 2);
          }
        }
      });
    } else {
      if (quizzes_ids.length > 1) {
        ShowGlobalMessage('An error occurred while changing the phase of the quizzes.', 2);
      } else {
        ShowGlobalMessage('An error occurred while changing the phase of the quiz.', 2);
      }
    }

    CheckStartQuizBtnAvailability();
    CheckStartRevisionBtnAvailability();
    CheckShowAnswersBtnAvailability();
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.log("Error");
  }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {// alert("complete");
  });
}

function StartQuizzes() {
  $.post({
    url: '/quiz/modify-quiz-status',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
      quiz_id: quizzes_ids,
      quiz_status: 1
    }
  }).done(function (data) {
    data = JSON.parse(data);

    if (data.status == true) {
      if (quizzes_ids.length > 1) {
        ShowGlobalMessage('All quizzes have been started.', 1);
      } else {
        ShowGlobalMessage('The quiz has been started.', 1);
      }

      $('.quiz-input').each(function (index, value) {
        if ($(value).closest('.quiz-row').css('display') == 'block') {
          //update DOM quiz status for each quiz row
          $(value).attr('data-status', 'open');

          if ($(value).attr('data-phase') == '') {
            $(value).attr('data-phase', 1);
          }

          $(value).attr('data-phase', data.phase);
          $(value).next('.quiz-status').removeClass('closed');
          $(value).next('.quiz-status').addClass('open').text('QUIZ IS OPEN');
        }
      });
    } else {
      if (quizzes_ids.length > 1) {
        ShowGlobalMessage('An error occurred while starting the quizzes.', 2);
      } else {
        ShowGlobalMessage('An error occurred while starting the quiz.', 2);
      }
    }

    CheckStartQuizBtnAvailability();
    CheckStartRevisionBtnAvailability();
    CheckShowAnswersBtnAvailability();
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.log("Error");
  }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {// alert("complete");
  });
}

function ProcessLeadPositions(quizzes_data) {
  lead_position_init = [];
  lead_position_rev = [];

  for (var idIter = 0; idIter < quizzes_data.length; idIter++) {
    var percentage_rev = 0;
    var percentage_init = 0;
    var total_init = 0;
    var total_rev = 0; //we have revisions so we only care about revisions

    percentage_rev = quizzes_data[idIter].rev_correct_resp / quizzes_data[idIter].rev_rep_count;
    percentage_rev = isNaN(percentage_rev) ? 0 : percentage_rev;
    total_rev = quizzes_data[idIter].rev_rep_count;
    percentage_init = quizzes_data[idIter].init_correct_resp / quizzes_data[idIter].init_rep_count;
    percentage_init = isNaN(percentage_init) ? 0 : percentage_init;
    total_init = quizzes_data[idIter].init_rep_count;
    lead_position_init.push({
      id: quizzes_data[idIter].id,
      percentage: percentage_init,
      total: total_init
    });
    lead_position_rev.push({
      id: quizzes_data[idIter].id,
      percentage: percentage_rev,
      total: total_rev
    });
  }

  lead_position_init.sort(function (a, b) {
    if (a.percentage < b.percentage) result = 1;else if (a.percentage > b.percentage) {
      result = -1;
    } else if (a.percentage == b.percentage) {
      if (a.total < b.total) result = 1;else if (a.total > b.total) {
        result = -1;
      } else if (a.total == b.total) {
        return 1;
      }
    }
    return result;
  });
  lead_position_rev.sort(function (a, b) {
    if (a.percentage < b.percentage) result = 1;else if (a.percentage > b.percentage) {
      result = -1;
    } else if (a.percentage == b.percentage) {
      if (a.total < b.total) result = 1;else if (a.total > b.total) {
        result = -1;
      } else if (a.total == b.total) {
        return 1;
      }
    }
    return result;
  });
}

function ProcessResponses(quizzes_data, is_update) {
  //random test data
  if (debug == true) {
    if (is_update == false) {
      quizzes_data = InitTestData();
    } else {
      quizzes_data = GetTestData();
    }
  }

  if (quizzes_data.value !== undefined) {
    quizzes_data = quizzes_data.value;
  }

  chart_data = quizzes_data;
  ProcessLeadPositions(quizzes_data);

  if (is_update === false) {
    quizzes_initial_info = [];
    quiz_charts = [];
    updateDataIntervalId = setInterval(CreateGraphs, 3000); //3000 MS == 3 seconds
  }

  for (var idIter = 0; idIter < quizzes_data.length; idIter++) {
    if (is_update === false) {
      quizzes_initial_info.push({
        id: quizzes_data[idIter].id,
        group_name: quizzes_data[idIter].group_name,
        questions_count: quizzes_data[idIter].questions_count
      });
    }

    DrawQuizChart('quiz' + quizzes_data[idIter].id, quizzes_data[idIter], is_update, idIter);

    if (idIter + 1 == quizzes_data.length) {
      //this is the last chart to initialize
      if (is_update === false) {}
    }
  }
}

function UpdateLeadingIcon($container, quiz_data) {
  var leading_position_init = lead_position_init.findIndex(function (i) {
    return i.id === quiz_data.id;
  }) + 1;
  var leading_position_rev = lead_position_rev.findIndex(function (i) {
    return i.id === quiz_data.id;
  }) + 1;

  if (leading_position_init === 1 && parseInt(quiz_data.rev_rep_count) === 0) {
    $('.lead-div').removeClass('leading');
    $($container).find('.lead-div').addClass('leading');
  } else if (leading_position_rev == 1 && quiz_data.rev_rep_count > 0) {
    $('.lead-div').removeClass('leading');
    $($container).find('.lead-div').addClass('leading');
  } else {
    $($container).find('.lead-div').removeClass('leading');
  }

  leading_position_init += '.';
  leading_position_rev += '.';

  if (quiz_data.rev_rep_count > 0) {
    $($container).find('.init-column .top-rect .leading-position').addClass('secondary-view');
  } else {
    leading_position_rev = '';
    $($container).find('.init-column .top-rect .leading-position').removeClass('secondary-view');
  } //update initial phase column


  $($container).find('.init-column .top-rect .leading-position').text(leading_position_init); //update revision phase column

  $($container).find('.rev-column .top-rect .leading-position').text(leading_position_rev);
}

function UpdatePercentages($container, init_percentage, rev_percentage, is_revision_phase) {
  //update initial phase percentage
  $($container).find('.percentages .initial-percentage').text(init_percentage.toFixed(2) + ' %');

  if ($($container).find('.init-column .top-rect .leading-position').hasClass('secondary-view')) {
    $($container).find('.percentages .initial-percentage').addClass('secondary-view');
  } else {
    $($container).find('.percentages .initial-percentage').removeClass('secondary-view');
  }

  if (is_revision_phase) {
    //update revision phase percentage
    $($container).find('.percentages .revision-percentage').text(rev_percentage.toFixed(2) + ' %');
  }
}

function DrawQuizChart(identifier, quiz_data, is_update, index) {
  var $container = $('#' + identifier).closest('.quiz-chart-container');

  if (is_update == false) {
    //set the colors
    $($container).find('.init-column .bottom-rect').css('background-color', colors[index]);
    $($container).find('.rev-column .bottom-rect').css('background-color', colors[index]);
    $($container).find('.quiz-group').css('color', colors[index]);
  } else {
    //initialize the group name
    $($container).find('.quiz-group').text(quiz_data.group_name);
  }

  if (quiz_data.rev_rep_count > 0) {
    //we have revision phase
    $($container).find('.rev-column').css('display', 'inline-block');
    $($container).find('.init-column .bottom-rect').css('background-color', colors[index]);
    $($container).find('.init-column .bottom-rect').css('opacity', '0.5');
    $($container).find('.chart-diff-div').css('visibility', 'visible');
    $($container).find('.revision-percentage').css('visibility', 'visible');
  } else {
    //we have initial phase
    $($container).find('.init-column .bottom-rect').css('opacity', '1');
    $($container).find('.chart-diff-div').css('visibility', 'hidden');
    $($container).find('.revision-percentage').css('visibility', 'hidden');
  } //update leading text


  UpdateLeadingIcon($container, quiz_data); //update n= count

  $($container).find('.quiz-group-size').text('(n=' + quiz_data.active_participants + ')'); //calculate percentages

  var initial_percentage = 0;
  var revision_percentage = 0;

  if (quiz_data.init_rep_count > 0) {
    initial_percentage = quiz_data.init_correct_resp / quiz_data.init_rep_count * 100;
  } else {
    quiz_data.init_rep_count = 0;
    quiz_data.init_correct_resp = 0;
    initial_percentage = 0;
  }

  if (quiz_data.rev_rep_count > 0) {
    revision_percentage = quiz_data.rev_correct_resp / quiz_data.rev_rep_count * 100;
  } else {
    quiz_data.rev_rep_count = 0;
    quiz_data.rev_correct_resp = 0;
    revision_percentage = 0;
  } //update chart view


  $($container).find('.init-column .top-rect').css('height', 100 - initial_percentage + '%');
  $($container).find('.init-column .bottom-rect').css('height', initial_percentage + '%');
  $($container).find('.rev-column .top-rect').css('height', 100 - revision_percentage + '%');
  $($container).find('.rev-column .bottom-rect').css('height', revision_percentage + '%');
  UpdatePercentages($container, initial_percentage, revision_percentage, quiz_data.rev_rep_count > 0);
  var diff = (initial_percentage - revision_percentage).toFixed(2);

  if (diff > 0) {
    diff = '+' + diff;
  } //update diff text


  $($container).find('.chart-diff-text').text(diff);
}

function CreateGraphs() {
  var quizzes_data = [];
  var is_update = true;

  if (first_draw === true) {
    quizzes_data = JSON.parse($('.run-page').attr('data-responses'));
    is_update = false;
    first_draw = false;
    chart_data = quizzes_data;
    ProcessResponses(quizzes_data, is_update);
  } else {
    FetchQuizzesResponsesInfo();
  }
}

function getRandomInt(max) {
  return Math.floor(Math.random() * Math.floor(max));
}

function InitTestData() {
  var data = [];
  var questions_count = getRandomInt(20);

  for (var iter = 0; iter < quizzes_initial_info.length; iter++) {
    var init_correct = 50;
    var rev_correct = 0;
    var init_count = 100;
    var rev_count = 0;
    data.push({
      id: quizzes_initial_info[iter].id,
      group_name: quizzes_initial_info[iter].group_name,
      init_correct_resp: init_correct,
      rev_correct_resp: rev_correct,
      init_rep_count: init_count,
      rev_rep_count: rev_count,
      active_participants: getRandomInt(200),
      questions_count: questions_count
    });
  }

  return data;
}

function GetTestData() {
  if (test_data.length == 0) {
    test_data = InitTestData();
  }

  var ids = quizzes_ids; //check if we start revision phase

  var start_revision = false;
  var stop_revision = false;

  for (var iter = 0; iter < ids.length; iter++) {
    var percentage = test_data[iter].init_correct_resp / test_data[iter].init_rep_count * 100;

    if (percentage >= 72) {
      start_revision = true;
    }
  }

  if (start_revision == true) {
    //check if we stop the revision
    for (var _iter = 0; _iter < ids.length; _iter++) {
      var _percentage = test_data[_iter].rev_correct_resp / test_data[_iter].rev_rep_count * 100;

      if (_percentage >= 85) {
        stop_revision = true;
      }
    }
  }

  for (var _iter2 = 0; _iter2 < ids.length; _iter2++) {
    if (start_revision == false) {
      //update initial phase responses
      var correct_resp_addition = getRandomInt(10, 20);
      test_data[_iter2].init_correct_resp += correct_resp_addition;
      test_data[_iter2].init_rep_count += correct_resp_addition + getRandomInt(1, 30);
    } else if (start_revision == true && stop_revision == false) {
      if (test_data[_iter2].rev_rep_count == 0) {
        test_data[_iter2].rev_rep_count = 50;
      } //update revision phase responses


      var _correct_resp_addition = getRandomInt(10, 20);

      test_data[_iter2].rev_correct_resp += _correct_resp_addition;
      test_data[_iter2].rev_rep_count += _correct_resp_addition + getRandomInt(1, 30);
    }
  }

  return test_data;
}

/***/ }),

/***/ 31:
/*!*****************************************!*\
  !*** multi ./resources/js/quiz_game.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\WORK\saga-project\resources\js\quiz_game.js */"./resources/js/quiz_game.js");


/***/ })

/******/ });