// jQuery Folder Tree Plugin
//
// Version 1.00
//
// Giannis Koutsaftakis
// <htmlab> (http://www.htmlab.gr/)
// 28 September 2011
//
// Visit http://www.htmlab.gr/blog for more information
//
// Usage: $('#smallbrowser').folderTree(options)
//
// Options:  root           - root folder to display; default = /
//           script         - location of the serverside AJAX file to use; default = jquery.foldertree.php
//           loadMessage    - Message to display while initial tree loads (can be HTML)
//
// History:
//
// 1.00 - released (28 September 2011)
//
// TERMS OF USE
// 
// This plugin is dual-licensed under the GNU General Public License and the MIT License and
// is copyright 2008 A Beautiful Site, LLC. 
//

(function($) {
  $.fn.folderTree = function(o) {

	  if( !o ) var o = {};
	  if( o.root == undefined ) o.root = '/'; //e.g. /root/folder/
	  if( o.script == undefined ) o.script = 'jquery.foldertree.php';
	  if( o.loadMessage == undefined ) o.loadMessage = 'Loading...';
	
		return this.each(function() { //Builds the whole tree

				function create_node (dir, target, fol){
					var toadd = '<ul class="jqueryFolderTree"><li><a class="home folder sel" href="'+o.root+'">Home</a></li></ul>';

					if($(fol).hasClass("sel")){
						$(fol).removeClass('folder').addClass('waitb');
					}else{
						$(fol).removeClass('folder').addClass('wait');
					}

					$.post(o.script, { dir: dir }, function(data) {
						$(fol).removeClass('wait waitb').addClass('folder');
						if(dir == o.root){ //if is the root dir
							data = toadd+data;
							target.html(data);
							target.find("ul.jqueryFolderTree").show();
						}else{
							target.append(data);
							target.find("ul.jqueryFolderTree").css({'padding-left':'20px'}).show();
						}
					});
				}

				$(this).delegate(".collapsed", "click", function(e){
					$(this).removeClass("collapsed").addClass("expanded");
					var cur_li = $(this).closest("li");
					var ul_to = cur_li.find("ul.jqueryFolderTree").first();
					if(ul_to.length > 0){
						ul_to.show();
					}else{
						create_node($(this).attr('rel'), cur_li, $(this).next('li a.folder') );
					}
				});


				$(this).delegate(".expanded", "click", function(e){
					$(this).removeClass("expanded").addClass("collapsed");
					var cur_li = $(this).closest("li");
					var ul_to = cur_li.find("ul.jqueryFolderTree").first();
					ul_to.hide();
				});

				$(this).delegate(".folder", "click", function(e){
					$(".folder", $(this).attr("id")).removeClass("sel");
					$(this).addClass("sel");
					e.preventDefault();
				});

				$(this).html('<ul class="jqueryFolderTree"><li class="wait">' + o.loadMessage + '</li></ul>');
				create_node(o.root, $(this));
		});

  }
})(jQuery);