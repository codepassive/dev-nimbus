<?php
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
 * @subpackage:	Nimbus_shell
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */

/**
 * The Shell class. Handles output
 *
 * @category:   		Shell
 */
class Shell extends Cloud {

	/**
	 * Generate the meta part of the Head
	 *
	 * @access	Public
	 */
	protected $__meta = array();

	/**
	 * Set elements that are in the body
	 *
	 * @access	Public
	 */
	protected $__body = array();

	/**
	 * The links to be appended to the head
	 *
	 * @access	Public
	 */
	protected $__link = array();

	/**
	 * The scripts to be appended to the header or the footer
	 *
	 * @access	Public
	 */
	protected $__script = array();

	/**
	 * Singleton function
	 *
	 * @access	Public
	 * @return Object instance of the object
	 */
	public static function getInstance(){
		static $_shell;
		if (!is_object($_shell)) {
			$_shell = new Shell();
		}
		return $_shell;
	}

	/**
	 * Class constructor
	 *
	 * @access	Public
	 */
	public function __construct(){
		parent::__construct();
		//Add the default values to meta information
		$this->__meta = array(
				array('name' => 'robots', 'content' => 'noindex, nofollow'),
				array('name' => 'copyright', 'content' => '(c) 2009-2010 Nimbus Desktop Initiative. The Open Source Cloud Desktop'),
				array('name' => 'author', 'content' => 'STI Lipa Thesis Group 4. John Rocela, Mark Filemon'),
				array('name' => 'description', 'content' => 'Nimbus is an Open-Source Cloud centric desktop in the Browser')
			);
		//Add the javascript libraries to be used
		$this->__script = array(
					'jquery' => array('src' => $this->config->path['scripts'] . 'jquery/jquery.js', 'version' => JQUERY_JS_VER),
					'jquery-ui' => array('src' => $this->config->path['scripts'] . 'jquery/jquery-ui.js', 'version' => JQUERY_UI_JS_VER),
					'jquery-interface' => array('src' => $this->config->path['scripts'] . 'jquery/interface.js', 'version' => JQUERY_INTERFACE_JS_VER),
					'jquery-plugin-hotkeys' => array('src' => $this->config->path['scripts'] . 'jquery/plugins/hotkeys.js', 'version' => JQUERY_PLUGIN_HOTKEYS_JS_VER),
					'tinymce' => array('src' => $this->config->path['scripts'] . 'tinymce/tinymce.js', 'version' => TINYMCE_JS_VER),
					'swfupload' => array('src' => $this->config->path['scripts'] . 'swfupload/swfupload.js', 'version' => SWFUPLOAD_JS_VER),
					'behaviors' => array('src' => $this->config->path['scripts'] . 'nimbus/behaviors.js', 'version' => NIMBUS_BEHAVIORS_JS_VER),
					'nimbus' => array('src' => $this->config->path['scripts'] . 'nimbus/nimbus.js', 'version' => NIMBUS_JS_VER),
				);
		//Script Location IDs
		$this->__script['header'] = array(
								$this->__script['jquery'], //Initially we will include the base javascript class
								$this->__script['jquery-ui'],
								$this->__script['jquery-interface'],
								$this->__script['nimbus'],
								$this->__script['behaviors'],
							);
		$this->__script['footer'] = array();
	}

	/**
	 * Apply the style configurations to the global configuration
	 *
	 * @access	Public
	 */
	public function apply($config){
		//Require the common shell functions
		require_once 'common.php';
		//Apply to the configuration
		$config->styles = array(
				//Common
				'common' => $config->appurl . 'public/resources/skins/common/style.css?ver=1.0',
				'favico' => $config->appurl . 'public/resources/images/favico.jpg'
			);
		//Return the configuration class
		return $config;
	}

	/**
	 * Generate the Head part of the base HTML file
	 *
	 * @access	Public
	 */
	public static function head(){
		//Get the static instance of the Shell object
		$_this = Shell::getInstance();

		//Build the meta values
		if (!empty($_this->__meta)) {
			foreach ($_this->__meta as $meta) {
				echo '<meta name="' . $meta['name'] . '" content="' . $meta['content'] . '"/>' . "\n";
			}
		}
		//Build the additional style values
		if (!empty($_this->__link)) {
			foreach ($_this->__link as $link) {
				echo '<link rel="' . $link['rel'] . '" type="' . $link['type'] . '" href="' . $link['href'];
				if (isset($link['version'])) {
					echo '?ver=' . $link['version'] . '"';
				} else {
					echo '"';
				}
				if (isset($link['media'])) {
					echo ' media="' . $link['media'] . '"';
				}
				echo ' />' . "\n";
			}
		}
	}

	/**
	 * Sets an Array or a Key value, or get a meta key from the meta property of the shell
	 *
	 * @access	Public
	 * @param	Mixed $name string name or a collection of meta key/values for setting/getting meta keys
	 * @param	String $content the content of the meta key if a name is provided
	 */
	/**
	 * Use in the API as API::shell::meta();
	 */
	public static function meta($name, $content){
		//Get the static instance of the Shell object
		$_this = Shell::getInstance();
		
		//Determine whether to get or set a meta name and meta value
		if (is_array($name)){
			//put the array to the meta
			foreach ($name as $meta) {
				if (!($_this->_metaExists($meta)) && isset($meta['name']) && isset($meta['content'])) {
					$_this->__meta[] = $meta;
				}
			}
		} else {
			if ($content) {
				//put the name and the content to the meta array
				$_this->__meta[] = array('name' => $name, 'content' => $content);
			} else {
				//get the meta value
				return $_this->_getMeta($name);
			}
		}
	}

	/**
	 * Check if a meta key already exists. Implementation is a bit jaunty as
	 * it may take a long time for a large meta library. but that's highly unlikely
	 *
	 * @access	Private
	 * @param	Array $meta the meta array to be checked
	 */
	private function _metaExists($meta){
		$result = false;
		foreach ($this->__meta as $m) {
			if (($meta['name'] == $m['name']) && ($meta['content'] == $m['content'])) {
				$result = true;
			}
		}
		return $result;
	}

	/**
	 * Get the value of the meta provided
	 *
	 * @access	Private
	 * @param	String $name the key of the meta to be searched
	 */
	private function _getMeta($name){
		$result = false;
		foreach ($this->__meta as $m) {
			if ($name == $m['name']) {
				$result = $m['content'];
			}
		}
		return $result;
	}

	/**
	 * Sets a Body element through a collection or a Key value
	 *
	 * @access	Public
	 */
	/**
	 * Use in the API as API::shell::body();
	 */
	public static function body($html, $namespace){
		//Get the static instance of the Shell object
		$_this = Shell::getInstance();
		
		if (is_array($html)) {
			foreach ($html as $elem) {
				if (isset($elem['html'])) {
					$_this->__body[] = $elem;
				}
			}
		} else {
			if ($namespace) {
				$_this->__body[] = array('id' => $namespace, 'html' => $html);
			} else {
				$_this->__body[] = array('html' => $html);
			}
		}
	}

	/**
	 * Generate the Body part of the base HTML file
	 *
	 * @access	Public
	 */
	public static function screen(){
		//Get the static instance of the Shell object
		$_this = Shell::getInstance();
		
		/**
		 * Prepend additional elements from applications and/or modules
		 * onto the Base HTML body.
		 */
		if (!empty($_this->__body)) {
			foreach ($_this->__body as $body) {
				$body['id'] = (isset($body['id'])) ? $body['id']: substr(md5(time()), 1, 10);
				echo '<div class="screen" id="screen-' . $body['id'] . '">' . $body['html'] . "</div>\n";
			}
		}
	}

	/**
	 * Generate the Scripts part of the base HTML file
	 *
	 * @access	Public
	 */
	public static function scripts($id){
		//Get the static instance of the Shell object
		$_this = Shell::getInstance();
		
		//Get the location onto which the scripts will be generated		
		if (!empty($_this->__script[$id])) {
			foreach ($_this->__script[$id] as $script) {
				$output = '<script type="text/javascript"';
				if (isset($script['src']) && !isset($script['content'])) {
					$output .= ' src="' . $script['src'];
					if (isset($script['version'])) {
						$output .= '?ver=' . $script['version'] . '">';
					} else {
						$output .= '">';
					}
				} else {
					 $output .= '>' . $script['content'];
				}
				echo $output . '</script>' . "\n";
			}
		}
	}

	/**
	 * Get a script element individually by the script's namespace
	 *
	 * @access	Public
	 */
	public function script($id, $version){
		//Get the static instance of the Shell object
		$_this = Shell::getInstance();
		
		$script = $_this->__script[$id];
		$script = $script[0];
		if (isset($script)) {
			$output = '<script type="text/javascript"';
			if ($script['src'] && !$script['content']) {
				$output .= ' src="' . $script['src'];
				if (isset($script['version'])) {
					$output .= '?ver=' . $script['version'] . '">';
				} else {
					$output .= '">';
				}
			} else {
				 $output .= '>' . $script['content'];
			}
			echo $output . '</script>' . "\n";
		}
	}

	/**
	 * Sets a Javascript src or content onto a namespace
	 *
	 * @access	Public
	 */
	/**
	 * Use in the API as API::shell::javascript();
	 */
	public static function javascript($namespace, $src, $version = '1.0'){
		//Get the static instance of the Shell object
		$_this = Shell::getInstance();
		
		if (is_array($src)) {
			foreach ($src as $js) {
				if (!($_this->_scriptExists($js)) && isset($js['src']) || isset($js['content'])) {
					$_this->__script[$namespace][] = array_merge($js, array('version' => $version));
				}
			}
		} else {
			if (is_url($src)) {
				$_this->__script[$namespace][] = array('src' => $src, 'version' => $version);
			} else {
				$_this->__script[$namespace][] = array('content' => $src, 'version' => $version);
			}
		}
	}

	/**
	 * Check if a javascript is set in the namespace already
	 *
	 * @access	Private
	 * @param	Array $script the script to be checked
	 */
	private function _scriptExists($script){
		$result = false;
		foreach ($this->__script as $s) {
			if ((($script['src'] == $s['src']) || ($script['content'] == $s['content'])) && $script['version'] == $s['version']) {
				$result = true;
			}
		}
		return $result;
	}

}

?>