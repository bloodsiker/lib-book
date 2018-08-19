/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	"use strict";

	var _smoothscroll_menu = __webpack_require__(1);

	var _smoothscroll_menu2 = _interopRequireDefault(_smoothscroll_menu);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	var param = new _smoothscroll_menu2.default({ menuID: "anchor-menu", fixMenu: true });

	function showHideMenu(event) {
	    event.preventDefault();
	    document.getElementById('menu').classList.toggle('-open');
	}
	var menuSandwich = document.getElementById("menu-sandwich");
	menuSandwich.addEventListener('click', function (event) {
	    showHideMenu(event);
	}, false);

/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

	var _smoothscroll_helper = __webpack_require__(2);

	var _smoothscroll_helper2 = _interopRequireDefault(_smoothscroll_helper);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

	var smoothScrollMenu = function () {
	    function smoothScrollMenu(param) {
	        _classCallCheck(this, smoothScrollMenu);

	        this.param = {
	            menuID: "anchor-menu",
	            scrollSpeed: 500,
	            fixMenu: false,
	            menuHeight: 0
	        };
	        this.param = Object.assign(this.param, param);
	        this.param.menu = document.getElementById(this.param.menuID);
	        this.param.menuItems = Array.prototype.slice.call(this.param.menu.querySelectorAll('a[data-anchor]'));

	        this.setEvents();

	        if (this.param.fixMenu) {
	            this.param.menuHeight = document.getElementById(this.param.menuID).offsetHeight;
	        }

	        this.scrollMethods = new _smoothscroll_helper2.default({});
	    }

	    _createClass(smoothScrollMenu, [{
	        key: "setEvents",
	        value: function setEvents() {
	            var _this = this;

	            this.param.menuItems.forEach(function (el) {
	                el.addEventListener('click', function (event) {
	                    event.preventDefault();
	                    _this.scrollTo(event.currentTarget);
	                });
	            });

	            var timer = null;
	            window.addEventListener('scroll', function () {
	                if (timer !== null) {
	                    clearTimeout(timer);
	                }
	                timer = setTimeout(function () {
	                    _this.setCurrentMenu();
	                }, 150);
	            }, false);
	        }
	    }, {
	        key: "scrollTo",
	        value: function scrollTo(scrollToElement) {
	            var scrollToY = document.getElementById(scrollToElement.getAttribute('data-anchor')).offsetTop - this.param.menuHeight;
	            this.scrollMethods.smoothScroll(scrollToY, 1000, function () {
	                /*this.setCurrentMenu(scrollToElement)*/document.getElementById('menu').classList.remove('-open');
	            });
	        }
	    }, {
	        key: "setCurrentMenu",
	        value: function setCurrentMenu() {
	            var _this2 = this;

	            var scrollPosition = arguments.length <= 0 || arguments[0] === undefined ? window.pageYOffset : arguments[0];


	            this.param.menuItems.forEach(function (el) {
	                el.classList.remove("-active");
	            });

	            if (scrollPosition == 0) {
	                this.param.menuItems[0].classList.add("-active");
	                return true;
	            }
	            if (scrollPosition + window.innerHeight >= document.body.offsetHeight) {
	                this.param.menuItems[this.param.menuItems.length - 1].classList.add("-active");
	                return true;
	            }
	            this.param.menuItems.every(function (el, key, array) {

	                var sectionId = el.getAttribute('data-anchor');
	                var sectionTopPosition = document.getElementById(sectionId).offsetTop - _this2.param.menuHeight;

	                if (scrollPosition < sectionTopPosition) {
	                    if (key == 0) {
	                        array[key - 1].classList.add('-active');
	                        return false;
	                    }
	                    array[key - 1].classList.add('-active');
	                    return false;
	                } else {
	                    if (key == array.length - 1) {
	                        array[key].classList.add('-active');
	                    }
	                }
	                return true;
	            });
	        }
	    }]);

	    return smoothScrollMenu;
	}();

	;

	exports.default = smoothScrollMenu;

/***/ },
/* 2 */
/***/ function(module, exports) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	    value: true
	});

	var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

	var smoothscroll_helper = function () {
	    function smoothscroll_helper() {
	        _classCallCheck(this, smoothscroll_helper);
	    }

	    _createClass(smoothscroll_helper, [{
	        key: '_getTop',


	        //var smoothScroll = require('smoothscroll');

	        value: function _getTop(element) {
	            // return value of html.getBoundingClientRect().top ... IE : 0, other browsers : -pageYOffset
	            if (element.nodeName === 'HTML') return -window.pageYOffset;
	            return element.getBoundingClientRect().top + window.pageYOffset;
	        }
	    }, {
	        key: '_easeInOutCubic',

	        // ease in out function thanks to:
	        // http://blog.greweb.fr/2012/02/bezier-curve-based-easing-functions-from-concept-to-implementation/
	        value: function _easeInOutCubic(t) {
	            return t < .5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
	        }
	    }, {
	        key: '_position',


	        // calculate the scroll position we should be in
	        // given the start and end point of the scroll
	        // the time elapsed from the beginning of the scroll
	        // and the total duration of the scroll (default 500ms)
	        value: function _position(start, end, elapsed, duration) {
	            if (elapsed > duration) return end;
	            return start + (end - start) * this._easeInOutCubic(elapsed / duration); // <-- you can change the easing funtion there
	            // return start + (end - start) * (elapsed / duration); // <-- this would give a linear scroll
	        }
	    }, {
	        key: 'smoothScroll',
	        value: function smoothScroll(el, duration, callback, context) {
	            var _this = this;

	            duration = duration || 500;
	            context = context || window;
	            var start = window.pageYOffset;

	            if (typeof el === 'number') {
	                var end = parseInt(el);
	            } else {
	                var end = this._getTop(el);
	            }

	            var clock = Date.now();
	            var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || function (fn) {
	                window.setTimeout(fn, 15);
	            };

	            var step = function step() {
	                var elapsed = Date.now() - clock;
	                if (context !== window) {
	                    context.scrollTop = _this._position(start, end, elapsed, duration);
	                } else {
	                    window.scroll(0, _this._position(start, end, elapsed, duration));
	                }

	                if (elapsed > duration) {
	                    if (typeof callback === 'function') {
	                        callback(el);
	                    }
	                } else {
	                    requestAnimationFrame(step);
	                }
	            };
	            step();
	        }
	    }]);

	    return smoothscroll_helper;
	}();

	exports.default = smoothscroll_helper;

/***/ }
/******/ ]);