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
	 * Create the Connect Object
	 */
	Connect = Nimbus.Connect = {
		post: function(where, data, callback){
			$('#loading-container-desktop').show();
			$.post(where, data, function(result){callback(result);$('#loading-container-desktop').hide();}, "json");
		}
	}
})();