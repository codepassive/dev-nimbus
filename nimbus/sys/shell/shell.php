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

	protected $__meta = array();
	protected $__link = array();
	protected $__script = array();

	/**
	 * Singleton function
	 *
	 * @access	public
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
	 * @access	public
	 */
	public function __construct(){
		//Require the common shell functions
		require_once 'common.php';
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
	 * @access	public
	 */
	public function apply($config){
		//Apply to the configuration
		$config->styles = array(
				//Common
				'common' => $config->appurl . 'public/resources/skins/common/system.css',
				'favico' => $config->appurl . 'public/resources/images/favico.jpg'
			);
		//Return the configuration class
		return $config;
	}

	public static function head(){
		//Get the static instance of the Shell object
		$_this = Shell::getInstance();
		//Build the meta values
		if (!empty($_this->__meta)) {
			foreach ($_this->__meta as $meta) {
				echo "\n" . '<meta name="' . $meta['name'] . '" content="' . $meta['content'] . '"/>';
			}
		}
		//Build the additional style values
		if (!empty($_this->__link)) {
			foreach ($_this->__link as $link) {
				echo "\n" . '<link rel="' . $link['rel'] . '" type="' . $link['type'] . '" href="' . $link['href'] . '"';
				if ($link['media']) {
					echo ' media="' . $link['media'] . '"';
				}
				echo ' />';
			}
		}
	}
	
	public function meta($name, $content){
		//Get the static instance of the Shell object
		$_this = Shell::getInstance();
		
		//Determine whether to get or set a meta name and meta value
		if (is_array($name)){
			//put the array to the meta
		} else {
			if ($content) {
				//put the name and the content to the meta array
			} else {
				//get the meta value
			}
		}
	}
	
	public static function body(){
		//Get the static instance of the Shell object
		$_this = Shell::getInstance();
		
		/**
		 * Prepend additional elements from applications and/or modules
		 * onto the Base HTML body.
		 */

	}
	
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