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
/******/ 	return __webpack_require__(__webpack_require__.s = 24);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/leaderboard.js":
/*!*************************************!*\
  !*** ./resources/js/leaderboard.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  console.log('page loaded');
  $('select').formSelect();
  LoadCharts();
  $('.class-filter').on('change', function () {
    filter_quizzes('class', $(this).find('option:selected').val());
  });
});

function LoadCharts() {
  // Load the Visualization API and the corechart package.
  google.charts.load('current', {
    packages: ['corechart', 'line']
  }); // Set a callback to run when the Google Visualization API is loaded.

  google.setOnLoadCallback(function () {
    DrawTimesChart('times_chart', $('#times_chart').attr('data-chart'));
    DrawScoresChart('scores_chart', $('#scores_chart').attr('data-chart'));
  });
}

function filter_quizzes(filter_name, filter_id) {
  window.location = '/leaderboards/' + filter_id;
}

function DrawTimesChart(identifier, info_data) {
  var user_value = $('#' + identifier).attr('data-user-value');
  info_data = JSON.parse(info_data);

  for (var iter = 0; iter < info_data.length; iter++) {
    if (user_value == parseFloat(info_data[iter])) {
      info_data[iter] = [iter + 1, parseFloat(info_data[iter]), 'Your time', user_value + 's'];
    } else {
      info_data[iter] = [iter + 1, parseFloat(info_data[iter]), null, null];
    }
  } // Create the data table.


  var data = new google.visualization.DataTable();
  data.addColumn('number', 'X');
  data.addColumn('number', 'Times');
  data.addColumn({
    type: 'string',
    role: 'annotation'
  });
  data.addColumn({
    type: 'string',
    role: 'annotationText'
  });
  data.addRows(info_data);
  var title = 'chart'; // Set chart options

  var options = {
    0: {// set any applicable options on the first series
    },
    1: {
      // set the options on the second series
      lineWidth: 0,
      pointSize: 5,
      visibleInLegend: false
    },
    legend: 'none',
    colors: ['#26a69a'],
    hAxis: {
      title: '',
      textColor: '#ffffff'
    },
    vAxis: {
      title: 'Time'
    }
  };
  _progress_initial_chart = new google.visualization.LineChart(document.getElementById(identifier));

  _progress_initial_chart.draw(data, options);
}

function DrawScoresChart(identifier, info_data) {
  var user_value = $('#' + identifier).attr('data-user-value');
  info_data = JSON.parse(info_data);
  info_data = Object.keys(info_data).map(function (key) {
    return parseFloat(info_data[key]);
  });
  console.log(info_data);
  info_data = info_data.sort(function (a, b) {
    return a - b;
  });
  console.log(info_data);

  for (var iter = 0; iter < info_data.length; iter++) {
    if (user_value == parseFloat(info_data[iter])) {
      info_data[iter] = [parseFloat(info_data[iter]), iter + 1, 'Your score', user_value];
    } else {
      info_data[iter] = [parseFloat(info_data[iter]), iter + 1, null, null];
    }

    console.log(info_data[iter]);
  } // Create the data table.


  var data = new google.visualization.DataTable();
  data.addColumn('number', 'Scores');
  data.addColumn('number', 'X');
  data.addColumn({
    type: 'string',
    role: 'annotation'
  });
  data.addColumn({
    type: 'string',
    role: 'annotationText'
  });
  data.addRows(info_data);
  var title = 'chart'; // Set chart options

  var options = {
    0: {// set any applicable options on the first series
    },
    1: {
      // set the options on the second series
      lineWidth: 0,
      pointSize: 5,
      visibleInLegend: false
    },
    colors: ['#26a69a'],
    orientation: 'vertical',
    legend: 'none',
    hAxis: {
      title: '',
      textColor: '#ffffff'
    },
    vAxis: {
      title: 'Scores'
    }
  };
  _progress_initial_chart = new google.visualization.LineChart(document.getElementById(identifier));

  _progress_initial_chart.draw(data, options);
}

/***/ }),

/***/ 24:
/*!*******************************************!*\
  !*** multi ./resources/js/leaderboard.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\WORK\saga-project\resources\js\leaderboard.js */"./resources/js/leaderboard.js");


/***/ })

/******/ });