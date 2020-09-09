/*
 * If not stated otherwise in this file or this component's Licenses.txt file the
 * following copyright and licenses apply:
 *
 * Copyright 2015 RDK Management
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
*/

/**********************************************************************
   Copyright [2018] [Cisco Systems, Inc.]
 
   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at
 
       http://www.apache.org/licenses/LICENSE-2.0
 
   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
**********************************************************************/

/**
 * jquery.virtualDialog.js
 *
 * jQuery plugin for creating a general virtual dialog popup.
 *
 * This widget also supports accessibility.
 *
 * Author: Nobel Huang (xiaoyhua)
 * Date: Nov 22, 2013
 */
(function($){

	$.virtualDialog = function(arg) {
		var invokeType = "init";
		var options = null;
		var func = null;
		var argArray = $.makeArray(arguments);
		var funcRes = null;

		if (arguments.length === 1 && typeof(arg) === 'object') {
			/* arguments for initialization */
			options = $.extend({}, $.virtualDialog.defaults, arg);
			func = $.virtualDialog.methods["__init"];
			invokeType = "init";
		}
		else if (typeof(arg) === 'string' && typeof($.virtualDialog.methods[arg]) === "function") {
			func = $.virtualDialog.methods[arg];
			argArray.shift(); // shift out the first arg which indicate the method name
			invokeType = "func";
		}
		else {
			/* invalid invoking */
			if (console) {
				console.error("virtualDialog invoking is incorrect!");
				return;
			}
		}

		switch (invokeType) {
			case "init":
				func.call(document.body, options);
			break;
			case "func":
				funcRes = func.apply(document.body, argArray);
			break;
		}

		return funcRes;
	};

	$.virtualDialog.defaults = {
		title: "Dialog",
		content: "dummy content",					// can be plain string, function, or a jquery object
		footer: "",
		width: "450px",
		maskOpacity: .50,							// transparency level of mask
		maskColor: '#000'							// base color of mask
	};
	$.virtualDialog.internal = {
		liveRegionId: '__vdialLiveCont'				// id of live region which used to hold alert content and support accessibility
	};

	$.virtualDialog.methods = {
		/* private functions */
		__init: function(opts) {
			/* create and show the virtual dialog */
			var $this = $(this);
			var $root = $("#"+$.virtualDialog.internal.liveRegionId);

			if ($.virtualDialog.methods.__prepare() === false) {
				var func = arguments.callee;
				var caller = arguments.caller;
				setTimeout(function(){
					func.call(caller, opts);
				}, 150);
				return;
			}

			var $mask = $('<div id="vpop_mask"></div>').css({
				position: "fixed",
				zIndex: 80001,
				top: 0,
				left: 0,
				height: "100%",
				width: "100%",
				opacity: opts.maskOpacity,
				display: "none"
			});

			// IE6 Fix
			var pos = ($.browser.msie && parseInt($.browser.version) <= 6 ) ? 'absolute' : 'fixed'; 

			var $container = $('<div id="vpop_container"></div>').css({
				position: pos,
				zIndex: 80002,
				height: "auto",
				width: opts.width,
				border: "8px solid #FFF",
				display: "none"
			});

			/* title content */
			$container.append($('<div id="vpop_title"></div>').append('<div class="title_content"></div>'));
			$container.find(".title_content").html(opts.title);

			/* dialog content */
			$container.append($('<div id="vpop_content"></div>').append('<div class="content_content"></div>'));
			var $content = $container.find("#vpop_content .content_content");
			if (typeof(opts.content) === 'string') {
				$content.html(opts.content);
			}
			else if (typeof(opts.content) === 'function') {
				$content.html((opts.content).call($content[0]));
			}
			else if (typeof(opts.content) === 'object' && opts.content.jquery !== undefined
					&& opts.content.length === 1) {
				var isAttached = $.contains(document.documentElement, opts.content[0]);
				opts.content.appendTo($content).show();
				if (isAttached) $container.data("restoreContent", true);
			}

			/* footer content */
			$container.append($('<div id="vpop_footer"></div>').append('<div class="footer_content"></div>'));
			$container.find("#vpop_footer .footer_content").html(opts.footer);

			/* show it out */
			$root.css({width: "100%"}).height($(document).height());
			$root.append($mask).append($container);
			$.virtualDialog.methods.__position();
			$.virtualDialog.methods.__focusManager("store");

			$mask.fadeIn();
			$container.show();
		},

		__prepare: function() {
			/* capture the keydown event at capturing phase */
			if (document.addEventListener) {
				document.removeEventListener("keydown", $.virtualDialog.methods._keyHandler, true);
				document.addEventListener("keydown", $.virtualDialog.methods._keyHandler, true);
			}
			else {
				document.detachEvent("onkeydown", $.virtualDialog.methods._keyHandler);
				document.attachEvent("onkeydown", $.virtualDialog.methods._keyHandler);
			}

			if ($("#"+$.virtualDialog.internal.liveRegionId).length === 0) {
				/* create this live region first */
				$(document.body).append('<div id="'+$.virtualDialog.internal.liveRegionId+'" aria-live="true" aria-atomic="true" aria-relevant="additions"></div>');
				$("#"+$.virtualDialog.internal.liveRegionId).css({
					position: "absolute",
					zIndex: 80000,
					top: "0px",
					left: "0px",
					padding: 0,
					margin: 0,
					width: "100%",
					height: "0px"
				});
				return false;
			}

			return true;
		},

		_keyHandler: function(event) {
			var e = event || window.event;
			var which = e.charCode || e.keyCode;
			var withShift = e.shiftKey;
			var stopIt = false;

			if ($("#vpop_container").length === 0) {
				/* only functional when popup exists */
				return;
			}

			if (which === 9) { // this is TAB
				stopIt = true;
				var $set = $("#vpop_container").find("input, a, select, textarea, button");
				var index = Math.max(0, $set.index($("#vpop_container :focus")));

				var $nSet = withShift ? $set.slice(0, index) : $set.slice(index + 1);

				if ($nSet.length > 0) $nSet.eq(withShift ? Math.max(index - 1, 0) : 0).focus();
				else $set.eq(withShift ? Math.max(0, $set.length - 1) : 0).focus();
			}
			else if (which === 27) { // this is ESC
				stopIt = true;
				setTimeout(function(){
					$.virtualDialog.methods.hide();
				}, 100);
			}

			if (stopIt) {
				if (e.stopPropagation) {
					e.preventDefault();
					e.stopPropagation();
				}
				else {
					e.returnValue = false;
					e.cancelBubble = true;
				}
			}
		},

		__focusManager: function(method) {
			var $dialogRoot = $("#vpop_container");

			if ($dialogRoot.length === 0) {
				return;
			}

			switch (method) {
				case "store":
				{
					var $focusElem = $(document.activeElement);
					var focusableArr = ["a", "input", "select", "button", "textarea", "*[tabindex]"];
					if (!$focusElem.is(focusableArr.join(","))) {
						var i = 0;
						var $focusable = $(focusableArr[i++]);
						while (i < focusableArr.length && $focusable.length === 0) {
							$focusable.add(focusableArr[i++]);
						}
						if ($focusable.length === 0) {
							/* there is not obvious focusable element, still use the current active element */
						}
						else {
							$focusElem = $focusable.eq(0);
						}
					}
					$dialogRoot.data("__initiator", $focusElem[0]);
				}
				break;

				case "restore":
				{
					var elem = $dialogRoot.data("__initiator");
					if (elem === null || elem === undefined) {
						return;
					}
					$dialogRoot.data("__initiator", null);
					$(elem).focus();
				}
				break;
			}
		},

		__position: function() {
			var top = (($(window).height() / 2) - ($("#vpop_container").outerHeight() / 2));
			var left = (($(window).width() / 2) - ($("#vpop_container").outerWidth() / 2));
			if( top < 0 ) top = 0;
			if( left < 0 ) left = 0;

			// IE6 fix
			if( $.browser.msie && parseInt($.browser.version) <= 6 ) top = top + $(window).scrollTop();

			$("#vpop_container").css({
				top: top + 'px',
				left: left + 'px'
			});
			$("#"+$.virtualDialog.internal.liveRegionId).css({width: "100%"}).height($(document).height());
		},

		/* public functions */
		hide: function() {
			var $root = $("#" + $.virtualDialog.internal.liveRegionId);
			$.virtualDialog.methods.__focusManager("restore");
			$("#vpop_container").hide();
			$("#vpop_mask").fadeOut(400, function() {
				/* restore content if needed */
				if ($("#vpop_container").data("restoreContent") === true) {
					$("#vpop_container .content_content").children().eq(0).hide().appendTo(document.body);
				}

				$(this).remove();
				$("#vpop_container").remove();
				$root.css({width: "0px", height: "0px"});
			});
		}
	};

	$(document).ready(function(){$.virtualDialog.methods.__prepare();});

}(jQuery));
