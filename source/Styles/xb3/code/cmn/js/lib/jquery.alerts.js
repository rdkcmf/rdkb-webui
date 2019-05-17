// jQuery Alert Dialogs Plugin
//
// Version 1.1
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 14 May 2009
//
// Visit http://abeautifulsite.net/notebook/87 for more information
//
// Usage:
//		jAlert( message, [title, callback] )
//		jConfirm( message, [title, callback] )
//		jPrompt( message, [value, title, callback] )
// 
// History:
//
//		1.00 - Released (29 December 2008)
//
//		1.01 - Fixed bug where unbinding would destroy all resize events
//
// License:
// 
// This plugin is dual-licensed under the GNU General Public License and the MIT License and
// is copyright 2008 A Beautiful Site, LLC. 
//
// Last Changed:			Nobel Huang
// Last Changed Date:		Nov 21, 2013
// Change History:
//		1	-	Using live region div to wrap the whole dialog elements in order to support
//				accessibility. (Nov 12, 2013)
//		2	-	Store the focus element and restore it after hide dialog. (Nov 21, 2013)
//		3	-	Manage the focus not to escape out the current dialog. (Nov 21, 2013)
//		4	-	Correct the ESCAPE key handler. (Nov 21, 2013)
//
(function($) {
	
	$.alerts = {
		
		// These properties can be read/written by accessing $.alerts.propertyName from your scripts at any time
		
		verticalOffset: -75,                // vertical offset of the dialog from center screen, in pixels
		horizontalOffset: 0,                // horizontal offset of the dialog from center screen, in pixels/
		repositionOnResize: true,           // re-centers the dialog on window resize
		overlayOpacity: .50,                // transparency level of overlay
		overlayColor: '#000',               // base color of overlay
		draggable: true,                    // make the dialogs draggable (requires UI Draggables plugin)
		okButton: '&nbsp;OK&nbsp;',         // text for the OK button
		cancelButton: '&nbsp;Cancel&nbsp;', // text for the Cancel button
		dialogClass: null,                  // if specified, this class will be applied to all dialogs
		liveRegionId: '__alertLiveCont',	// id of live region which used to hold alert content and support accessibility
		
		// Public methods
		
		alert: function(message, title, callback) {
			if( title == null ) title = 'Alert';
			$.alerts._show(title, message, null, 'alert', function(result) {
				if( callback ) callback(result);
			});
		},
		
		confirm: function(message, title, callback) {
			if( title == null ) title = 'Confirm';
			$.alerts._show(title, message, null, 'confirm', function(result) {
				if( callback ) callback(result);
			});
		},
			
		prompt: function(message, value, title, callback) {
			if( title == null ) title = 'Prompt';
			$.alerts._show(title, message, value, 'prompt', function(result) {
				if( callback ) callback(result);
			});
		},
		
		// Private methods

		_prepare: function() {
			/* capture the keydown event at capturing phase */
			if (document.addEventListener) {
				document.removeEventListener("keydown", $.alerts._keyHandler, true);
				document.addEventListener("keydown", $.alerts._keyHandler, true);
			}
			else {
				document.detachEvent("onkeydown", $.alerts._keyHandler);
				document.attachEvent("onkeydown", $.alerts._keyHandler);
			}

			if ($("#"+$.alerts.liveRegionId).length === 0) {
				/* create this live region first */
				$(document.body).append('<div id="'+$.alerts.liveRegionId+'" aria-live="true" aria-atomic="true" aria-relevant="additions"></div>');
				$("#"+$.alerts.liveRegionId).css({
					position: "absolute",
					zIndex: 99997,
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

			if ($("#popup_container").length === 0) {
				/* only functional when popup exists */
				return;
			}

			if (which === 9) { // this is TAB
				stopIt = true;
				var $set = $("#popup_container").find("input, a, select, textarea, button");
				var index = Math.max(0, $set.index($("#popup_container :focus")));

				var $nSet = withShift ? $set.slice(0, index) : $set.slice(index + 1);

				if ($nSet.length > 0) $nSet.eq(withShift ? Math.max(index - 1, 0) : 0).focus();
				else $set.eq(withShift ? Math.max(0, $set.length - 1) : 0).focus();
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

		_show: function(title, msg, value, type, callback) {
			
			if ($.alerts._prepare() === false) {
				setTimeout(function(){
					$.alerts._show(title, msg, value, type, callback);
				}, 150);
				return;
			}

			$.alerts._hide();
			$.alerts._overlay('show');

			$("#"+$.alerts.liveRegionId).append(
			  '<div id="popup_container">' +
			    '<h2 id="popup_title"></h2>' +
			    '<div id="popup_content">' +
			      '<div id="popup_message"></div>' +
				'</div>' +
			  '</div>');
			
			if( $.alerts.dialogClass ) $("#popup_container").addClass($.alerts.dialogClass);

			/* store the focus element before we change it to the dialog */
			$.alerts._focusManager("store");
			
			// IE6 Fix
			var pos = ($.browser.msie && parseInt($.browser.version) <= 6 ) ? 'absolute' : 'fixed'; 
			
			$("#popup_container").css({
				position: pos,
				zIndex: 99999,
				padding: 0,
				margin: 0
			});
			
			$("#popup_title").text(title);
			$("#popup_content").addClass(type);
			$("#popup_message").text(msg);
			$("#popup_message").html( $("#popup_message").text().replace(/\n/g, '<br />') );
			
			$("#popup_container").css({
				minWidth: $("#popup_container").outerWidth(),
				maxWidth: $("#popup_container").outerWidth()
			});
			
			$.alerts._reposition();
			$.alerts._maintainPosition(true);
			
			switch( type ) {
				case 'alert':
					$("#popup_message").after('<div id="popup_panel"><input type="button" value="' + $.alerts.okButton + '" id="popup_ok" class="btn" /></div>');
					$("#popup_ok").click( function() {
						$.alerts._hide();
						callback(true);
					});
					$("#popup_ok").focus().keypress( function(e) {
						if( e.keyCode == 13 ) {
							e.preventDefault();
							e.stopPropagation();
							$("#popup_ok").trigger('click');
						}
					}).keydown(function(e) {
						if (e.keyCode == 27) {
							e.preventDefault();
							e.stopPropagation();
							$("#popup_ok").trigger('click');
						}
					});
				break;
				case 'confirm':
					$("#popup_message").after('<div id="popup_panel"><input type="button" value="' + $.alerts.okButton + '" id="popup_ok" class="btn" /> <input type="button" value="' + $.alerts.cancelButton + '" id="popup_cancel" class="btn alt" /></div>');
					$("#popup_ok").click( function() {
						$.alerts._hide();
						if( callback ) callback(true);
					});
					$("#popup_cancel").click( function() {
						$.alerts._hide();
						if( callback ) callback(false);
					});
					$("#popup_ok").focus();
					$("#popup_ok, #popup_cancel").keypress( function(e) {
						if( e.keyCode == 13 ) {
							e.preventDefault();
							e.stopPropagation();
							$("#"+e.currentTarget.id).trigger('click');
						}
					}).keydown(function(e) {
						if( e.keyCode == 27 ) {
							e.preventDefault();
							e.stopPropagation();
							$("#popup_cancel").trigger('click');
						}
					});
				break;
				case 'prompt':
					$("#popup_message").append('<br /><input type="text" size="30" id="popup_prompt" class="btn" />').after('<div id="popup_panel"><input type="button" value="' + $.alerts.okButton + '" id="popup_ok" class="btn" /> <input type="button" value="' + $.alerts.cancelButton + '" id="popup_cancel" class="btn alt" /></div>');
					$("#popup_prompt").width( $("#popup_message").width() );
					$("#popup_ok").click( function() {
						var val = $("#popup_prompt").val();
						$.alerts._hide();
						if( callback ) callback( val );
					});
					$("#popup_cancel").click( function() {
						$.alerts._hide();
						if( callback ) callback( null );
					});
					$("#popup_prompt, #popup_ok, #popup_cancel").keypress( function(e) {
						if( e.keyCode == 13 ) {
							var id = e.currentTarget.id;
							if (id == "popup_prompt") id = "popup_ok";
							e.preventDefault();
							e.stopPropagation();
							$("#"+id).trigger('click');
						}
					}).keydown(function(e) {
						if( e.keyCode == 27 ) {
							e.preventDefault();
							e.stopPropagation();
							$("#popup_cancel").trigger('click');
						}
					});
					if( value ) $("#popup_prompt").val(value);
					$("#popup_prompt").focus().select();
				break;
			}
			
			// Make draggable
			if( $.alerts.draggable ) {
				try {
					$("#popup_container").draggable({ handle: $("#popup_title") });
					$("#popup_title").css({ cursor: 'move' });
				} catch(e) { /* requires jQuery UI draggables */ }
			}
			$("#popup_ok").blur();
		},
		
		_hide: function() {
			/* restore the focus element */
			$.alerts._focusManager("restore");
			$("#popup_container").remove();
			$.alerts._overlay('hide');
			$.alerts._maintainPosition(false);
			$("#"+$.alerts.liveRegionId).css({width: "0px", height: "0px"});
		},
		
		_overlay: function(status) {
			switch( status ) {
				case 'show':
					$.alerts._overlay('hide');
					$("#"+$.alerts.liveRegionId).css({width: "100%"}).height($(document).height());
					$("#"+$.alerts.liveRegionId).append('<div id="popup_overlay"></div>');
					$("#popup_overlay").css({
						position: 'absolute',
						zIndex: 99998,
						top: '0px',
						left: '0px',
						width: '100%',
						height: $(document).height(),
						background: $.alerts.overlayColor,
						opacity: $.alerts.overlayOpacity
					});
				break;
				case 'hide':
					$("#popup_overlay").remove();
				break;
			}
		},

		_focusManager: function(method) {
			var $dialogRoot = $("#popup_container");

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
		
		_reposition: function() {
			var top = (($(window).height() / 2) - ($("#popup_container").outerHeight() / 2)) + $.alerts.verticalOffset;
			var left = (($(window).width() / 2) - ($("#popup_container").outerWidth() / 2)) + $.alerts.horizontalOffset;
			if( top < 0 ) top = 0;
			if( left < 0 ) left = 0;
			
			// IE6 fix
			if( $.browser.msie && parseInt($.browser.version) <= 6 ) top = top + $(window).scrollTop();
			
			$("#popup_container").css({
				top: top + 'px',
				left: left + 'px'
			});
			$("#"+$.alerts.liveRegionId).height($(document).height());
			$("#popup_overlay").height( $(document).height() );
		},
		
		_maintainPosition: function(status) {
			if( $.alerts.repositionOnResize ) {
				switch(status) {
					case true:
						$(window).bind('resize', $.alerts._reposition);
					break;
					case false:
						$(window).unbind('resize', $.alerts._reposition);
					break;
				}
			}
		}
		
	}

	$(document).ready(function(){$.alerts._prepare();});
	
	// Shortuct functions
	jAlert = function(message, title, callback) {
		$.alerts.alert(message, title, callback);
	}
	
	jConfirm = function(message, title, callback) {
		$.alerts.confirm(message, title, callback);
	};
		
	jPrompt = function(message, value, title, callback) {
		$.alerts.prompt(message, value, title, callback);
	};
	
})(jQuery);
