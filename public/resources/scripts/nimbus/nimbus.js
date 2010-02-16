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
(function(){
	Nimbus = {
		/**
		 * Version of the Javascript API
		 */
		version: '1.0',

		/**
		 * Store for the Libraries to be used
		 */
		libraries: ['html', 'desktop', 'application', 'connect'],

		/**
		 * Store for the elements to be loaded
		 */
		elements: ['window'],
		
		/**
		 * Session token for the current user
		 */
		token: null,
		
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
								setTimeout("$('#loading-container').fadeOut(500);", 2000);
								$('.screen').show();
							});
						}
				});

				//Iterate through the base scripts provided
				$.each(BASE_SCRIPTS, function(i, o){
					$.getScript(o.src, function(){
						//Change the Progressbar width
						$ = jQuery = window.jQuery = window.$;
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
		
		Dialog: {
			open: function(option, callback){
				var tab = 'one';
				var ret = []
				if (!$('.window.confirm.dialog.message-box').length) {
					var parent = option.parent;
					var html = '<div id="' + option.id + '" class="static window confirm dialog message-box draggable center-x center-y child-' + parent + '"><div class="wrapper" id="windowwrapper"><div class="titlebar"><div class="title" style="margin-left:0px;">Open File Location</div><div class="actions"><a href="javascript:void(0);" class="action action-closable"></a><div class="clear"></div></div></div><div class="outer"><div class="inner"><div class="content" style="height:300px;width:400px;">';
					html += '<div style="float:left;" class="vertical-tabs"><ul><li><a href="javascript:;" class="selected" name="one">External URL</a></li><li><a href="javascript:;" name="two">Upload File</a></li><li><a href="javascript:;" name="three">User Directory</a></li></ul></div>';
					html += '<div class="tab-content one focus" style="text-align:center;padding:90px 0;"><h2>Use an External Resource</h2><p>Input a URL of the resource you wish to use</p><input type="text" class="text dialog-external" value="http://" style="width:250px;"/></div><div class="tab-content two" style="text-align:center;padding:90px 0 50px;"><h2>Upload a file</h2><p>Select a file or Drag it onto this window.</p><div class="swfupload-control"><span id="spanButtonPlaceholder-' + option.id + '"></span></div><div style="padding:24px;" class="upload_message"></div><div style="height:68px;padding:0 12px;text-align:left;overflow:auto;" class="uploaded_message"></div><input type="hidden" value="" id="upload_hidden" /></div>';
					html += '<div class="tab-content three" style="overflow:auto;padding:8px;"><div class="tree" style="padding:8px;height:266px;width:268px;overflow:auto;"></div></div>';
					html += '</div><div class="clear"></div><div class="buttons"><input type="button" value="Cancel" class="button"/>&nbsp;<input type="button" value="Open" class="button"/></div></div></div></div></div>';
					var window = new Window({thtml: html,id: option.id, parent: option.parent});
					window.fix();
					$('#' + option.id).hide();
					$('#' + option.id).fadeIn(200);
					$('#' + option.id + ' .vertical-tabs a').click(function(){
						tab = $(this).attr('name');
						$('#' + option.id + ' .vertical-tabs a').removeClass('selected');
						$(this).addClass('selected');
						$('#' + option.id + ' .tab-content').hide();
						$('#' + option.id + ' .' + tab).show();
					});
					$('#' + option.id + ' .action-closable').click(function(){$('#' + option.id).remove();});
					$('#' + option.id + '.draggable').draggable({opacity: 0.7, handle:'.titlebar', stack:{group:'.draggable', min: 550}, start:function(){$(this).find('.content').css({visibility:'hidden'});$(this).find('.titlebar .title').css({cursor:'move'});}, stop:function(){$(this).find('.content').css({visibility:'visible'});$(this).find('.titlebar .title').css({cursor:'default'});}});
					$('#' + option.id + ' .buttons .button:eq(0)').click(function(){
						$('#' + option.id).remove();
					})
					$.getScript(SERVER_URL + 'public/resources/scripts/swfupload/jquery/vendor/swfupload/swfupload.js', function(){
						$.getScript(SERVER_URL + 'public/resources/scripts/swfupload/jquery/src/jquery.swfupload.js', function(){
							var a = option.allow;
							var allow = [];
							$.each(a, function(i, e){
								allow[i] = "*." + e;
							});
							$('#' + option.id + ' .swfupload-control').swfupload({
								upload_url: SERVER_URL + '?service=uploader&path=root\Uploads&PHPSESSID=' + $('#session_id').val() + '&username=' + $('.userbutton').text(), // Relative to the SWF file (or you can use absolute paths)
								file_post_name: "file",
								file_size_limit : "2147483647", // 100MB
								file_types : allow.join(";"),
								file_types_description : "Open Files",
								file_upload_limit : "99",
								file_queue_limit : "0",
								button_image_url : SERVER_URL + "public/resources/scripts/swfupload/jquery/vendor/swfupload/XPButtonUploadText_61x22.png", // Relative to the SWF file
								button_placeholder_id : 'spanButtonPlaceholder-' + option.id,
								button_width: 61,
								button_height: 22,
								flash_url : SERVER_URL + "public/resources/scripts/swfupload/jquery/vendor/swfupload/swfupload.swf"
							});
							// assign our event handlers
							$('.swfupload-control')
								.bind('fileQueued', function(event, file){
									// start the upload once a file is queued
									$(this).swfupload('startUpload');
								})
								.bind('uploadProgress', function(file, bytes, total){
									var percentage = Math.round((bytes * 100) / total);
									$('#' + option.id + ' .upload_message').html('Uploading: ' + percentage + '%');
								})
								.bind('uploadError', function(file, code, message){
									//alert('error' + message);
								})
								.bind('uploadSuccess', function(file, data, response){
									ret = [];
									txt = eval(response);
									$.each(txt, function(i, e){
										$('#' + option.id + ' .uploaded_message').append('<p>' + e.name + ' uploaded successfully</p>');
										var title = e.path;
										var username = $('.userbutton').text();
										title = title.replace(/\\/g, "/");
										title = title.replace(username + '/drives/', '');
										ret[i] = SERVER_URL + '?res=user://' + title;
									});
									//$('#' + option.id + ' #upload_hidden').val(str.join(","));
								})
								.bind('uploadComplete', function(event, file){
									//alert('Upload completed - '+ file.name +'!');
									$('#' + option.id + ' .upload_message').html('No items in queue...');
									$(this).swfupload('startUpload');
								});

						});						
					});
					Nimbus.Connect.post(SERVER_URL + '?app=fileexplorer&action=grid', {allow:option.allow.join(","), serialize:1}, function(result){
						$('#' + option.id + ' .tab-content.three .tree').html(Nimbus.Dialog.grid(result));
						$.getScript(SERVER_URL + 'public/resources/scripts/jquery/plugins/tree/jquery.tree.js', function(){
							Nimbus.HTML.head('link', 'text/css', SERVER_URL + 'public/resources/scripts/jquery/plugins/tree/themes/default/style.css', 'stylesheet');
							$('#' + option.id + ' .tab-content.three .tree').tree({
								type: {
									renameable: false,
									deletable: false,
									creatable: false,
									draggable: false
								},
								rules: {
									multiple: option.multiple
								}
							});
						});
					});
					$('#' + option.id + ' .upload_message').html('No items in queue...');
					$.getScript(SERVER_URL + 'public/resources/scripts/swfupload/dnd_uploader.js', function(){
						$('#' + option.id + ' .two').upload5({
							beforeLoad:function() {
								this.gate = SERVER_URL + '?service=uploader&path=root\Uploads';
							},
							onProgress: function(event) {
								if (event.lengthComputable) {
									var percentage = Math.round((event.loaded * 100) / event.total);
									$('#' + option.id + ' .upload_message').html('Uploading: ' + percentage + '%');
								}
							}, onComplete:function(event,txt) {
								txt = eval(txt);
								$.each(txt, function(i, e){
									$('#' + option.id + ' .uploaded_message').append('<p>' + e.name + ' uploaded successfully</p>');
									var title = e.path;
									var username = $('.userbutton').text();
									title = title.replace(/\\/g, "/");
									title = title.replace(username + '/drives/', '');
									ret[i] = SERVER_URL + '?res=user://' + title;
									
								});
								//$('#' + option.id + ' #upload_hidden').val(str.join(","));
								$('#' + option.id + ' .upload_message').html('No items in queue...');
							}
						});
							
					});
					$('#' + option.id + ' .buttons .button:eq(1)').live('click', function(){
						switch (tab) {
							case "one":
								ret = [];
								ret[0] = SERVER_URL + '?res=' + $('#' + option.id + ' .dialog-external').val();
							break;
							case "three":
								ret = [];
								var ref = $.tree.reference('#' + option.id + ' .tab-content.three .tree').selected_arr;
								var o = 0;
								if (option.multiple) {
									for(var key in ref){
										var node = ref[key];
										var username = $('.userbutton').text();
										var title =  $.tree.reference('#' + option.id + ' .tab-content.three .tree').get_node(node).find('a').attr("title");
										title = title.replace(/\\/g, "/");
										title = title.replace(username + '/drives/', '');
										ret[o] = SERVER_URL + '?res=user://' + title;
										o++;
									}
								} else {
									ref = $.tree.reference('#' + option.id + ' .tab-content.three .tree').selected;
									var node = ref;
									var username = $('.userbutton').text();
									var title =  $.tree.reference('#' + option.id + ' .tab-content.three .tree').get_node(node).find('a').attr("title");
									title = title.replace(/\\/g, "/");
									title = title.replace(username + '/drives/', '');
									ret[o] = SERVER_URL + '?res=user://' + title;
								}
								$.tree.reference('#' + option.id + ' .tab-content.three .tree').destroy();
							break;
						}
						if (callback) {
							$('#' + option.id + ' .buttons .button:eq(1)').die();
							callback(ret);
						}
						$('#' + option.id + ', .child-' + option.id).remove();
					});
				}
			},
			save: function(option, callback){
				var ret = {}
				if (!$('.window.confirm.dialog.message-box').length) {
					var parent = option.parent;
					path = option.path ? option.path: 'drives/root/Documents';
					filename = option.filename ? option.filename: 'file.txt';
					var html = '<div id="' + option.id + '" class="static window confirm dialog message-box draggable center-x center-y child-' + parent + '"><div class="wrapper" id="windowwrapper"><div class="titlebar"><div class="title" style="margin-left:0px;">Save File</div><div class="actions"><a href="javascript:void(0);" class="action action-closable"></a><div class="clear"></div></div></div><div class="outer"><div class="inner"><div class="content">';
					html += '<div style="margin:8px;"><table cellspacing="0" cellpadding="0" border="0"><tr><td>Path&nbsp;</td><td><input type="text" class="path" value="' + path + '" style="width:200px"/></td></tr><tr><td>Filename&nbsp;</td><td><input type="text" value="' + filename + '" class="filename" style="width:140px"/></td></tr></table></div>';
					html += '</div><div class="clear"></div><div class="buttons"><input type="button" value="Cancel" class="button"/>&nbsp;<input type="button" value="Save" class="button"/></div></div></div></div></div>';
					var window = new Window({thtml: html,id: option.id, parent: option.parent});
					window.fix();
					$('#' + option.id).hide();
					$('#' + option.id).fadeIn(200);
					$('#' + option.id + ' .action-closable').click(function(){$('#' + option.id).remove();});
					$('#' + option.id + '.draggable').draggable({opacity: 0.7, handle:'.titlebar', stack:{group:'.draggable', min: 550}, start:function(){$(this).find('.content').css({visibility:'hidden'});$(this).find('.titlebar .title').css({cursor:'move'});}, stop:function(){$(this).find('.content').css({visibility:'visible'});$(this).find('.titlebar .title').css({cursor:'default'});}});
					$('#' + option.id + ' .buttons .button:eq(0)').click(function(){
						$('#' + option.id).remove();
					});
					$('#' + option.id + ' .buttons .button:eq(1)').click(function(){
						ret.path = $('#' + option.id + ' .path').val();
						ret.filename = $('#' + option.id + ' .filename').val();
						Nimbus.Connect.post(option.save, {filename: ret.filename, content: option.content, path: ret.path}, function(result){
							Nimbus.msgbox2({id:'closedialog-' + option.id,title:'File Save Action', content: result.message});	
							ret.response = result.response;
							$('#' + option.id).remove();
							if (callback) {
								callback(ret);
							}
						});
					});
					$('#' + option.id + ' .buttons .button:eq(1)').die();
				}
			},
			custom: function(option, callback){
				var tab = 'one';
				var ret = []
				if (!$('.window.confirm.dialog.message-box').length) {
					var parent = option.parent;
					var html = '<div id="' + option.id + '" class="static window confirm dialog message-box draggable center-x center-y child-' + parent + '"><div class="wrapper" id="windowwrapper"><div class="titlebar"><div class="title" style="margin-left:0px;">' + option.title + '</div><div class="actions"><a href="javascript:void(0);" class="action action-closable"></a><div class="clear"></div></div></div><div class="outer"><div class="inner"><div class="content" style="height:' + option.height + ';width:' + option.width + ';overflow:auto;"></div><div class="clear"></div><div class="buttons"><input type="button" value="Cancel" class="button"/>&nbsp;<input type="button" value="Save" class="button"/></div></div></div></div></div>';
					var window = new Window({thtml: html,id: option.id, parent: option.parent});
					window.fix();
					$('#' + option.id).hide();
					$('#' + option.id).fadeIn(200);
					$('#' + option.id + ' .action-closable').click(function(){$('#' + option.id).remove();});
					$('#' + option.id + '.draggable').draggable({opacity: 0.7, handle:'.titlebar', stack:{group:'.draggable', min: 550}, start:function(){$(this).find('.content').css({visibility:'hidden'});$(this).find('.titlebar .title').css({cursor:'move'});}, stop:function(){$(this).find('.content').css({visibility:'visible'});$(this).find('.titlebar .title').css({cursor:'default'});}});
					$('#' + option.id + ' .buttons .button:eq(0)').click(function(){
						$('#' + option.id).remove();
					});
					$('#' + option.id + ' .content').html($('#' + option.content_id).html());
					$('#' + option.id + ' .buttons .button:eq(1)').click(function(){if (option.save) {option.save();}if (callback) {callback();}});
					$('#' + option.id + ' .buttons .button:eq(0)').click(function(){if (option.cancel) {option.cancel();}if (callback) {callback();}});
					$('#' + option.id + ' .buttons .button:eq(1)').die();
					if (option.load) {
						option.load();
					}
				}
			},
			justOk: function(option, callback){
				var tab = 'one';
				var ret = []
				if (!$('.window.confirm.dialog.message-box').length) {
					var parent = option.parent;
					var html = '<div id="' + option.id + '" class="static window confirm dialog message-box draggable center-x center-y child-' + parent + '"><div class="wrapper" id="windowwrapper"><div class="titlebar"><div class="title" style="margin-left:0px;">' + option.title + '</div><div class="actions"><a href="javascript:void(0);" class="action action-closable"></a><div class="clear"></div></div></div><div class="outer"><div class="inner"><div class="content" style="height:' + option.height + ';width:' + option.width + ';overflow:auto;"></div><div class="clear"></div><div class="buttons"><input type="button" value="Ok" class="button"/></div></div></div></div></div>';
					var window = new Window({thtml: html,id: option.id, parent: option.parent});
					window.fix();
					$('#' + option.id).hide();
					$('#' + option.id).fadeIn(200);
					$('#' + option.id + ' .action-closable').click(function(){$('#' + option.id).remove();});
					$('#' + option.id + '.draggable').draggable({opacity: 0.7, handle:'.titlebar', stack:{group:'.draggable', min: 550}, start:function(){$(this).find('.content').css({visibility:'hidden'});$(this).find('.titlebar .title').css({cursor:'move'});}, stop:function(){$(this).find('.content').css({visibility:'visible'});$(this).find('.titlebar .title').css({cursor:'default'});}});
					$('#' + option.id + ' .buttons .button:eq(0)').click(function(){
						$('#' + option.id).remove();
					});
					$('#' + option.id + ' .content').html($('#' + option.parent + ' .' + option.content_id).html());
					$('#' + option.id + ' .buttons .button:eq(0)').click(function(){if (callback) {callback();}});
					if (option.load) {
						option.load();
					}
				}
			},
			grid: function(ob){
				var string = '<ul>';
				$.each(ob, function(i, obj){
					var n = obj.name;
					n.toLowerCase();
					string += '<li><ins class="' + obj.type + '">&nbsp;</ins><a href="javascript:;" class="item" name="' + n.replace(" ", "") + '" title="' + obj.path + '">' + obj.name + '</a>';
					if (obj.sub) {
						string += Nimbus.Dialog.grid(obj.sub);
					}
					string += '</li>';
				});
				string += '</ul>';
				return string;
			}
		},
		
		confirm: function(option, okay, cancel){
			var html = '<div id="' + option.id + '" class="window confirm dialog message-box draggable center-x center-y"><div class="wrapper" id="windowwrapper"><div class="titlebar"><div class="title" style="margin-left:0px;">' + option.title + '</div><div class="actions"><a href="javascript:void(0);" class="action action-closable"></a><div class="clear"></div></div></div><div class="outer"><div class="inner"><div class="content"><div class="message help">' + option.content + '</div></div><div class="buttons"><input type="button" value="Cancel" class="button"/>&nbsp;<input type="button" value="OK" class="button"/></div></div></div></div></div>';
			var window = new Window({thtml: html,id: option.id});
			window.fix();
			$('#' + option.id).hide();
			$('#' + option.id).fadeIn(200);
			$('#' + option.id + ' .buttons .button:eq(0)').click(function(){
				$('#' + option.id).remove();
				if (cancel) {
					cancel();
				}
			})
			$('#' + option.id + ' .buttons .button:eq(1)').click(function(){
				$('#' + option.id).remove();
				if (okay) {
					okay();
				}
			})
			$('#' + option.id + ' .action-closable').click(function(){$('#' + option.id).remove();});
			$('#' + option.id + '.draggable').draggable({opacity: 0.7, handle:'.titlebar', stack:{group:'.draggable', min: 550}, start:function(){$(this).find('.content').css({visibility:'hidden'});$(this).find('.titlebar .title').css({cursor:'move'});}, stop:function(){$(this).find('.content').css({visibility:'visible'});$(this).find('.titlebar .title').css({cursor:'default'});}});
		},
		
		
		
		msgbox2: function(option, okay){
			if ($('#' + option.id).length == 0) {
				var html = '<div id="' + option.id + '" class="window msgbox dialog message-box draggable center-x center-y"><div class="wrapper" id="windowwrapper"><div class="titlebar"><div class="title" style="margin-left:0px;">' + option.title + '</div><div class="actions"><a href="javascript:void(0);" class="action action-closable"></a><div class="clear"></div></div></div><div class="outer"><div class="inner"><div class="content"><div class="message help">' + option.content + '</div></div><div class="buttons"><input type="button" value="OK" class="button"/></div></div></div></div></div>';
				var window = new Window({thtml: html,id: option.id});
				window.fix();
				$('#' + option.id).hide();
				$('#' + option.id).fadeIn(200);
				$('#' + option.id + ' .buttons .button:eq(0)').click(function(){
					$('#' + option.id).remove();
					if (okay) {
						okay();
					}
				})
				$('#' + option.id + ' .action-closable').click(function(){$('#' + option.id).remove();});
				$('#' + option.id + '.draggable').draggable({opacity: 0.7, handle:'.titlebar', stack:{group:'.draggable', min: 550}, start:function(){$(this).find('.content').css({visibility:'hidden'});$(this).find('.titlebar .title').css({cursor:'move'});}, stop:function(){$(this).find('.content').css({visibility:'visible'});$(this).find('.titlebar .title').css({cursor:'default'});}});
			}
		},
		
		/**
		 * Method that generates a seperate window without regard to the current environment
		 */
		msgbox: function(options){
			if (options.html == true) {
				$('#screen-workspace-' + Nimbus.Desktop.currentWorkspace).before(options.content);
				//Create the window handle for use
				var window = new Window({id:options.id});
			} else {
				var window = new Window({
					html: true,
					id: null,
					classes: ['message-box'],
					type: 1, 
					x: 'center',
					y: 'center',
					title: Nimbus.language.information_title,
					content: ['<div class="message information">' + options.message + '</div>'],
					buttons: options.buttons,
					visible: true,
					resizable: false,
					draggable: true,
					pinnable: false,
					minimizable: false,
					toggable: false,
					hasIcon: false
				});
			}
			window.fix(); //and fix the position of the window
			//Set the effect that makes the msgbox appear through a fadeIn
			$('#' + window.id).hide();
			$('#' + window.id).fadeIn(200);
			$('#' + window.id + ' .buttons .button:eq(0)').click(function(){
				$('#' + window.id).remove();
				Nimbus.modal(false);
			});
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
					$('#screen-workspace-' + Nimbus.Desktop.currentWorkspace).before('<div class="screen" id="screen-modal"></div>');
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
})();