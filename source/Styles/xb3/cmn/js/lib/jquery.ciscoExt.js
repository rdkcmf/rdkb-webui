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
 * jquery.ciscoExt.js
 *
 * jQuery extension by Cisco. This extension introduces extened jQuery functions
 * to match Cisco Web needs.
 *
 * Author: Nobel Huang (xiaoyhua)
 * Date: Nov 4, 2013
 */
(function($){
	/**
	 * Wrapping API for set/get value for select (combo box) as the need of
	 * accessibility.
	 */
	$.fn.comboVal = function (tVal) {
		if (this.length === 0) {
			/* nothing selected */
			return undefined;
		}
		if (tVal === undefined) {
			/* only get the first element value */
			return $(this[0]).val();
		}
		/* set value for each element */
		this.each(function(){
			var $this = $(this);
			$this.children("option").each(function(){
				var that = $(this);
				if (this.value == tVal) {
					if (that.prop) that.prop("selected", true);
					else that.attr("selected", true);
					return false;
				}
			});
			$this.val(tVal);
		});
	};
}(jQuery));
