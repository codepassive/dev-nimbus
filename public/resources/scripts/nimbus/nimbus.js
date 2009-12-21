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
 * Nimbus Desktop API
 */
var ndesk;
(function($){
	ndesk = {
		version: '1.0',
		elements: [],
		init: function(){
			var max = BASE_SCRIPTS.length + ndesk.elements.length;
			var current = 0;
			var percent = 0;
			$.getScript(SERVER_URL + 'public/resources/scripts/nimbus/elements/progressbar.js', function(){
				var progressbar = new Progressbar({id:'#system_loader'});
				$.each(BASE_SCRIPTS, function(i, o){
					$.getScript(o.src, function(){
						progressbar.change(((current + 1) / max) * 100);
						current += 1;
					});
				});
				if (ndesk.elements.length > 0) {
					$.each(ndesk.elements, function(i, o){
						var script = SERVER_URL + 'public/resources/scripts/nimbus/elements/' + o + '.js';
						$.getScript(script, function(){
							progressbar.change(((current + 1) / max) * 100);
							current += 1;
						});
					});
				}
			});
		},
	}
})(jQuery);
ndesk.init();