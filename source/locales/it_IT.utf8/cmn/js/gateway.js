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

/* $Id: global.js 3167 2010-03-03 18:11:27Z slemoine $ */

/*
 *	Declare the global object for namespacing.
 */


var gateway = window.comcast || {};

gateway.page = function() {
    function setupLeftNavigation(selectedNavElement) {
        if(typeof selectedNavElement == "string") {
            $("#nav li." + selectedNavElement + " a").addClass("selected");
        }
    
        // Show all UL that contain the current page
        $("#nav ul:has(.selected)").show();
        
        // Folder arrows
        $("#nav li li:has(ul) > a").addClass("folder");
        
        $("#nav li li:has(.selected) > a").addClass("folder-open");    
        
        // Top Level Navigation 
        $("#nav li:has(.selected) > a.top-level").addClass("top-level-active");        
    	
    	// For Development Only: Show broken links in navigation as gray
    	// $("#nav a[href='#']").css("color","#ccc");
    	
    	$("#nav a.top-level").click(function() {
            var $topNav = $("#nav a.top-level-active");
            var $newNav = $(this);
            var $newNavList = $newNav.next();
            
            if(!$newNav.hasClass("top-level-active")) {
                $("#nav a.top-level-active").removeClass("top-level-active").next();
                $(this).addClass("top-level-active");
            
        	    	$topNav.next();    
                $newNav.next();
            }
    	});
    	
    	$("#nav a.folder").click(function() {
            var $link = $(this);
            var $list = $link.next();
    	    
    	    if($link.is(".folder-open")) {
                $link.removeClass("folder-open");
                $list.slideUp();    
    	    } else {
                $link.addClass("folder-open");
                $list.slideDown();
    	    }
        });

		//Fire Fox display inline fixes


			//Fire Fox 3.0 display inline fixes
			if ($.browser.mozilla) {
					var $version = $.browser.version.split('.')
					if ($version[0] && parseInt($version[0], 10) <= 1){
						if ($version[1] && parseInt($version[1], 10) <= 9){
							if ($version[2] && parseInt($version[2], 10) <= 0){
								if ($version[3] && parseInt($version[3], 10) <= 11 || parseInt($version[3], 10) <= 14 ){
	
									//fixes block content positioning such as image dissappearing
									$('.block').addClass("ff2");
									//fixes odd width bug after applying moz-inline-stack
									$(".block").wrapInner($("<div class=\"ff2fix\"></div>"));
	
								};
							};
						};
					};
	
			//Fire Fox 2 display inline fixes
					if ($version[0] && parseInt($version[0], 10) <= 1){
						if ($version[1] && parseInt($version[1], 10) <= 8){
							if ($version[2] && parseInt($version[2], 10) <= 1){
								if ($version[3] && parseInt($version[3], 10) <= 15){
	
									//fixes block content positioning such as image dissappearing
									$('.block').addClass("ff2");
									//fixes odd width bug after applying moz-inline-stack
									$(".block").wrapInner($("<div class=\"ff2fix\"></div>"));
	
								};
							};
						};
					};

			};

    }

	function setupBatteryIndicator(){
        /*
         * Battery indicator in the status bar
         */
		//get percentage
		var battery = $("li.battery").text().match(/\d+/);
		var $icon = $("li.battery span");
		//assign class based off of battery percentage

		if(battery > 90){
			$icon.removeClass().addClass("bat-100");
		}

		else if(battery > 60 ){
			$icon.removeClass().addClass("bat-75");
		}

		else if (battery > 39){
			$icon.removeClass().addClass("bat-50");
		}

		else if(battery > 18) {
			$icon.removeClass().addClass("bat-25");
		}

		else if(battery > 8) {
			$icon.removeClass().addClass("bat-10");
		}

		else {
			$icon.removeClass().addClass("bat-0");
		};
	}
	
   /* function getCookie(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length == 2)
            return parts.pop().split(";").shift();
    }*/ 
    
    function setupEducationalTip() {
        if($("#educational-tip:has(.hidden)").length > 0) {
           var closed = true;
           var $link = $("<a href=\"javascript:;\" class=\"tip-more\">altro</a>").click(function() {
               if(closed) {
        	       $("#educational-tip .hidden").fadeIn();
        	       closed = false;
        	       $(this).html("meno");
        	   } else {
        	       $("#educational-tip .hidden").fadeOut();
        	       closed = true;
        	       $(this).html("altro");
        	   
        	   }
           }).appendTo("#educational-tip");
        }
    }

    function setupFirewallDisplay() {

				var $link = $("#security-level label");
				var $div = $("#security-level .hide");
				
				// hide all of the elements
				$($div).hide();
				
				// toggle slide
				$($link).click(function(e){
				    //e.preventDefault();
					$(this).siblings('.hide').slideToggle();
				});
    }

    function setupDeleteConfirmDialogs() {
        /*
         * Confirm dialog for delete action
         */
             
        $("a.confirm").click(function(e) {
            e.preventDefault();
            
            var href = $(this).attr("href");
            var message = ($(this).attr("title").length > 0) ? "Sei sicuro di " + $(this).attr("title") + "?" : "Sei sicuro?";
            
            jConfirm(
                message
                ,"Sei sicuro?"
                ,function(ret) {
                    if(ret) {
                        window.location = href;
                    }    
                });
        });
    }
    
    function setupFormValidation() {
    	$.validator.setDefaults({
    		errorElement : "p"
    		,errorPlacement: function(error, element) {
                error.appendTo(element.closest(".form-row"));
            }
    	});
/*!
 * jQuery Validation Plugin 1.11.1
 *
 * http://bassistance.de/jquery-plugins/jquery-plugin-validation/
 * http://docs.jquery.com/Plugins/Validation
 *
 * Copyright 2013 JÃ¶rn Zaefferer
 * Released under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
*/
        jQuery.extend(jQuery.validator.messages, {
        	required: "Campo obbligatorio.",
        	remote: "Correggi questo campo.",
        	email: "Inserisci un indirizzo e-mail valido.",
        	url: "Inserisci un URL valido.",
        	date: "Inserisci una data valida.",
        	dateISO: "Inserisci una data valida(ISO).",
        	number: "Inserisci un numero valido.",
        	digits: "Inserisci solo cifre",
        	creditcard: "Inserisci un numero di carta di credito valido.",
        	equalTo: "Inserisci di nuovo lo stesso valore.",
        	accept: "Inserisci un valore con un\’estensione valida.",
        	maxlength: $.validator.format("Inserisci un massimo di {0} caratteri."),
        	minlength: $.validator.format("Inserisci almeno {0} caratteri."),
        	rangelength: $.validator.format("Inserisci un valore di lunghezza compresa tra {0} e {1} caratteri."),
        	range: $.validator.format("Inserisci un valore compreso tra {0} e {1}."),
        	max: $.validator.format("Inserisci un valore inferiore o uguale a {0}."),
        	min: $.validator.format("Inserisci un valore superiore o uguale a {0}."),
        	ipv4: "Inserisci un indirizzo IPv4 nel formato #.#.#.#",
            newpwd:'La password deve contenere almeno una cifra, una minuscola, una maiuscola e un minimo di 10 caratteri'
        });
    
    	$.validator.addMethod("alphanumeric", function(value, element) {
    		return this.optional(element) || /^[a-zA-Z0-9]+$/i.test(value);
    	}, "Sono validi solo numeri e lettere. No spazi né caratteri speciali.");
    	
    	$.validator.addMethod("exactlengths", function(value, element, param) {
    		return this.optional(element) || !jQuery.inArray( value.length, param );
    	}, "Inserisci esattamente {0} caratteri.");
    	
    	$.validator.addMethod("hexadecimal", function(value, element) {
    		return this.optional(element) || /^[a-fA-F0-9]+$/i.test(value);
    	}, "Sono validi solo i caratteri esadecimali. I caratteri accettabili sono ABCDEF0123456789.");
		
		$.validator.addMethod("exactlength", function(value, element, param) {
			return this.optional(element) || value.length == param;
		}, jQuery.format("Inserisci esattamente {0} caratteri."));
    	
    	$.validator.addMethod("ipv4", function(value, element) {
    		return this.optional(element) || /^0*([1-9]?\d|1\d\d|2[0-4]\d|25[0-5])\.0*([1-9]?\d|1\d\d|2[0-4]\d|25[0-5])\.0*([1-9]?\d|1\d\d|2[0-4]\d|25[0-5])\.0*([1-9]?\d|1\d\d|2[0-4]\d|25[0-5])$/i.test(value);
    	}, "Inserisci un indirizzo IPv4 nel formato #.#.#.#");

    	$.validator.addMethod("newpwd", function(value, element) {
    	    return this.optional(element) || /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{10,}$/.test(value);
    	}, "La password deve contenere almeno una cifra, una minuscola, una maiuscola e un minimo di 10 caratteri");
    	
        jQuery.validator.addMethod('ip', function(val, el) {
            function ip_valid(value) {
                return (value.match(/^\d+$/g) && value >= 0 && value <= 255);
            }
            
 /*            jQuery.validator.addMethod('ipt4', function(val, el) {
	                function ip_valid(value) {
	                    return (value.match(/^\d+$/g) && value > 0 && value <255);
            }*/
            
            var inputs = $(el).closest('.form-row').find('input');
            var isValid = true;
            
            inputs.each(function(index, element) {
                isValid &= ip_valid($(element).val());
            });
            
            return isValid;
        },"Inserisci un indirizzo IP valido.");
    
    	$.validator.addMethod("ipv6", function(value, element) {
    		return this.optional(element) || /^\s*((([0-9A-Fa-f]{1,4}:){7}(([0-9A-Fa-f]{1,4})|:))|(([0-9A-Fa-f]{1,4}:){6}(:|((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})|(:[0-9A-Fa-f]{1,4})))|(([0-9A-Fa-f]{1,4}:){5}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){4}(:[0-9A-Fa-f]{1,4}){0,1}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){3}(:[0-9A-Fa-f]{1,4}){0,2}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){2}(:[0-9A-Fa-f]{1,4}){0,3}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:)(:[0-9A-Fa-f]{1,4}){0,4}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(:(:[0-9A-Fa-f]{1,4}){0,5}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})))(%.+)?\s*$/i.test(value);
    	}, "Inserisci un indirizzo IPv6 nel formato");
    	
    	$.validator.addMethod("mac", function(value, element) {
    		return this.optional(element) || /^[0-9A-Fa-f][0-9A-Fa-f]:[0-9A-Fa-f][0-9A-Fa-f]:[0-9A-Fa-f][0-9A-Fa-f]:[0-9A-Fa-f][0-9A-Fa-f]:[0-9A-Fa-f][0-9A-Fa-f]:[0-9A-Fa-f][0-9A-Fa-f]$/i.test(value);
    	}, "Inserisci un indirizzo MAC nel formato xx:xx:xx:xx:xx:xx");
    
    	$.validator.addClassRules({
    	    octet: {
				range: [0,255]
    	    },
    	    ipv4: {
				ipv4: true
    	    },
    	    ipv6: {
				ipv6: true
    	    },
    	    hexadecimal: {
    	    	hexadecimal: true
    	    },
    	    exactlength: {
    	    	exactlength: true
    	    }
    	});
    }
 
 	function setupTooltipInHeader() {
 		$("#status li").mouseenter(function() {
			$(".tooltip", this).fadeIn();
 		}).mouseleave(function() {
			$(".tooltip", this).fadeOut();
 		});
 	}
    
    return {
        init: function(title, navElementId) {

            document.title = title + " - " + document.title;
            setupLeftNavigation(navElementId);
            setupDeleteConfirmDialogs();
			//setupBatteryIndicator();
			setupEducationalTip();
			setupFormValidation();
			setupFirewallDisplay();
			setupTooltipInHeader();
			
			// IE6 flickering fix
            try { document.execCommand('BackgroundImageCache', false, true); } catch(e) {};

			// IE6/7 fix for change event firing on radio and checkboxes            
		    if ($.browser.msie) {
		        $('input:radio, input:checkbox').click(function() {
					try {
						this.blur();
						this.focus();
					}
					catch (e) {}
		        });
		    }

        }
    }
}();

gateway.breakWord = function(originalString, characterLimit) {
	var originalString = ""+originalString; 						// Cast variable as string
	var characterLimit = parseInt(characterLimit); 					// Cast variable to integer
	
	if(originalString.length <= 0  || characterLimit <= 0) return; 	// Exit if string or character limit are out of bounds
	
	var re = new RegExp("(\\w{" + characterLimit + "})","g")
	
	// Insert spaces inside a long string at characterLimit intervals
	return originalString.replace(re, '$1 ');
}

/*
 * Turn radio input fields to Buttons
 */

$.fn.radioToButton = function(settings) {
	var config = {
		autoSubmitForm: false
	}

	if (settings) $.extend(config, settings);

    this.each(function() {
        var $c = $container = $(this);
		var $boxes = $c.find("li");
		
        $c.addClass("radiolist");
        
        $("li", $c).removeClass("selected");
        $("input:radio:checked", $c).parent().addClass("selected");
        
        $("label", $c).click(function(e) {
            e.preventDefault();
            
            var $parent;
            var $radio;
            
            // Clear selected box
            $boxes.removeClass("selected");

			$parent = $(this).parent().addClass("selected");
			
			// Show button/radio as checked
			$("input:radio", $c).prop("checked",false);
			
			$radio = $parent.find("input:radio").prop("checked",true);
			
			$c.trigger("change", [$radio.val()]);
			
			if(config.autoSubmitForm) $c.closest('form').submit();
        });    
    });
    
    return this;
};
