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

/***/ "./resources/js/page.ui-charts.js":
/*!****************************************!*\
  !*** ./resources/js/page.ui-charts.js ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function () {
  'use strict';

  var Performance = function Performance(id) {
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'line';
    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    var data = {
      labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: [{
        label: "Performance",
        data: [0, 10, 5, 15, 10, 20, 15, 25, 20, 30, 25, 40]
      }]
    };
    Charts.create(id, type, options, data);
  };
  var Orders = function Orders(id) {
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'roundedBar';
    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    var data = {
      labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: [{
        label: "Sales",
        data: [25, 20, 30, 22, 17, 10, 18, 26, 28, 26, 20, 32],
        barThickness: 20
      }]
    };
    Charts.create(id, type, options, data);
  };
  var Devices = function Devices(id) {
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'doughnut';
    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    var data = {
      labels: ["Desktop", "Tablet", "Mobile"],
      datasets: [{
        data: [60, 25, 15],
        backgroundColor: [],
        hoverBorderColor: settings.colors.white
      }]
    };
    Charts.create(id, type, options, data);
  };
  var TopicIQChart = function TopicIQChart(id) {
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'radar';
    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    var data = {
      labels: ["JavaScript", "HTML", "Flinto", "Vue.js", "Sketch", "Priciple", "CSS", "Angular"],
      datasets: [{
        label: "Experience IQ",
        data: [30, 35, 33, 32, 31, 30, 28, 36],
        pointHoverBorderColor: settings.colors.accent[400],
        pointHoverBackgroundColor: settings.colors.white
      }]
    };
    Charts.create(id, type, options, data);
  };
  var Progress = function Progress(id, value, total) {
    var type = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'doughnut';
    var options = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : {};
    options = Chart.helpers.merge({
      cutoutPercentage: 85,
      aspectRatio: 1,
      responsive: false,
      maintainAspectRatio: false
    }, options);
    var data = {
      datasets: [{
        data: [value, total - value],
        borderWidth: 0
      }]
    };
    Charts.create(id, type, options, data);
  };

  ///////////////////
  // Create Charts //
  ///////////////////

  Progress('#inTimeProgressChart', 24.84, 27);
  Progress('#lateProgressChart', 6.21, 27);
  Progress('#absentsProgressChart', 1.62, 27);
  Progress('#vacationProgressChart', 0.27, 27);
  Performance('#performanceChart');
  Performance('#performanceAreaChart', 'area');
  Orders('#ordersChart');
  Orders('#ordersChartSwitch');
  Devices('#devicesChart');
  TopicIQChart('#topicIqChart');
  $('[data-toggle="chart"]:checked').each(function (index, el) {
    Charts.add($(el));
  });
})();

/***/ }),

/***/ 24:
/*!**********************************************!*\
  !*** multi ./resources/js/page.ui-charts.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\Users\Dipa\Documents\Football LMS\luma-education-html-learning-management-system-2023-11-27-04-51-05-utc\luma-v2.0.0\resources\js\page.ui-charts.js */"./resources/js/page.ui-charts.js");


/***/ })

/******/ });