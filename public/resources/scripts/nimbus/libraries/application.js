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
			$('#loading-container-desktop').show();
			if (Nimbus.Application.loaded[name] != true) {
				$.getScript(SERVER_URL + '/?app=' + name, function(result){
					//Flag a running instance of the application
					Nimbus.Application.loaded[name] = true;
					//Run the callback
					if (callback) {
						callback(result);
					}
					$('#loading-container-desktop').hide();
				}, "json");
			} else {
				name = name.capitalize();
				$.getScript(SERVER_URL + '/?app=' + name + '&new', function(result){
					eval("if(" + name + "_multiple != false){" + name + "_instance++;if(" + name + "_instance > 0){" + name + "[" + name + "_instance].init();" + name + "[" + name + "_instance + 1] = " + name + "[0];}}else{if($('." + name.toLowerCase() + "').length == 0){" + name + ".init();}}");
					$('#loading-container-desktop').hide();
				});
			}
		},	
		addToTaskbar: function(options){
			if (options.showInTaskbar == true) {
				var id = 'taskbarinstance-' + options.id;
				$('#nimbusbar-taskbar-noinstances:visible').fadeOut(500);
				$('#nimbusbar-taskbar .items .item').removeClass('active');
				$('#nimbusbar-taskbar .items').prepend('<div class="item active" id="' + id + '"><a href="javascript:;" title="' + options.title + '"><span class="instance-name">' + options.title + '</span></a></div>');
				$('#' + id + ' a').hide(0).fadeIn(500).css({backgroundImage:"url('" + options.icon + "')"});
				//Bind the events
				$('#' + id).click(function(){
					var id = $(this).attr('id');
					Nimbus.Desktop.window.toggle(id.replace("taskbarinstance-", ""));
					$('#nimbusbar-taskbar .items .item').removeClass('active');
					$(this).addClass('active');
					$('.window').removeClass('active');
					$('#' + id.replace("taskbarinstance-", "")).addClass('active');
					var zindex = 530;
					$('.window').each(function(){
						if ($(this).css('zIndex') >= zindex) {
							zindex = $(this).css('zIndex') + 1;
						}
					});
					$('#' + id.replace("taskbarinstance-", "")).css({zIndex: zindex});
				});
			}
		},
		removeFromTaskbar: function(id){
			var id = '#taskbarinstance-' + id;
			$(id).fadeOut(1000);
			setTimeout("$('" + id + "').remove()", 100);
		},
		close: function(id, options){
			Nimbus.Application.removeFromTaskbar(id);
			$('#' + id + ', .child-' + id).fadeOut(500);
			setTimeout("$('#" + id + ", .child-" + id + "').remove();if ($('#nimbusbar-taskbar .items .item').length == 0){$('#nimbusbar-taskbar-noinstances:hidden').fadeIn(500);}", 500);
		}
	}
})();