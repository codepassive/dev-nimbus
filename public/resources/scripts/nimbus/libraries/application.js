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
	Application = Nimbus.Application = {
		loaded: [],
		load: function(name, callback){
			if (!Nimbus.Application.loaded[name] == 1) {
				$.getScript(SERVER_URL + '/?app=' + name, function(result){
					//Flag a running instance of the application
					Nimbus.Application.loaded[name] = true;
					//Run the callback
					callback(result);
				}, "json");
			}
		},
	}
})();