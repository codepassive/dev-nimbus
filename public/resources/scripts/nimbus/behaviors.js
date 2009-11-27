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
 * @subpackage:		Nimbus_styles
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:			GNU/GPLv3, see LICENSE
 * @version:			1.0.0 Alpha
 */
/**
 * Window Behaviors
 */
(function($) {
	$(function() {
		//Resizable
		$(document).resize(function(){nimbus.screen.resize('all');});

		//Minimize
		$('.window-minimize').click(function(){$(this).parents('.window').each(function(){minimizeWindow(this)});});
		
		//Maximize
		$('.window-title').dblclick(function(){$(this).parents('.window').each(function(){maximizeToggleWindow(this)});});
		$('.window-toggle').click(function(){$(this).parents('.window').each(function(){maximizeToggleWindow(this)});});
		
		//Close
		$('.window-close').click(function(){$(this).parents('.window').each(function(){closeWindow(this)});});

		function closeWindow(elem){
			//$(elem).Shrink(200);
			$(elem).fadeOut(200);
		}
		
		function minimizeWindow(elem){
			$(elem).animate({bottom:0,left:0,width:0,height:0,opacity:0}, 500);
		}
		
		/**
		 * Function to toggle maximize windows
		 */
		function maximizeToggleWindow(elem){
			var info = $(elem).data('information');
			if (info && info.isMaximized == true) {
				windowSize(elem, 'windowed');
			} else {
				windowSize(elem, 'full')
			}
		}

		/**
		 * Function to resize the window to a specific value or make it fullscreen or smallscreen
		 */
		function windowSize(elem, width, height) {
			if (width == 'full') {				
				//Store information about the window
				$(elem).data('information', {isMaximized:true, x:$(elem).offset().left, y:$(elem).offset().top, z:$(elem).css('zIndex'), width:$(elem).width(), height:$(elem).height()});
				//Do full resize
				$(elem).addClass('maximized').draggable('disable').ResizableDestroy().css({top:0,left:0,width:'100%',height:'100%'});
				$(elem).find('.resize-handles').hide(0);
			} else if (width == 'windowed') {
				var info = $(elem).data('information');
				//Do windowed resize like last time
				$(elem).removeClass('maximized').draggable('enable').css({top:info.y+'px',left:info.x+'px',zIndex:info.z,width:info.width+'px',height:info.height+'px'});
				$(elem).find('.resize-handles').show(0);
				$(elem).Resizable(resizableOption);
				$(elem).data('information', {isMaximized:false, x:$(elem).offset().left, y:$(elem).offset().top, z:$(elem).css('zIndex'), width:$(elem).width(), height:$(elem).height()});
			} else if (height) {
				var info = $(elem).data('information');
				//Do windowed resize like last time
				$(elem).css({width:width+'px',height:height+'px'});
			} else {
				return false;
			}
			nimbus.screen.resize(elem);
		}
		
		/**
		 * Function to move an element to a specific location on the screen
		 */
		function moveTo(elem, x, y){
			$(elem).css({top: y + 'px',left: x + 'px'});
		}
	});
})(jQuery);