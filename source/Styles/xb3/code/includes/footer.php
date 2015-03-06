<!-- $Id: footer.php 2976 2009-09-02 21:42:51Z cporto $ -->
		</div> <!-- end #main-content-->
		
		<!--Footer-->
		<div id="footer">
			<ul id="footer-links">
				<li class="first-child"><a href="http://www.xfinity.com" target="_blank">Xfinity.com</a></li>
				<li style="list-style:none outside none; margin-left:10px">&#8226;&nbsp;&nbsp;<a href="https://customer.comcast.com/" target="_blank">customerCentral</a></li>
				<li style="list-style:none outside none; margin-left:10px">&#8226;&nbsp;&nbsp;<a href="http://customer.comcast.com/userguides" target="_blank">User Guide</a></li>
			</ul>
		</div> <!-- end #footer -->
	</div> <!-- end #container -->
<script type="text/javascript">
$(document).ready(function() {
	// focus current page link, must after page.init()
	//$('#nav [href="'+location.href.replace(/^.*\//g, '')+'"]').focus();		// need a "skip nav" function
	$("#skip-link").click(function () {
        $('#content').attr('tabIndex', -1).focus();  //this is to fix skip-link doesn't work on webkit-based Chrome
    });

	// change radio-btn status and do ajax when press "enter"
	//$(".radio-btns a").keydown(function(event){
	$(".radio-btns a").keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(13 == keycode){
			if (!$(this).parent(".radio-btns").find("li").hasClass("selected")){
				return;		// do nothing if has disabled class, don't detect disabled attr for radio-btn
			}
			// console.log($(this).find(":radio").hasClass("disabled"));
			$(this).find(":radio").trigger('click');
			$(this).find(":radio").trigger('change');
			$(this).parent(".radio-btns").radioToButton();
		}
	});
	
	// press Esc to skip menu and goto first control of content
	// Esc:keypress:which is zero in FF, Esc:keypress is not work in Chrome
	$("#nav").keydown(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(27 == keycode){
			$("#content textarea:eq(0)").focus();
			$("#content input:eq(0)").focus();
			$("#content a:eq(0)").focus();			// high priority element to focus			
		}
		// alert(event.keyCode+"---"+event.which+"---"+event.charCode);		
	});
	
	/* changes for high contrast mode */
	$.highContrastDetect({useExtraCss: true, debugInNormalMode: false});
	if ($.__isHighContrast) {
		/* change plus/minus tree indicator of nav menu */
		$("#nav a.top-level").prepend('<span class="hi_nav_top_indi">[+]</span>');
		$("#nav a.folder").prepend('<span class="hi_nav_folder_indi">[+]</span>');
		$("#nav a.top-level-active span.hi_nav_top_indi").text("[-]");
		$("#nav a.folder").click(function() {
			/* this should be called after nav state changed */
			var $link = $(this);
			if ($link.hasClass("folder-open")) {
				$link.children("span.hi_nav_folder_indi").text("[-]");
			}
			else {
				$link.children("span.hi_nav_folder_indi").text("[+]");
			}
		});
	}

	/*
	*	these 3 sections for radio-btn accessibility, as a workaround, maybe should put at the front of .ready().
	*/
	// add "role" and "title" for ARIA, attr may need to be embedded into html
	$(".radio-btns a").each(function(){
		$(this).attr("role", "radio").attr("title", $(this).closest("ul").prev().text() + $(this).find("label").text());
	});
	
	// monitor "aria-checked" status for JAWS, NOTE: better depends on input element
	$(".radio-btns").change(function(){
		$(this).find("a").each(function(){
			$(this).attr("aria-checked", $(this).find("input").attr("checked") ? "true" : "false");
		});
	});
	
	//give the initial status, do not trigger change above
	$(".radio-btns").find("a").each(function(){
		$(this).attr("aria-checked", $(this).find("input").attr("checked") ? "true" : "false");
	});

});
</script>	
</body>
</html>
