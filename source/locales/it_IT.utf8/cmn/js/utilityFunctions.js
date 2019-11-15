/*
 * If not stated otherwise in this file or this component's Licenses.txt file the
 * following copyright and licenses apply:
 *
 * Copyright 2016 RDK Management
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

/**
 * Utility functions that could be reused across all pages.
 *
 * Creator:		Nobel Huang
 * Data:		Sep 17th, 2013
 */

/**
 * Given a IPv4 string, this converts it to binary representation.
 */


var timeOutId =null;

function ip4StrToBin(ip4Str) {
	var ipArr = ip4Str.match(/(\d{1,3}).(\d{1,3}).(\d{1,3}).(\d{1,3})/);
	var ipBin = 0;
	if (ipArr === null) return null;
	else if (ipArr.length != 5) return null;

	ipArr.shift();
	for (var i=0; i<ipArr.length; ++i) {
		ipBin = ipBin << 8;
		ipBin |= parseInt(ipArr[i]);
	}

	return ipBin;
}

/**
 * This is the reversed operation to ip4StrToBin().
 */
function ip4BinToStr(ip4Bin) {
	var ipStr = '';
	for (var i=3; i>=0; --i) {
		var ipComp = ((ip4Bin >> (i * 8)) & 0xFF);
		ipStr += ipComp.toString() + (i == 0 ? '' : '.');
	}
	return ipStr;
}

/**
 * This calculates the mask number from mask binary.
 */
function ip4MaskNum(maskBin) {
	var zeroNum = 0;
	for (var i=0; i<32; ++i) {
		var shiftBin = maskBin >> i;
		if (shiftBin & 0x1) {
			break;
		}
		else {
			zeroNum++;
		}
	}
	return (32 - zeroNum);
}

/**
 * This check if the binary input ipv4 is a valid ip in subnet.
 */
function isIp4ValidInSubnet(ip4Bin, subnetIpBin, subnetMaskBin) {
	if (ip4Bin === null || subnetIpBin === null || subnetMaskBin === null) {
		return false;
	}
	if ((ip4Bin & subnetMaskBin) != (subnetIpBin & subnetMaskBin)) {
		return false;
	}
	if ((ip4Bin & subnetMaskBin) == ip4Bin) {
		return false;
	}
	var bcAddr = (subnetIpBin & subnetMaskBin) + ((~subnetMaskBin) & 0xffffffff);
	if (ip4Bin == bcAddr) {
		return false;
	}
	return true;
}

function isValidIp6Str(v6Str) {
	var testRegexFull = /^([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}$/;
	var testRegexVeryShort = /^[0-9a-fA-F]{0,4}::[0-9a-fA-F]{0,4}$/;
	var testRegexFront = /^([0-9a-fA-F]{1,4}:){1,}/;
	var testRegexEnd = /(:[0-9a-fA-F]{1,4}){1,}$/;

	if (typeof(v6Str) !== "string") {
		return false;
	}
	else if (testRegexFull.test(v6Str) || testRegexVeryShort.test(v6Str)) {
		return true;
	}
	else {
		var frontMatchArr = v6Str.match(testRegexFront);
		var endMatchArr = v6Str.match(testRegexEnd);

		if (frontMatchArr === null || endMatchArr === null) {
			return false;
		}
		if ((frontMatchArr[0] + endMatchArr[0]) !== v6Str) {
			return false;
		}

		var frontCompArr = frontMatchArr[0].match(/[0-9a-fA-F]{1,4}:/g);
		var endCompArr = endMatchArr[0].match(/:[0-9a-fA-F]{1,4}/g);

		if (frontCompArr === null || endCompArr === null
			|| (frontCompArr.length + endCompArr.length > 7)) {
			return false;
		}
	}

	return true;
}


function jProgress(a,b){  
//to show the progress bar   
waitingDialog.show(a);
 
     timeOutId=setTimeout(function () {
     if (typeof(ajaxrequest)!='undefined') ajaxrequest.abort();
     $(".modal-header").text('Operazione in corso');
     $(".modal-body").text('Timeout operazione, si prega di riprovare!');
     $(".progress-bar").remove();
     $(".progress").remove();
     $(".progress-striped").remove();
     $(".modal-body").after('<div id="popup_box"><input type="button" value="ok" id="popup_ok" class="btn" /></div>');
     $("#popup_ok").click( function() {
       	
    	waitingDialog.hide();
	
     });

     $('.modal-header').attr('style', 'text-align: left !important; font-size: 1.1em; font-weight: bold');
     $('.modal-body').attr('style', 'text-align: left !important; margin: 6px !important; background-color:#ededed');
     $('#popup_box').attr('style', 'padding: 8px 0 0 !important; margin: -5px 8px 8px 425px !important'); 

     }, b*1000);


}
function jHide(){
	//to hide the progress bar
	clearTimeout(timeOutId);
	waitingDialog.hide();

}


function ValidIp4Addr(ip4Dec, subnetIpDec, subnetMaskDec){
	//to check if "Server IPv4 Address" is in "DHCP Pool range"
	ip4Bin			= ip4StrToBin(ip4Dec);
	subnetIpBin		= ip4StrToBin(subnetIpDec);
	subnetMaskBin	= ip4StrToBin(subnetMaskDec);

	isIp4ValidRange = isIp4ValidInSubnet(ip4Bin, subnetIpBin, subnetMaskBin);

	if(isIp4ValidRange){
		//Reserve last IP of subnet for ATOM process communication
		var bcAddr = (subnetIpBin & subnetMaskBin) + ((~subnetMaskBin) & 0xfffffffe);
		if (ip4Bin == bcAddr) {
			return false;
		}
		//for blocking GW IP
		else if(ip4Bin == subnetIpBin){
			return false;
		}
		return true;
	}
	else {
		return false;
	}

}

$.validator.addMethod("allowed_char", function(value, element, param) {
	//Invalid characters are Less than (<), Greater than (>), Ampersand (&), Double quote ("), Single quote ('), Pipe (|).
	return !param || (value.match(/[<>&"'|]/)==null);
}, 'Minore di (<), maggiore di (>), E commerciale (&), Virgolette doppie ("), Virgolette singole (\') e Barra dritta (|) sono caratteri non consentiti.');
// Minore di (<), maggiore di (>), E commerciale (&), Virgolette doppie ("), Virgolette singole (\') e Barra dritta (|) sono caratteri non consentiti.

$.validator.addMethod("noSpace", function(value, element, param) {
	//prevent users to use space in password
	var res = !(/\s/g.test(value));
	return res;
}, 'Nessun carattere di spazio è consentito.'); //Nessun carattere di spazio è consentito