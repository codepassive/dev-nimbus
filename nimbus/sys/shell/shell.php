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
 * @subpackage:		Nimbus_shell
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
		//Add the default values to meta information
		$this->__meta = array(
				array('name' => 'robots', 'content' => 'noindex, nofollow'),
				array('name' => 'copyright', 'content' => '(c) 2009-2010 Nimbus Desktop Initiative. The Open Source Cloud Desktop'),
				array('name' => 'author', 'content' => 'STI Lipa Thesis Group 4. John Rocela, Mark Filemon'),
				array('name' => 'description', 'content' => 'Nimbus is an Open-Source Cloud centric desktop in the Browser')
			);
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
				'common' => $config->appurl . 'public/resources/skins/common/system.css',
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
				echo '<link rel="' . $link['rel'] . '" type="' . $link['type'] . '" href="' . $link['href'] . '"' . "\n";
				if ($link['media']) {
					echo ' media="' . $link['media'] . '"';
				}
				echo ' />';
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
				if (!$_this->_metaExists($meta) && isset($meta['name']) && isset($meta['content'])) {
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
		
		//Determine whether to get or set a meta name and meta value
		if (is_array($html)) {
			//put the array to the meta
			foreach ($html as $elem) {
				if (!$_this->_metaExists($elem) && isset($elem['html'])) {
					$_this->__body[] = $elem;
				}
			}
		} else {
			if ($namespace) {
				//put the name and the content to the meta array
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
		switch ($id) {
			case "header":
			
			break;
			case "footer":
			
			break;
		}
	}

}

?>