// jQuery Alert Progress Dialogs Plugin 
//
// Based on jQuery Alert Dialogs Plugin
//
// Usage:
//	jProgress( message[, value, title, callback])
//		popup a window with a pseudo progress bar without any buttons.
//			message: the window content text, to show user message about this operation, especially the estimate time.
//			value: time (unit seconds), when operation last longer than the value,  window content will become "Operation timeout" with an OK button to close the window. Default is 0, which means the window will never disappear unless you call jHide() or close or refresh your page. 
//			title: the window title, default is "Operation In Progress".
//			callback: callback when close the window.
//	jHide()
//		close the jProgress bar.	
//
(function($) {
	
	$.palerts = {
		
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
		timeoutId: null,
		// Public methods
		
		
		progress: function(message, value, title, callback) {
			if( value == null ) value = 0;
			if( title == null ) title = 'Operation In Progress';
			$.palerts._show(title, message, value, 'progress', function(result) {
				if( callback ) callback(result);
			});
		},
		// Private methods
		
		_show: function(title, msg, value, type, callback) {
			
			if ($.alerts._prepare() === false) {
				setTimeout(function(){
					$.palerts._show(title, msg, value, type, callback);
				}, 150);
				return;
			}

			$.palerts._hide();
			$.palerts._overlay('show');
			
			$("#"+$.alerts.liveRegionId).append(
			  '<div id="popup_container">' +
			    '<h2 id="popup_title"></h2>' +
			    '<div id="popup_content">' +
			      '<div id="popup_message"></div>' +
				'</div>' +
			  '</div>');
			
			if( $.palerts.dialogClass ) $("#popup_container").addClass($.palerts.dialogClass);

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
			
			$.palerts._reposition();
			$.palerts._maintainPosition(true);
			
			switch( type ) {
				
				case 'progress':
					$("#popup_message").append('<br /><br /><div id="popup_bar"><img src="./cmn/img/progress_bar.gif"/></div>');
					$("#popup_bar").css({
						'text-align': 'center'
						//,'width': '100px'
						//,'height': '10px'
					});
					if( value != 0 ) {
					$.palerts.timeoutId=setTimeout(function(){
						//if ((ajaxrequest)) ajaxrequest.abort();
						if (typeof(ajaxrequest)!='undefined') ajaxrequest.abort(); /////////////////////////////////////////////////////////////////////////
						$("#popup_message").text('Operation timeout, please try again!');
						$("#popup_bar").remove();
						if ($("#popup_panel").length < 1){
							$("#popup_message").after('<div id="popup_panel"><input type="button" value="'+$.palerts.okButton+'" id="popup_ok" class="btn" /></div>');
						}
						$("#popup_ok").click( function() {
							$.palerts._hide();
							callback(true);
						});
						$("#popup_ok").focus().keypress( function(e) {
							if( e.keyCode == 13 || e.keyCode == 27 ) $("#popup_ok").trigger('click');
						});
					}, value*1000 );
					}
				break;
			}
			
			// Make draggable
			if( $.palerts.draggable ) {
				try {
					$("#popup_container").draggable({ handle: $("#popup_title") });
					$("#popup_title").css({ cursor: 'move' });
				} catch(e) { /* requires jQuery UI draggables */ }
			}
		},
		
		_hide: function() {
			clearTimeout($.palerts.timeoutId);
			/* restore the focus element */
			$.alerts._focusManager("restore");
			$("#popup_container").remove();
			$.palerts._overlay('hide');
			$.palerts._maintainPosition(false);
			$("#"+$.alerts.liveRegionId).css({width: "0px", height: "0px"});
		},
		
		_overlay: function(status) {
			switch( status ) {
				case 'show':
					$.palerts._overlay('hide');
					$("#"+$.alerts.liveRegionId).css({width: "100%"}).height($(document).height());
					$("#"+$.alerts.liveRegionId).append('<div id="popup_overlay"></div>');
					$("#popup_overlay").css({
						position: 'absolute',
						zIndex: 99998,
						top: '0px',
						left: '0px',
						width: '100%',
						height: $(document).height(),
						background: $.palerts.overlayColor,
						opacity: $.palerts.overlayOpacity
					});
				break;
				case 'hide':
					$("#popup_overlay").remove();
				break;
			}
		},
		
		_reposition: function() {
			var top = (($(window).height() / 2) - ($("#popup_container").outerHeight() / 2)) + $.palerts.verticalOffset;
			var left = (($(window).width() / 2) - ($("#popup_container").outerWidth() / 2)) + $.palerts.horizontalOffset;
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
			if( $.palerts.repositionOnResize ) {
				switch(status) {
					case true:
						$(window).bind('resize', $.palerts._reposition);
					break;
					case false:
						$(window).unbind('resize', $.palerts._reposition);
					break;
				}
			}
		}
		
	}
	
	// Shortuct functions
	
	jProgress = function(message, value, title, callback) {
		$.palerts.progress(message, value, title, callback);
	};
	
	jHide = function() {
		$.palerts._hide();
	};
})(jQuery);
