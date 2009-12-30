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

		/**
		 * Add an element to the current workspace
		 */
		add: function(content, view_id){
			$('#screen-workspace-' + Nimbus.Desktop.currentWorkspace).append(content);
			if (view_id) {
				$('#' + view_id).hide().fadeIn(500);
			}
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