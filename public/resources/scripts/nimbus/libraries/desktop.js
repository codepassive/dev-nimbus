/** 
 * Nimbus - Manage, Share & Collaborate
 *
 * Nimbus is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * see LICENSE for more Copyright goodness.
 *
 * @package:		Nimbus
 * @copyright:	2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */
(function(){
	/**
	 * Create the Desktop Object
	 */
	Desktop = Nimbus.Desktop = {
		/**
		 * Current Workspace the user is on
		 */
		currentWorkspace: 1,
		
		cache: [],

		/**
		 * Add an element to the current workspace
		 */
		load: function(content, view_id, options){
			$('#screen-workspace-' + Nimbus.Desktop.currentWorkspace).append(content);
			if (view_id) {
				$('#' + view_id).hide().fadeIn(500);
				if (options) {					
					//Events
					view_id = view_id.replace("view_", "");
					Nimbus.Desktop.cache[view_id] = {maximized:false};
					$('#' + view_id).click(function(){var id = $('#' + view_id).attr('id');$('#nimbusbar-taskbar .items .item').removeClass('active');$('#taskbarinstance-' + view_id).addClass('active');$(this).css({zIndex:($('.window').css('zIndex') + 50)});});
					$('#' + view_id + '.draggable').draggable({opacity: 0.7, handle:'.titlebar', stack:{group:'.draggable', min: 550}, start:function(){$(this).find('.content').css({visibility:'hidden'});$(this).find('.titlebar .title').css({cursor:'move'});}, stop:function(){$(this).find('.content').css({visibility:'visible'});$(this).find('.titlebar .title').css({cursor:'default'});}});
					$('#' + view_id + ' .action-minimizable').click(function(){Nimbus.Desktop.window.minimize(view_id);});
					$('#' + view_id + ' .action-toggable').click(function(){Nimbus.Desktop.window.toggable(view_id);});
					$('#' + view_id + '.toggable .titlebar').dblclick(function(){Nimbus.Desktop.window.toggable(view_id);});
					$('#' + view_id + ' .action-closable').click(function(){Nimbus.Desktop.window.close(view_id, options.handle);});
					$('#' + view_id + '.resizable').Resizable({
							minWidth: 300,
							minHeight: 200,
							minTop: 32,
							minLeft: 0,
							dragHandle: true,
							handlers: {
								se: '#' + view_id + '.resizable .resizeSE',
								e: '#' + view_id + '.resizable .resizeE',
								ne: '#' + view_id + '.resizable .resizeNE',
								n: '#' + view_id + '.resizable .resizeN',
								nw: '#' + view_id + '.resizable .resizeNW',
								w: '#' + view_id + '.resizable .resizeW',
								sw: '#' + view_id + '.resizable .resizeSW',
								s: '#' + view_id + '.resizable .resizeS'
							},
							onResize : function(size, position) {
								Nimbus.Desktop.window.redraw(view_id); //Fix Resize on resize
							}
						}
					);

					//Toolbars
					$('#' + view_id + ' .toolbar a.parent').each(function(){
						$(this).toggle(function(){
							$('.toolbar .child').hide();
							$(this).next().show();
							$('.toolbar a.parent').not('.parent-text').mouseover(function(){
								var elem = this;
								$('.toolbar .child').hide();
								$(this).next().show();
								$('body').click(function(e){if ($(e.target).parents('.toolbar').length == 0) {
									$(elem).unbind('mouseover');
								}});
							});
						}, function(){
							$('.toolbar .child').hide();
						});
						$(this).find('a').each(function(){
							$(this).hover(function(){
								$(this).('.child').hide();
								alert($(this).('.child').html());
								$(this).('.child').next().show();
							});
						});
					});
					$('body').click(function(e){if ($(e.target).parents('.toolbar').length == 0) {$('.toolbar .child').hide();}});
					//Check if it should be put to the taskbar
					Nimbus.Application.addToTaskbar(options);
				}
			}
		},
		
		window: {
			title: function(id, title) {
				$('#taskbarinstance-' + id + ' .instance-name').text(title);
				$('#' + id + ' .title').text(title);
			},
			close: function(id, options){
				Nimbus.Application.removeFromTaskbar(id);
				Nimbus.Application.close(id, options);
			},
			minimize: function(window){
				$('#nimbusbar-taskbar .items .item').removeClass('active');
				Nimbus.Desktop.window.hide(window);
			},
			redraw: function(window){
				//Fix the window height
				if ($('#' + window).hasClass('.maximized')) {
					$('#' + window + ' .content').height($(document).height() - ($('#nimbusbar').height() + 56));
				}
				//Fix the Content Height
				var height = $('#' + window + ' .toolbars-top').height() + $('#' + window + ' .toolbars-bottom').height() + $('#' + window + ' .buttons').height();
				$('#' + window + ' .content').height(($('#' + window + ' .content').height() - ($('#' + window).height() - height)));
				//Fix the FILL-AREA height
				var height = $('#' + window + ' .content').height();
				var width = $('#' + window + ' .inner').width();
				$('#' + window + ' .fill-all').height((height - 2)).width((width - 2));
				//Fix width? If not 100%
			},
			toggable: function(id){
				var win = {};
				if (Nimbus.Desktop.cache[id].maximized == false) {
					win = {
						x: $('#' + id).offset().left,
						y: $('#' + id).offset().top,
						height: $('#' + id + ' .content').height(),
						width: $('#' + id).width(),
						maximized: true
					};
					$('#' + id + ' .content').height($(document).height() - ($('#nimbusbar').height() + 56)),
					$('#' + id).width('100%'),
					Nimbus.Desktop.cache[id] = win;
					$('#' + id).addClass('maximized').removeClass('draggable');
				} else {
					win = Nimbus.Desktop.cache[id];
					Nimbus.Desktop.cache[id].maximized = false;
					$('#' + id).removeClass('maximized').addClass('draggable').css({top:win.y,left:win.x,width:win.width});
					$('#' + id + ' .content').height(win.height);
				}
				Nimbus.Desktop.window.redraw(id);
			},
			show: function(id){
				$('#' + id).show(400, function(){
					$(this).css({zIndex:($('.window').css('zIndex') + 50)});
				});
			},
			hide: function(id){
				$('#' + id).hide(400, function(){
					$(this).hide(0);
				});
			},
			toggle: function(id){
				$('#' + id).toggle(400, function(){
					$(this).css({zIndex:($('.window').css('zIndex') + 50)});
				});
			},
		},
		
		notify: function(options){
			options.message;
			if (options.click) {
				options.click();
			}			
		},
		
		unload: function(view_id){
			$('#' + view_id).fadeOut(500);
			setTimeout("$('#" + view_id + "').remove();", 500);
		},
		
		addIcon: function(options, callback){
			options.id = (options.id) ? 'icon-' + options.id: 'icon-' + Math.random();
			options.name = (options.name) ? options.name: '';
			options.path = (options.path) ? options.path: '';
			$('.desktop-icons').prepend('<div class="icon"><div class="item" id="' + options.id + '"><div class="icon-inner"><a href="javascript:;" title="' + options.title + '"><img src="' + options.path + '" border="0" alt="" /></a><a href="javascript:;" title="' + options.title + '">' + options.name + '</a></div></div></div>');
			//Bind the events
			$('.desktop-icons .item').draggable({delay:200,stack:{group:'.desktop-icons .item', min: 520}}).click(function(){
				$('.desktop-icons .item').removeClass('active');
				//$(this).addClass('selected');
			});
			//$('.desktop-icons .item a').click(function(){$(this).addClass('active');});
			$('#' + options.id).dblclick(function(){
				Nimbus.Application.load(options.handle);
				if (callback != undefined) {
					callback();				
				}
			});
			
		},

		/**
		 * Backgrounds to be used in the background cycles
		 */
		background: [],

		/**
		 * The Current Background Index
		 */
		currentBackground: 0,

		/**
		 * The Current Background Screen visible
		 */
		currentBackgroundVisible: 1,

		/**
		 * Change the Background of the current visible background screen
		 */
		background: function(background, interval){
			if (background.constructor == Array) {
				Nimbus.Desktop.backgrounds = background;
				Nimbus.Desktop._background(Nimbus.Desktop.backgrounds[Nimbus.Desktop.currentBackground]);
				setInterval("Nimbus.Desktop._background()", (interval * 1000));
			} else {
				$('.screen-background').fadeOut(1000);
				$('#screen-background-' + Nimbus.Desktop.currentBackgroundVisible).css({background:"url('" + background + "') no-repeat center scroll"}).fadeIn(1000);
			}
		},

		/**
		 * Private method to be used by the Public background method to cycle through the backgrounds in the backgrounds store
		 */
		_background: function(){
			var visible = (Nimbus.Desktop.currentBackgroundVisible == 2) ? 1: 2;
			$('#screen-background-' + visible).fadeOut(1000);
			$('#screen-background-' + Nimbus.Desktop.currentBackgroundVisible).css({background:"url('" + Nimbus.Desktop.backgrounds[Nimbus.Desktop.currentBackground] + "') no-repeat center scroll"}).fadeIn(1000, function(){
				if (Nimbus.Desktop.currentBackground < (Nimbus.Desktop.backgrounds.length - 1)) {
					Nimbus.Desktop.currentBackground++;
				} else {
					Nimbus.Desktop.currentBackground = 0;
				}
				Nimbus.Desktop.currentBackgroundVisible = (Nimbus.Desktop.currentBackgroundVisible == 2) ? 1: 2;
			});
		}
	}
})();