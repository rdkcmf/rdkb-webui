/**
 * jquery.highContrastDetect.js
 *
 * All right reserved by Cisco Systems.
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
