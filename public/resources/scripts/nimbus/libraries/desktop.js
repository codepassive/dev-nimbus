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
	
		refreshRate: 5,
		
		shortcuts: 1,
	
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
					var vid = view_id.replace("container-", "");
					$('.active_instance .table tbody').prepend('<tr><td>' +  vid.replace("view_", "") + '</td><td style="text-align:center"><input type="button" value="Close" title="' + view_id.replace("view_", "") + '"/></td></tr>');		
					//Events
					view_id = view_id.replace("view_", "");
					Nimbus.Desktop.cache[view_id] = {maximized:false};
					$('#' + view_id).click(function(){
						var id = $('#' + view_id).attr('id');
						$('#nimbusbar-taskbar .items .item').removeClass('active');
						$('#taskbarinstance-' + view_id).addClass('active');
						$('.window').removeClass('active');
						$(this).addClass('active');
						var zindex = 530;
						$('.window').each(function(){
							if ($(this).css('zIndex') >= zindex) {
								zindex = $(this).css('zIndex') + 1;
							}
						});
						$(this).css({zIndex: zindex});
					});
					$('#' + view_id + '.draggable').draggable({opacity: 0.7, handle:'.titlebar', stack:{group:'.draggable', min: 550}, start:function(){$(this).find('.content').css({visibility:'hidden'});$(this).find('.titlebar .title').css({cursor:'move'});}, stop:function(){$(this).find('.content').css({visibility:'visible'});$(this).find('.titlebar .title').css({cursor:'default'});}});
					$('#' + view_id + ' .action-minimizable').click(function(){Nimbus.Desktop.window.minimize(view_id);});
					$('#' + view_id + ' .action-toggable').click(function(){Nimbus.Desktop.window.toggable(view_id);});
					$('#' + view_id + '.toggable .titlebar').dblclick(function(){Nimbus.Desktop.window.toggable(view_id);});
					$('#' + view_id + ' .action-closable').click(function(){Nimbus.Desktop.window.close(view_id, options.handle);});
					$('#' + view_id + '.resizable').Resizable({
							minWidth: 400, minHeight: 200, minTop: 32, minLeft: 0,
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
								$(this).find('.content').css({visibility:'hidden'});
								$(this).css({opacity:0.7});
								//Fix the Content Height
								var height = $(this).find('.toolbars-top').height() + $(this).find('.toolbars-bottom').height() + $(this).find('.buttons').height();
								$(this).find('.content').height($(this).height() - (32 + height));
								//Fix the FILL-AREA height
								height = $(this).find('.content').height();
								var width = $(this).find('.inner').width();
								if ($(this).find('.fill-all').length) {
									$(this).find('.fill-all').height((height - 2));
									$(this).find('.fill-all').width((width));
								}
								if ($(this).find('.buttons').length) {
									$(this).find('.content').height(($(this).find('.content').height() + 7) - $(this).find('.buttons').height());
								}
							},
							onStop: function(){
								$(this).find('.content').css({visibility:'visible'});
								$(this).css({opacity:1});
								//Fix the Content Height
								var height = $(this).find('.toolbars-top').height() + $(this).find('.toolbars-bottom').height() + $(this).find('.buttons').height();
								$(this).find('.content').height($(this).height() - (32 + height));
								//Fix the FILL-AREA height
								height = $(this).find('.content').height();
								var width = $(this).find('.inner').width();
								if ($(this).find('.fill-all').length) {
									$(this).find('.fill-all').height((height - 2));
									$(this).find('.fill-all').width((width));
								}
								if ($(this).find('.buttons').length) {
									$(this).find('.content').height(($(this).find('.content').height() + 7) - $(this).find('.buttons').height());
								}
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
					$(document).click(function(e){if ($(e.target).parents('.toolbar').length == 0) {$('.toolbar .child').hide();}});
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
				Nimbus.Application.close(id, options);
			},
			minimize: function(wind){
				$('#nimbusbar-taskbar .items .item').removeClass('active');
				Nimbus.Desktop.window.hide(wind);
			},
			redraw: function(wind, callback){
				//Fix the wind height
				if ($('#' + wind).hasClass('.maximized')) {
					var height = $('#' + wind + ' .toolbars-top').height() + $('#' + wind + ' .toolbars-bottom').height() + $('#' + wind + ' .buttons').height();
					$('#' + wind + ' .content').height($(document).height() - ($('#nimbusbar').height() + (height + 30)));
				}
				//Fix the Content Height
				var height = $('#' + wind + ' .toolbars-top').height() + $('#' + wind + ' .toolbars-bottom').height() + $('#' + wind + ' .buttons').height();
				$('#' + wind + ' .content').height(($('#' + wind + ' .content').height() - ($('#' + wind).height() - height)));
				//Fix the FILL-AREA height
				var height = $('#' + wind + ' .content').height();
				var width = $('#' + wind + ' .inner').width();
				if ($('#' + wind).find('.fill-all').length) {
					$('#' + wind + ' .fill-all').height((height - 2));
					$('#' + wind + ' .fill-all').width((width));
				}
				//Fix width? If not 100%
				if ($('#' + wind + ' .buttons').length) {
					$('#' + wind + ' .content').height(($('#' + wind + ' .content').height() + 8) - $('#' + wind + ' .buttons').height());
				}
				if (callback) {
					callback($('#' + wind));
				}
			},
			toggable: function(id){
				var win = {};
				if (Nimbus.Desktop.cache[id].maximized == false) {
					win = {
						x: $('#' + id).offset().left,
						y: $('#' + id).offset().top,
						height: $('#' + id + ' .inner').height(),
						wheight: $('#' + id).height(),
						width: $('#' + id).width(),
						maximized: true
					};
					var height = $('#' + id + ' .toolbars-top').height() + $('#' + id + ' .toolbars-bottom').height() + $('#' + id + ' .buttons').height();
					$('#' + id).height('100%');
					$('#' + id).width('100%'),
					Nimbus.Desktop.cache[id] = win;
					$('#' + id).addClass('maximized').removeClass('draggable');
					$('#' + id + ' .resizable_wrapper').hide();
					$('#' + id + ' .content').height($(document).height() - ($('#nimbusbar').height() + height)),
					Nimbus.Desktop.window.redraw(id);
				} else {
					win = Nimbus.Desktop.cache[id];
					Nimbus.Desktop.cache[id].maximized = false;
					$('#' + id).removeClass('maximized').addClass('draggable resizable').css({top:win.y,left:win.x,width:win.width});
					$('#' + id + ' .resizable_wrapper').show();
					$('#' + id).height(win.wheight);
					//Fix the Content Height
					var height = $('#' + id).find('.toolbars-top').height() + $('#' + id).find('.toolbars-bottom').height() + $('#' + id).find('.buttons').height();
					$('#' + id).find('.content').height(win.height - height);
					//Fix the FILL-AREA height
					height = $('#' + id).find('.content').height();
					var width = $('#' + id).find('.inner').width();
					if ($('#' + id).find('.fill-all').length) {
						$('#' + id).find('.fill-all').height((height - 3));
						$('#' + id + ' .fill-all').width((width));
					}
					if ($('#' + id + ' .buttons').length) {
						$('#' + id + ' .content').height(($('#' + id + ' .content').height() + 7) - $('#' + id + ' .buttons').height());
					}
				}
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
				$('#' + id).toggle(600, function(){
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
		
		currentBackgroundInt: 0,

		/**
		 * Change the Background of the current visible background screen
		 */
		background: function(background, interval){
			if (background.constructor == Array) {
				Nimbus.Desktop.backgrounds = background;
				Nimbus.Desktop._background(Nimbus.Desktop.backgrounds[Nimbus.Desktop.currentBackground]);
				Nimbus.Desktop.currentBackgroundInt = setInterval("Nimbus.Desktop._background()", (interval * 1000));
			} else {
				clearTimeout(Nimbus.Desktop.currentBackgroundInt);
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