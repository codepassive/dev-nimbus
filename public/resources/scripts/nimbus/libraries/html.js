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
	HTML = Nimbus.HTML = {
		loaded: [],
		head: function(elem, type, src, rel){
			switch (elem) {
				case "link":
					if (!Nimbus.HTML.loaded[src]) {
						$('head').append('<link rel="' + rel + '" href="' + src + '" type="' + type + '" />');
						Nimbus.HTML.loaded[src] = src;
					}
				break;
			}
		}
	}
})();