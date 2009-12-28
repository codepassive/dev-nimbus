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
 * Namespaces
 */
var Nimbus,
	//General Namespaces
	HTML, Desktop, Application, Data, Connect, Utility, Extend,
	//Window Namespaces
	Window, Toolbar, Context, Menu, Frame,
	//Window Element Namespaces
	Textbox, Textarea, Form, Checkbox, Radio, Selectbox, Combobox, Button,
	//Common Element Namespaces
	Label, Image, Tabs, Slider, Progressbar,
	//Uncommon Element Namespaces
	Flash, Table, Grid;

/**
 * Nimbus Desktop API
 */
(function($){
	/**
	 * Create the Nimbus Object
	 */
	Nimbus = {
		/**
		 * Version of the Javascript API
		 */
		version: '1.0',

		/**
		 * Store for the Libraries to be used
		 */
		libraries: ['html', 'desktop', 'application'],

		/**
		 * Store for the elements to be loaded
		 */
		elements: ['window'],
		
		/**
		 * Flag to show that Nimbus is loaded completely
		 */
		isLoaded: false,
		
		/**
		 * Language store
		 */
		language: {},

		/**
		 * Initial Actions the Javascript API Should do on system load
		 */
		init: function(){
			//Count the Number of scripts to be loaded
			var max = BASE_SCRIPTS.length + Nimbus.libraries.length + Nimbus.elements.length;
			//The current scripts loaded
			var current = 0;
			//The current scripts loaded in percent
			var percent = 0;
			
			//Load the language for use in the javascript API
			$.getScript(SERVER_URL + '/?service=language');
			
			//Do something if one of the scripts are not loading
			setTimeout("Nimbus.messages.bootStopped()", 15000);
			
			//Get the progressbar element since it is the first element to be used on the system
			$.getScript(SERVER_URL + 'public/resources/scripts/nimbus/elements/progressbar.js', function(){				
				//Make a new Progressbar and use #system_loader as ID
				var progressbar = new Progressbar({
						id:'system_loader', 
						//Load the Login Application
						onComplete: function(){
							//Set the loaded flag for the system
							Nimbus.isLoaded = true;
							//Load the Login application
							Nimbus.Application.load('login', function(){
								//Once the application has been loaded, Ease out the loading container
								$('#loading-container').fadeOut(500);
							});
						}
				});

				//Iterate through the base scripts provided
				$.each(BASE_SCRIPTS, function(i, o){
					$.getScript(o.src, function(){
						//Change the Progressbar width
						progressbar.change(((current + 1) / max) * 100);
						current += 1;
					});
				});

				//Iterate through the Library scripts provided if any
				$.each(Nimbus.libraries, function(i, o){
					//Build the URL of the Library scripts
					var script = SERVER_URL + 'public/resources/scripts/nimbus/libraries/' + o + '.js';
					$.getScript(script, function(){
						//Change the Progressbar width
						progressbar.change(((current + 1) / max) * 100);
						current += 1;
					});
				});

				//Iterate through the Element scripts provided if any
				if (Nimbus.elements.length > 0) {
					$.each(Nimbus.elements, function(i, o){
						//Build the URL of the Element scripts
						var script = SERVER_URL + 'public/resources/scripts/nimbus/elements/' + o + '.js';
						$.getScript(script, function(){
							//Change the Progressbar width
							progressbar.change(((current + 1) / max) * 100);
							current += 1;
						});
					});
				}
			});
		},
		
		/**
		 * Method that generates a seperate window without regard to the current environment
		 */
		msgbox: function(options){
			$('#screen-workspace').before(options.content);
			//Create the window handle for use
			var window = new Window({id:options.id});
			window.fix(); //and fix the position of the window
			//Set the effect that makes the msgbox appear through a fadeIn
			$('#' + options.id).hide();
			$('#' + options.id).fadeIn(200);
		},
		
		/**
		 * Enable the modal screen if needed
		 */
		modal: function(enable){
			//Check if we need to enable the modal screen
			if (enable == true) {
				//Check if the modal screen hasn't already been placed
				if ($('#screen-modal').length == 0) {
					//Show the modal screen
					$('#screen-workspace').before('<div class="screen" id="screen-modal"></div>');
					$('#screen-modal').fadeIn(200);
				}
			} else {
				//Remove the friggin' modal screen
				$('#screen-modal').remove();
			}
		},

		/**
		 * Set of methods for static messages for Nimbus
		 */
		messages: {
			/**
			 * Event that a script cannot be loaded because of a slow connection or no connection at all
			 */
			bootStopped: function(){
				//Check first if the system has Loaded
				if (Nimbus.isLoaded == false) {
					//Output a boot_stopped message
					$('#loading-container p strong').html(Nimbus.language.boot_stopped);
				}
			}
		},
		
	}
})(jQuery);