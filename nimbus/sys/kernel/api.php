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
 * @subpackage:		Nimbus_kernel
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */

/**
 * The API Class
 *
 * @category:   		API
 */
class API extends Cloud {

	/**
	 * Shell class property for the API. Enables Shell functionality
	 *
	 * @access	Public
	 */
	public $shell;

	/**
	 * Class constructor
	 *
	 * @access	Public
	 */
	public function __construct(){
		parent::__construct();
		//Delegate the classes to usable properties
		$this->shell = Shell::getInstance();
	}

	/**
	 * Delegate the API $this to a variable
	 *
	 * @access	Public
	 */
	public function delegate(){
		unset($this->db, $this->session);
		//Return the clean $this variable
		return $this;
	}

	/**
	 * Get the API element object
	 *
	 * @access:	Public
	 * @param:	String $id the identifier of the template file
	 * @param:	Array $options array of options for the element
	 */
	function element($id, $options = array()){
		//Include the Elements Base class
		include SYSTEM_DIR . 'kernel' . DS . 'api' . DS . 'elements.php';		
		//Get the class file
		$file = SYSTEM_DIR . 'kernel' . DS . 'api' . DS . 'elements' . DS . $id . '.php';
		if (file_exists($file)){
			include $file;
			//Create a new Element instance and render it
			$element = new $id($options);
			$element->render();
			//Return the $element for future use
			return $element;
		}
		return false;
	}

	/**
	 * Get the Progressbar element
	 *
	 * @access:	Public
	 * @param:	Array $options array of options for the element
	 */
	function progressbar($options = array()){
		$this->element('progressbar', $options);
	}

}

?>