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
 * The progressbar element
 */
(function(){
	/**
	 * Create the Progressbar Object
	 */
	Progressbar = function(options){

		/**
		 * ID of the progressbar
		 */
		this.id = options.id;
		
		/**
		 * The function to be executed once the progressbar completes
		 */
		this.onComplete = (options.onComplete) ? options.onComplete: false;
		
		/**
		 * The function to be executed once the progressbar changes
		 */
		this.onChange = (options.onChange) ? options.onChange: false;

		/**
		 * Change the current width of the progressbar
		 */
		this.change = function(percent){
			if (percent <= 100) {
				var elementWidth = $('#' + this.id).width();
				var width = elementWidth;
				//- 2 is the width in pixels of the outer and inner right borderlines
				width = ((width * percent) / 100) - 2;
				//Assign the complete and change events to a Local var
				var onComplete = this.onComplete;
				var onChange = this.onChange;
				//Animate the width change
				$('#' + this.id + ' .inner .bar').animate({width: width + 'px'}, 100, function(){
					//Execute the onchange event
					if (onChange != false) {
						onChange(percent);
					}
					//Execute the oncomplete event
					if (percent >= 100 || ((width + 2) == elementWidth)) {
						if (onComplete != false) {
							onComplete();
						}
					}
				});
			}
			//Else do not do anything
		};
	}
})();