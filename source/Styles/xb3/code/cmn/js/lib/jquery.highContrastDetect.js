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
 * jquery.highContrastDetect.js
 *
 * jQuery plugin to enable detecting high contrast mode in Windows when this
 * plugin is invoked.
 *
 * Author: Nobel Huang (xiaoyhua)
 * Date: Oct 23, 2013
 */
(function($){
	$.highContrastDetect = function(options) {
		var defaults = {
			divId: "__highContrastDetectDiv",
			bgImgSrc: "cmn/img/icn_on_off.png",
			useExtraCss: false,
			cssPath: "./cmn/css/highContrast.css",
			debugInNormalMode: false
		};

		options = $.extend(defaults, options);

		/* create a div with background */
		var testDiv = $("<div></div>");
		testDiv.attr("id", options.divId).css({
			width: "0px",
			height: "0px",
			background: "url(" + options.bgImgSrc + ")"
		}).appendTo(document.body);

		/* check the background-image */
		$.__isHighContrast = false;
		if (testDiv.css("background-image") === "none" || options.debugInNormalMode) {
			/* yes, it is under high contrast mode */
			$.__isHighContrast = true;
			if (options.useExtraCss) {
				$("head").append('<link rel="stylesheet" type="text/css" title="High Contrast Overwrite Style" href="' + options.cssPath + '" />');
			}
		}

		/* remove the test div */
		testDiv.remove();
		testDiv = null;
	};
}(jQuery));
