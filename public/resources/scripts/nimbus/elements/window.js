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
/**
 * The window element
 */
(function(){
	/**
	 * Create the Window Object
	 *
	 * @SKELETON
	 */
	Window = function(options){

		/**
		 * ID of the progressbar
		 */
		this.id = options.id; 

		/**
		 * Class fixes for the window
		 */
		this.fix = function(){			
			//Window Position Fix
			$('.center-x').each(function(){
				var width = $(this).width() / -2;
				$(this).css({left:'50%', marginLeft: width + 'px'});
			});
			$('.center-y').each(function(){
				var height = $(this).height() / -2;
				$(this).css({top:'50%', marginTop: height + 'px'});
			});
			//Enable the Modal Screen when a single window has the modal class
			$('.window').each(function(){
				if ($(this).hasClass('modal')) {
					Nimbus.modal(true);
				}
			});
		}
		
	}
})();