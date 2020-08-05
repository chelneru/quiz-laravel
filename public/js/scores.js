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
/******/ 	return __webpack_require__(__webpack_require__.s = 25);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/scores.js":
/*!********************************!*\
  !*** ./resources/js/scores.js ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var dataQueryHistory = [];
var current_content = [];
var quizzes = null;
$(document).ready(function () {
  quizzes = JSON.parse($('.scores-page').attr('data-quiz'));
  $('select').formSelect();
  $('.modal').modal();
  $('.dropdown-trigger').dropdown({
    constrainWidth: false
  });
  $('#class-dropdown').on('change', function () {
    var selected_class = $(this).find('option:selected').attr('value');
    var selected_quizzes;

    if (selected_class != "") {
      selected_quizzes = quizzes.filter(function (quiz) {
        return quiz.class_id == selected_class;
      });
    } else {
      selected_quizzes = quizzes;
    }

    PopulateQuizDropdown(selected_quizzes);
    var simplified_quizzes = selected_quizzes.map(function (item) {
      return {
        id: item["id"],
        text: item['title'],
        class_id: item['class_id']
      };
    });
    PopulateQuickSelectionTable(simplified_quizzes, 'quiz');
  });
  $('#quiz-dropdown').on('change', function () {
    var selected_quiz_id = $(this).find('option:selected').attr('value');

    if (selected_quiz_id != "") {
      var selected_quiz = quizzes.find(function (quiz) {
        return quiz.id == selected_quiz_id;
      });
      var sessions = selected_quiz.sessions;
      PopulateQuickSelectionTable(sessions, 'session');
    } else {}
  });
  $(document).on('click', '.quick-access-area tr[data-type="quiz"]', function () {
    $('.quick-access-area tr').removeClass('selected');
    var quiz_id = $(this).attr('id');
    UpdateQuizDropdownSelected(quiz_id);
    var class_id = $(this).attr('data-class-id');
    AddQueryToHistory('quiz', quiz_id, class_id, quiz_id);

    if (dataQueryHistory.length >= 1) {
      ShowBackButton();
    }

    var selected_quiz = quizzes.find(function (quiz) {
      return quiz.id == quiz_id;
    });
    var sessions = selected_quiz.sessions;
    PopulateQuickSelectionTable(sessions, 'session');
    RetrieveData('quiz', quiz_id);
  });
  $(document).on('click', '.quick-access-area tr[data-type="session"]', function () {
    $('.quick-access-area tr').removeClass('selected');
    $(this).addClass('selected');
    var session_id = $(this).attr('data-id');
    RetrieveData('session', session_id); // let selected_class_id = $('#class-dropdown').find('option:selected').attr('value');
    // let selected_quiz_id = $('#quiz-dropdown').find('option:selected').attr('value');
    //
    // AddQueryToHistory('session', session_id,selected_class_id,selected_quiz_id);
    // if (dataQueryHistory.length > 1) {
    //     ShowBackButton();
    // }
  });
  $(document).on('click', '.quick-access-area tr[data-type="participant"]', function () {
    $('.quick-access-area tr').removeClass('selected');
    $(this).addClass('selected');
    UpdateClassDropdownSelected($(this).attr('class-id'));
    UpdateQuizDropdownSelected($(this).attr('quiz-id'));
    var user_id = $(this).attr('id');
    AddQueryToHistory('participant', user_id);

    if (dataQueryHistory.length > 1) {
      ShowBackButton();
    }

    RetrieveData('participant', user_id);
  });
  $('.prev-query').on('click', function () {
    var query = dataQueryHistory.pop();

    if (dataQueryHistory.length == 0) {
      HideBackButton();
    }

    UpdateQuizDropdownSelected(query.quiz_id);
    UpdateClassDropdownSelected(query.class_id);

    if (query.type == 'session') {
      //populate quick selection with all the session from the corresponding quiz
      var selected_quiz = quizzes.find(function (quiz) {
        return quiz.id == query.quiz_id;
      });
      var sessions = selected_quiz.sessions;
      PopulateQuickSelectionTable(sessions, query.type);
      RetrieveData(query.type, query.id);
    } else if (query.type == 'quiz') {
      //populate quick selection with all the quizzes from the corresponding class
      var selected_quizzes = quizzes.filter(function (quiz) {
        return quiz.class_id == query.class_id;
      });
      var simplified_quizzes = selected_quizzes.map(function (item) {
        return {
          id: item["id"],
          text: item['title'],
          class_id: item['class_id']
        };
      });
      PopulateQuickSelectionTable(simplified_quizzes, 'quiz');
      $('.scores-table tbody tr').remove();
    }
  });
  var participants = JSON.parse($('.scores-page').attr('data-participants'));
  var data = {};

  for (var i = 0; i < participants.length; i++) {
    data[participants[i]] = null; //countryArray[i].flag or null
  }

  $('input.autocomplete').autocomplete({
    data: data,
    limit: 5,
    onAutocomplete: function onAutocomplete(txt) {
      FilterByParticipant(txt);
    }
  });
  $(document).on('click', 'th', function () {
    var prop = $(this).attr('data-function').split('-');
    var dir = prop[1];
    var sort_by = prop[2];
    $('table.scores-table th span i').css('display', 'none');
    $(this).find('span i.' + dir).css('display', 'block');
    var new_dir = dir == 'asc' ? 'desc' : 'asc';
    $(this).attr('data-function', 'sort-' + new_dir + '-' + sort_by);
    SortContent(sort_by, dir);
  });
});

function SortContent(sort_by, dir) {
  switch (sort_by) {
    case 'name':
      current_content.sort(SortByName);
      break;

    case 'date':
      current_content.sort(SortByDate);
      break;

    case 'init':
      current_content.sort(SortByInitPhase);
      break;

    case 'rev':
      current_content.sort(SortByRevPhase);
      break;

    case 'diff':
      current_content.sort(SortByDiff);
      break;
  }

  if (dir == 'desc') {
    current_content.reverse();
  }

  $('.scores-table tbody tr').remove();
  AddRowsToMainTable(current_content);
}

function AddRowsToMainTable(rows) {
  for (var iter = 0; iter < rows.length; iter++) {
    var $row = $("<tr>");
    var $participant_td = $('<td>', {
      'class': 'participant-td'
    });
    var $init_td = $('<td>', {
      'class': 'initial-score-td'
    });
    var $rev_td = $('<td>', {
      'class': 'revision-score-td'
    });
    var $taken_on_td = $('<td>', {
      'class': 'taken-on-td'
    });
    var $diff_td = $('<td>', {
      'class': 'diff-td'
    });

    if (Array.isArray(rows[iter]) && rows[iter].length > 0) {
      if (rows[iter][0].phase !== undefined && rows[iter][0].phase == 1 && rows[iter][0].score !== undefined) {
        $($init_td).text(rows[iter][0].score);
      }

      if (rows[iter][0].name !== undefined) {
        $($participant_td).text(rows[iter][0].name);
      }

      if (rows[iter][0].started_at !== undefined) {
        $($taken_on_td).text(rows[iter][0].started_at);
      }
    }

    if (Array.isArray(rows[iter]) && rows[iter].length > 1) {
      if (rows[iter][1].phase !== undefined && rows[iter][1].phase == 2 && rows[iter][1].score !== undefined) {
        $($rev_td).text(rows[iter][1].score != '' ? rows[iter][1].score : '-');
      } else {
        $($rev_td).text('-');
      }
    }

    if ($($init_td).text().trim() !== '' && $($rev_td).text().trim() !== '' && $($rev_td).text() != '-') {
      $($diff_td).text(parseInt($($rev_td).text() - $($init_td).text()));

      if (parseInt($($diff_td).text()) > 0) {
        $($diff_td).text('+' + $($diff_td).text().trim());
        $($diff_td).addClass('positive');
      } else if (parseInt($($diff_td).text()) < 0) {
        $($diff_td).addClass('negative');
      } else if (parseInt($($diff_td).text()) == 0) {
        $($diff_td).text('-');
      }
    } else {
      $($diff_td).text('Incomplete');
    }

    $($row).append($participant_td, $taken_on_td, $init_td, $rev_td, $diff_td);
    $('.scores-table tbody').append($row);
  }
}

function PopulateQuizDropdown(quizzes) {
  $('#quiz-dropdown').find('option').remove();
  var $default_option = $('<option>');
  $($default_option).attr('value', "");
  $($default_option).text(" ");
  $('#quiz-dropdown').append($default_option);

  for (var iter = 0; iter < quizzes.length; iter++) {
    var $option = $('<option>');
    $($option).attr('value', quizzes[iter].id);
    $($option).text(quizzes[iter].title.ShortenedString(20, true));
    $('#quiz-dropdown').append($option);
  }

  var quizSelectElem = document.querySelectorAll('#quiz-dropdown');
  M.FormSelect.init(quizSelectElem, {});
}

function AddQueryToHistory(type, id, class_id, quiz_id) {
  dataQueryHistory.push({
    type: type,
    id: id,
    class_id: class_id,
    quiz_id: quiz_id
  });
}

function ShowBackButton() {
  $('.prev-query').css('visibility', 'visible').css('opacity', '1');
}

function HideBackButton() {
  $('.prev-query').css('visibility', 'hidden').css('opacity', '0');
}

function RetrieveData(type, id) {
  $('.quick-access-area').css('pointer-events', 'none');
  $.post({
    url: '/get-scores-data',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
      type: type,
      id: id
    }
  }).done(function (data) {
    var result = {};

    try {
      result = JSON.parse(data);
    } catch (e) {
      ShowGlobalMessage("Unable to retrieve the scores", 2);
      console.log(data);
    }

    if (result.status == true) {
      if (type == 'participant') {
        PopulateScoresTableForParticipant(result.content, true);
      } else {
        PopulateScoresTableFoQuiz(result.content, false);
      }
    } else {
      ShowGlobalMessage('An error occurred while fetching the scores.', 2);
    }
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.log("Error");
    ShowGlobalMessage('An error occurred while fetching the scores.', 2);
  }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
    $('.quick-access-area').css('pointer-events', 'initial');
  });
}

function PopulateQuickSelectionTable(rows, type) {
  $('.quick-access-area table tbody tr').remove();

  if (type == 'quiz') {
    var _loop = function _loop(iterRow) {
      var $row = $('<tr>').attr('id', rows[iterRow].id).attr('data-class-id', rows[iterRow].class_id).attr('data-type', 'quiz');
      Object.keys(rows[iterRow]).forEach(function (e) {
        if (e != 'id' && e != 'class_id' && e != 'quiz_id') {
          $($row).append($('<td>').text(rows[iterRow][e].ShortenedString(40, true)));
        }
      });
      $('.quick-access-area table tbody').append($row);
    };

    for (var iterRow = 0; iterRow < rows.length; iterRow++) {
      _loop(iterRow);
    }
  } else if (type == 'session') {
    for (var iterRow = 0; iterRow < rows.length; iterRow++) {
      var $row = $('<tr>').attr('data-type', 'session').attr('data-id', rows[iterRow].id);
      $($row).append($('<td>').text('Session ' + moment(rows[iterRow].started_at).format("DD-MM-YYYY HH:mm")));
      $('.quick-access-area table tbody').append($row);
    }
  } else if (type == 'participant') {
    for (var _iterRow = 0; _iterRow < rows.length; _iterRow++) {
      for (var sesIter = 0; sesIter < rows[_iterRow].sessions.length; sesIter++) {
        var _$row = $('<tr>').attr('id', rows[_iterRow].id).attr('data-type', 'participant').attr('quiz-id', rows[_iterRow].quiz_id).attr('class-id', rows[_iterRow].class_id);

        $(_$row).append($('<td>').text(typeof rows[_iterRow].name == "string" ? rows[_iterRow].name.ShortenedString(35, true) : rows[_iterRow].name));
        $(_$row).append($('<td>').text(rows[_iterRow].class_name.ShortenedString(35, true)));
        $(_$row).append($('<td>').text(rows[_iterRow].quiz_title.ShortenedString(35, true)));
        $(_$row).append($('<td>').text('Session ' + moment(rows[_iterRow].sessions[sesIter].started_at).format("DD-MM-YYYY HH:mm")));
        $('.quick-access-area table tbody').append(_$row);
      }
    }
  }
}

function UpdateQuizDropdownSelected(id) {
  $('#quiz-dropdown').val(id);
  $('#quiz-dropdown').formSelect();
}

function UpdateClassDropdownSelected(id) {
  $('#class-dropdown').val(id);
  $('#class-dropdown').formSelect();
  var selected_quizzes = quizzes.filter(function (quiz) {
    return quiz.class_id == id;
  });
  PopulateQuizDropdown(selected_quizzes);
}

function PopulateScoresTableFoQuiz(rows) {
  $('.scores-table tbody tr').remove();
  $('th.name').contents().filter(function () {
    return this.nodeType == 3;
  }).first().replaceWith('User ID');
  $('th.date').contents().filter(function () {
    return this.nodeType == 3;
  }).first().replaceWith('Taken On');
  $('th.init').contents().filter(function () {
    return this.nodeType == 3;
  }).first().replaceWith('Init. Phase');
  $('th.rev').contents().filter(function () {
    return this.nodeType == 3;
  }).first().replaceWith('Rev. Phase');
  $('th.diff').contents().filter(function () {
    return this.nodeType == 3;
  }).first().replaceWith('Diff');

  if (rows instanceof Object) {
    rows = Object.keys(rows).map(function (key) {
      return rows[key];
    });
  }

  current_content = rows;
  AddRowsToMainTable(rows);
}

function PopulateScoresTableForParticipant(rows) {
  $('.scores-table tbody tr').remove();
  $('th.name').contents().filter(function () {
    return this.nodeType == 3;
  }).first().replaceWith('Quiz ID');
  $('th.date').contents().filter(function () {
    return this.nodeType == 3;
  }).first().replaceWith('Started on date');
  $('th.init').contents().filter(function () {
    return this.nodeType == 3;
  }).first().replaceWith('Init. Phase');
  $('th.rev').contents().filter(function () {
    return this.nodeType == 3;
  }).first().replaceWith('Rev. Phase');
  $('th.diff').contents().filter(function () {
    return this.nodeType == 3;
  }).first().replaceWith('Diff');

  if (rows instanceof Object) {
    rows = Object.keys(rows).map(function (key) {
      return rows[key];
    });
  }

  current_content = rows;
  AddRowsToMainTable(rows);
}

function SortByName(a, b) {
  if (a[0].name < b[0].name) {
    return -1;
  }

  if (a[0].name > b[0].name) {
    return 1;
  }

  return 0;
}

function SortByDate(a, b) {
  return new Date(b[0].started_at) - new Date(a[0].started_at);
}

function SortByInitPhase(a, b) {
  return a[0].score - b[0].score;
}

function SortByRevPhase(a, b) {
  if (a[1] === undefined) {
    a[1] = {
      score: 0
    };
  }

  if (b[1] === undefined) {
    b[1] = {
      score: 0
    };
  }

  return a[1].score - b[1].score;
}

function SortByDiff(a, b) {
  if (a[1] === undefined) {
    a[1] = {
      score: 0
    };
  }

  if (b[1] === undefined) {
    b[1] = {
      score: 0
    };
  }

  return a[1].score - a[0].score - (b[1].score - b[0].score);
}

function FilterByParticipant(text) {
  $('.quick-access-area').css('pointer-events', 'none');
  $.post({
    url: '/get-participant-score-overview',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
      participant: text
    }
  }).done(function (data) {
    var result = {};

    try {
      result = JSON.parse(data);
    } catch (e) {
      ShowGlobalMessage("Unable to retrieve the scores", 2);
      console.log(data);
    }

    if (result.status == true) {
      PopulateQuickSelectionTable(result.content, 'participant');
    } else {
      ShowGlobalMessage('An error occurred while fetching the participant\'s information.', 2);
    }
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.log("Error");
    ShowGlobalMessage('An error occurred while fetching the participant\'s information.', 2);
  }).always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
    $('.quick-access-area').css('pointer-events', 'initial');
  });
}

/***/ }),

/***/ 25:
/*!**************************************!*\
  !*** multi ./resources/js/scores.js ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\WORK\saga-project\resources\js\scores.js */"./resources/js/scores.js");


/***/ })

/******/ });