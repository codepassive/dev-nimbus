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
 * Initial Directives
 */
$(function() {
	$('#loading_container').hide(0);
	$('.screen').fadeIn(500);
});
/**
 * Windows
 */
(function($) {
	$(function() {
		/**
		 * Window Behaviors
		 */
		//Draggable
		$('.window.draggable').draggable({
				handle: '.window-title',
				stack: {group: '.window', min: 100},
				start: function(){
					$(this).find('.window-content-inner').hide(0);
				},
				stop: function(){
					$(this).find('.window-content-inner').show(0);
				}
			});
		//Resizable
		resize('all');
		$(document).resize(function(){resize('all');});
		$('.window').each(function(){
			if ($(this).hasClass('resizable')) {
				$(this).Resizable({
					minWidth: 250,
					minHeight: 150,
					handlers: {
						se: '#' + $(this).find('.handleSE').attr('id'),
						e: '#' + $(this).find('.handleE').attr('id'),
						ne: '#' + $(this).find('.handleNE').attr('id'),
						n: '#' + $(this).find('.handleN').attr('id'),
						nw: '#' + $(this).find('.handleNW').attr('id'),
						w: '#' + $(this).find('.handleW').attr('id'),
						sw: '#' + $(this).find('.handleSW').attr('id'),
						s: '#' + $(this).find('.handleS').attr('id')
					},
					onResize: function(){
						resize(this);
						$(this).find('.window-content-inner').hide(0);
					},
					onStop: function(){
						resize(this);
						$(this).find('.window-content-inner').show(0);
					},
				});
			}
		});
		//Maximize
		$('.window-title').dblclick(function(){
			if ($(this).parents('.window').data('isMaximized') == true) {
				alert(2);
			} else {
				$(this).parents('.window').addClass('maximized').draggable('disable').ResizableDestroy().css({top:0,left:0,width:'100%',height:'100%'});
				$(this).parents('.window').find('.resize-handles').hide(0);
				$(this).parents('.window').each(function(){resize(this);});
				$(this).parents('.window').data('isMaximized', true);
			}
		});
		
		/**
		 * Function to resize an element upon the resize event
		 */
		function resize(elem) {
			if (elem == 'all') {
				$('.window').each(function(){
					resize(this);
				});
			} else {
				var adjustment = ($(elem).hasClass('maximized')) ? 10: 18;
				var height = ($(elem).height() - $(elem).find('.window-title').height()) - adjustment;
				$(elem).find('.window-content-wrapper').height(height);
			}
		}
	});
})(jQuery);