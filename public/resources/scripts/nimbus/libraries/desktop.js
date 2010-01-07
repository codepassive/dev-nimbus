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
					$('.window.draggable').draggable({handle:'.window .titlebar .title', stack:{group:'window'}, start:function(){$('.window .content').css({visibility:'hidden'});}, drag:function(){$('.window .content').css({cursor:'move'});}, stop:function(){$('.window .content').css({cursor:'default',visibility:'visible'});}});
					Nimbus.Desktop.cache[options.handle] = {maximized:false};
					$('.window .titlebar').dblclick(function(){
						var win = {};
						if (Nimbus.Desktop.cache[options.handle].maximized == false) {
							win = {
								x: $(this).parents('.window').offset().left,
								y: $(this).parents('.window').offset().top,
								height: $(this).parents('.window').height(),
								width: $(this).parents('.window').width(),
								maximized: true
							};		
							$(this).parents('.window').height('100%'),
							$(this).parents('.window').width('100%'),				 
							Nimbus.Desktop.cache[options.handle] = win;
							$(this).parents('.window').addClass('maximized');
						} else {
							win = Nimbus.Desktop.cache[options.handle];
							Nimbus.Desktop.cache[options.handle].maximized = false;
							$(this).parents('.window').removeClass('maximized').css({top:win.y,left:win.x,height:win.height,width:win.width});
						}
						setTimeout(options.handle + ".redraw();", 0);
					});
					//Check if it should be put to the taskbar
					Nimbus.Application.addToTaskbar(options);
				}
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
			$('.desktop-icons').prepend('<div class="item" id="' + options.id + '"><div class="icon-inner"><a href="javascript:;" title="' + options.title + '"><img src="' + options.path + '" border="0" alt="" /></a><a href="javascript:;" title="' + options.title + '">' + options.name + '</a></div></div>');
			//Move if needed
			if (options.x) { $('#' + options.id).css({left: options.x + 'px'}); }
			if (options.y) { $('#' + options.id).css({top: options.y + 'px'}); }
			//Bind the events
			$('#' + options.id).dblclick(function(){
				Nimbus.Application.start(options.handle);
				if (callback != undefined) {
					callback();				
				}
			});
		},
		
		fixIcons: function(){
			var moveBy = [80, 110];
			var cur = [12, 54];
			$('.desktop-icons .item').each(function(i, e){
				$(this).css({left: cur[0] + 'px'});
				$(this).css({top: cur[1] + 'px'});
				cur[1] = cur[1] + moveBy[1];
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