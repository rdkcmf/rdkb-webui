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
 * jquery.radioswitch.js
 *
 * jQuery plugin to construct a switch widget using radio buttons.
 * A switch widget state is either "on" or "off".
 *
 * The usage is similar with jQueryUI.
 *
 * This widget also supports accessibility.
 *
 * Author: Nobel Huang (xiaoyhua)
 * Date: Nov 6, 2013
 */
(function($) {
	
	$.fn.radioswitch = function(arg) {
		var invokeType = "init";
		var options = null;
		var func = null;
		var argArray = $.makeArray(arguments);
		var chainRes = null;
		var funcRes = null;

		if (arguments.length === 1 && typeof(arg) === 'object') {
			/* arguments for initialization */
			options = $.extend({}, $.fn.radioswitch.defaults, arg);
			func = $.fn.radioswitch.methods["__init"];
			invokeType = "init";
		}
		else if (typeof(arg) === 'string' && typeof($.fn.radioswitch.methods[arg]) === "function") {
			func = $.fn.radioswitch.methods[arg];
			argArray.shift(); // shift out the first arg which indicate the method name
			invokeType = "func";
		}
		else {
			/* invalid invoking */
			if (console) {
				console.error("radioswitch invoking is incorrect!");
				return;
			}
		}

		chainRes = this;
		this.each(function() {
			switch (invokeType) {
				case "init":
					func.call(this, options);
					break;
				case "func":
					funcRes = func.apply(this, argArray);
					if ($.inArray(arg, $.fn.radioswitch.nonChainMethods) >= 0) {
						chainRes = funcRes;
						return false;
					}
					break;
			}
		});

		return chainRes;
	};

	$.fn.radioswitch.defaults = {
		id: "radioswitch",
		id_on: "switch_on",
		id_off: "switch_off",
		radio_name: "switch_radio",
		label_on: "Attiva",
		label_off: "Disattiva",
		title_on: "Abilita questo interruttore",
		title_off: "Disabilita questo interruttore",
		size: "normal",
		revertOrder: false,
		state: "on"
	};
	$.fn.radioswitch.nonChainMethods = [
		"getState"
	];
	$.fn.radioswitch.methods = {
		/* private functions */
		__init: function(opts) {
			var $this = $(this);
			var $root = $('<ul></ul>');

			$this.addClass("radioswitch_cont");

			/* init the root structure dom */
			$root.attr("id", opts.id).attr({
				role: "radiogroup"
			}).addClass("rs_radiolist");
			if (opts.size === "small") {
				$root.addClass("rs_size_small");
			}

			function __addButton(type) {
				$root.append(
					$('<a tabindex="0" role="radio"></a>').attr({
						title: $this.prev().text() + (type==='on' ? opts.title_on : opts.title_off),
						"aria-checked": (type==='on' ? "true" : "false"),
						"aria-selected": (type==='on' ? "true" : "false")
					}).append(
						$('<li></li>').addClass(type==='on' ? "radioswitch_on" : "radioswitch_off rs_radio_off").append(
							$('<input type="radio"/>').attr({
								id: (type==='on' ? opts.id_on : opts.id_off),
								name: opts.radio_name,
								value: (type==='on' ? "Attivo" : "Non attivo")
							}).attr("cheched", (type==='on' ? true : false))
						).append(
							$('<label></label>').attr("for", (type==='on' ? opts.id_on : opts.id_off)).text((type==='on' ? opts.label_on : opts.label_off))
						)
					)
				);
			};

			
			/* add switch buttons */
			var btnTypes = ['on', 'off'];
			if (opts.revertOrder) {
				btnTypes.reverse();
			}
			$.each(btnTypes, function(idx, elem) {
				__addButton(elem);
			});

			/* add into dom tree for show */
			$this.append($root);

			/* save the state */
			$this.data("radioswitchstates", {on: opts.state === 'on', enabled: true});

			/* init the state */
			$.fn.radioswitch.methods.__doSwitch.call(this, opts.state, true);

			/* bind event handles */
			$this.find("a[role]").unbind("click").bind("click keypress", function(e) {
				if (e.type === 'keypress' && e.which !== 13) {
					return;
				}
				e.preventDefault();
				e.stopPropagation();

				var $this = $(this);
				var $cont = $this.closest(".radioswitch_cont");
				var states = $cont.data("radioswitchstates");

				$this.focus();

				if (!states.enabled || $this.children("li").hasClass("rs_selected")) {
					return;
				}

				$.fn.radioswitch.methods.doSwitch.call($this.closest(".radioswitch_cont")[0],
													   (states.on ? "off" : "on"),
													   true);
			});
		},
		__doSwitch: function(value, /* internal use */ force) {
			var $this = $(this);
			var switchKey = {on: "off", off: "on"};

			var states = $this.data("radioswitchstates");
			var changed = (value === 'on') !== states.on;

			if (changed || force) {
				$this.find("li.radioswitch_" + value).addClass("rs_selected").
					find("input").attr("checked", true);
				$this.find("li.radioswitch_" + switchKey[value]).removeClass("rs_selected").
					find("input").attr("checked", false);

				$this.find("a[role]").each(function(){
					var v = $(this).find("input").attr("checked") ? "true" : "false";
					$(this).attr({"aria-checked": v, "aria-selected": v});
				});

				states.on = (value === 'on');
				$this.data("radioswitchstates", states);
			}

			return changed;
		},

		/* public functions */
		option: function(name, value) {
			/* currently not allow to change options after initialization */
		},

		/* switch the widget to state "on"/"off", if state changed, a "change" event
		 * will be triggered on container element.
		 *
		 * @param		value (string) - "on"/"off"
		 * @param		onfire (boolean) - whether trigger the change event if state changed
		 *
		 * @return		(boolean) true if state changed
		 */
		doSwitch: function(value, onfire) {
			var states = $(this).data("radioswitchstates");
			if (!states.enabled) {
				/* prevent switch if this is disabled */
				return false;
			}
			if ($.fn.radioswitch.methods.__doSwitch.call(this, value)) {
				if (onfire) {
					/* fire the changed event if state changed */
					$(this).trigger("change");
				}
				return true;
			}
			return false;
		},

		/* Enable/disable the whole widget. Once this widget is disabled, any events
		 * on it would not get response.
		 *
		 * @return		(boolean) true if enable/disable state changed.
		 */
		doEnable: function(isEnable) {
			var $this = $(this);
			var states = $this.data("radioswitchstates");
			if (states.enabled === isEnable) {
				return false;
			}

			if (isEnable === true) {
				$this.children(".rs_radiolist").removeClass("disabled");
			}
			else {
				$this.children(".rs_radiolist").addClass("disabled");
			}

			states.enabled = (isEnable === true);
			$this.data("radioswitchstates", states);

			return true;
		},

		/* Retrieve widget state in an object like {(boolean)on, (boolean)enabled}
		 */
		getState: function() {
			var states = $(this).data("radioswitchstates");

			return {on: states.on, enabled: states.enabled};
		}

	};
}(jQuery));
