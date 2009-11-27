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
 * @subpackage:	Nimbus_API
 * @copyright:	2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */
var nimbus;

/**
 * Constants
 */
var dragOptions = {
	handle: '.window-title',
	stack: {group: '.window', min: 100},
	start: function(){
		$(this).find('.window-content-inner').hide(0);
	},
	stop: function(){
		$(this).find('.window-content-inner').show(0);
	}
};
var resizableOption = {
	minWidth: 300,
	minHeight: 150,
	handlers: {
		se: '#' + $(this).find('.handleSE').attr('id'),
		e: '#' + $(this).find('.handleE').attr('id'),
		ne: '#' + $(this).find('.handleNE').attr('id'),
		n: '#' + $(this).find('.handleN').attr('id'),
		nw: '#' + $(this).find('.handleNW').attr('id'),
		w: '#' + $(this).find('.handleW').attr('id'),
		sw: '#' + $(this).find('.handleSW').attr('id'),
		s: '#' + $(this).find('.handleS').attr('id')
	},
	onResize: function(){
		nimbus.screen.resize(this);
		$(this).find('.window-content-inner').hide(0);
	},
	onStop: function(){
		nimbus.screen.resize(this);
		$(this).find('.window-content-inner').show(0);
	},
};

(function($){nimbus={

	/**
	 * Version of the API
	 */
	version: '1.0.0 Alpha',

	/** 
	 * Token for the requests
	 */
	token: false,

	/**
	 * Flag that shows connectivity
	 */
	isConnected: false,
	
	/**
	 * Timeout settings for connectivity checks
	 */
	timeOut: 30000,

	/**
	 * Initialize the Application
	 */
	init: function() {	
		//ping server and load the login screen
		$.getScript(SERVER_URL + '?app=login', function(){
			$('#screen-workspace').fadeIn(500);
			$('#loading-container').hide();
			nimbus.isConnected = true;
			nimbus.token = Login_data.token;
		});
		setTimeout("if(nimbus.isConnected == false){nimbus._noResponse()}", nimbus.timeOut);	
	},
	
	app: {
		launch: function(name, callback){
			$.getScript(SERVER_URL + '?app=' + name, callback);
		}
	},
	
	screen: {
		add: function(content, view_id){
			$('#screen-workspace').append(content);
			if (view_id) {
				$('#' + view_id).each(function(){
					$(this).draggable(dragOptions);
					$(this).Resizable(resizableOption);
					nimbus.screen.resize(this);
				});
			}
		},

		/**
		 * Function to resize an element upon the resize event
		 */
		resize: function(elem) {
			if (elem == 'all') {
				$('.window').each(function(){
					nimbus.screen.resize(this);
				});
			} else {
				var adjustment = ($(elem).hasClass('maximized')) ? 10: 18;
				var height = ($(elem).height() - $(elem).find('.window-title').height()) - adjustment;
				$(elem).find('.window-content-wrapper').height(height);
			}
		}
	},
	
	html: {
		head: function(elem, type, src, rel){
			switch (elem) {
				case "link":
					$('head').append('<link rel="' + rel + '" href="' + src + '" type="' + type + '" />');
				break;
			}
		},
	},

	/**
	 * Display a no response from Server message
	 */
	_noResponse: function(){
		$('body').html('No Response from Server');
	}

// End of script
}})(jQuery);
nimbus.init();