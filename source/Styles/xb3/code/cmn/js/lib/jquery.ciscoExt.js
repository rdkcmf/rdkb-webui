/**
 * jquery.ciscoExt.js
 *
 * All right reserved by Cisco Systems.
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
