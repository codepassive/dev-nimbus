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
					if (callback) {
						callback(result);
					}
				}, "json");
			}
		},
		start: function(name, callback){
			if (Nimbus.Application.loaded[name] == true) {
				name = name.capitalize();
				setTimeout(name + ".init();", 0);
			} else {
				Nimbus.Application.load(name, callback);
			}
		},		
		addToTaskbar: function(options, callback){
			options.id = (options.id) ? 'taskbar-instance-' + options.id: 'taskbar-instance-' + Math.random();
			$('#nimbusbar-taskbar-noinstances:visible').hide(500);
			$('#nimbusbar-taskbar .items .item').removeClass('active');
			$('#nimbusbar-taskbar .items').append('<div class="item active" id="' + options.id + '"><a href="javascript:;" title="' + options.title + '"><span class="instance-name">' + options.title + '</span></a></div>');
			$('#' + options.id + ' a').hide(0).fadeIn(500).css({backgroundImage:"url('" + options.icon + "')"});
			//Bind the events
			$('#nimbusbar-taskbar .items .item').click(function(){
				$('#nimbusbar-taskbar .items .item').removeClass('active');
				$(this).addClass('active');
				/*Nimbus.Application.show(options.handle);
				if (callback != undefined) {
					callback();
				}*/
			});
		},
	}
})();