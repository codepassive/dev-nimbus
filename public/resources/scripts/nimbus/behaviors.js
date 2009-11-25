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
(function($) {
	$(function() {
		/** DUMMY **/
		$('#loading_container').hide(0);
		/**
		 * Various Fixes
		 */
		$('.window').each(function(){
			var height = ($(this).height() - $(this).find('.window-title').height()) - 18;
			$(this).find('.window-content-wrapper').height(height);
		});
		
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
		$('.window.resizable').Resizable({
			minWidth: 250,
			minHeight: 150,
			handlers: {
				se: '.handleSE',
				e: '.handleE',
				ne: '.handleNE',
				n: '.handleN',
				nw: '.handleNW',
				w: '.handleW',
				sw: '.handleSW',
				s: '.handleS'
			},
			onResize: function(){
				var height = ($(this).height() - $(this).find('.window-title').height()) - 19;
				$(this).find('.window-content-wrapper').height(height);
			},
			onStop: function(){ //make sure it got resized right
				var height = ($(this).height() - $(this).find('.window-title').height()) - 19;
				$(this).find('.window-content-wrapper').height(height);
			},			
		});
	});
})(jQuery);