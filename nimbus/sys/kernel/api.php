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
	 * User class property for the API. Enables User methods
	 *
	 * @access	Public
	 */
	public $user;

	/**
	 * Instances of running modules or applications on the system
	 *
	 * @access	Public
	 */
	public $instances = array();

	/**
	 * Class constructor
	 *
	 * @access	Public
	 */
	public function __construct(){
		parent::__construct();
		//Delegate the User class to usable properties
		$this->user = new User();
		//Delegate the Shell class to usable properties
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
	 * Register an application onto the instances array
	 * 
	 * @access	Public
	 * @param	String $name name of the application to be registered
	 * @param	Boolean $global determine if the application is ran globally or by a user
	 */
	public function register($name, $global = null){
		if (!in_array($name, $this->instances)) {
			$in = array(
						'name' => $name,
						'pid' => generateHash($name . $this->config->salt),
						'started' => time(),
						'user' => ($global) ? $this->user->current('id'): $this->config->appname
					);
			$this->instances[] = $in;
			return $in;
		}
	}

	/**
	 * Get the API element object
	 *
	 * @access:	Public
	 * @param:	String $id the identifier of the template file
	 * @param:	Array $options array of options for the element
	 */
	public function element($id, $options = array()){	
		//Get the class file
		$file = SYSTEM_DIR . 'kernel' . DS . 'api' . DS . 'elements' . DS . $id . '.php';
		if (file_exists($file)){
			include_once $file;
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
	public function progressbar($options = array()){
		return $this->element('progressbar', $options);
	}

	/**
	 * Get the Window element
	 *
	 * @access:	Public
	 * @param:	Array $options array of options for the element
	 */
	public function window($options = array()){
		return $this->element('window', $options);
	}
	
	public function msgbox($options = array()){
		//Set Proper Header
		header('Content-Type: text/javascript');
		header('HTTP/1.0 200 OK');
		//Start Output Buffering
		ob_start();
		//Generate an ID for the message box
		$id = 'message-box-' . generateHash(microtime());
		//Create the Window
		$windop = array('id' => $id, 'classes' => array('message-box'), 'type' => 1, 'x' => 'center', 'y' => 'center', 'name' => $id, 'title' => $options['title'], 'content' => array('<div class="message ' . $options['type'] . '">' . $options['text'] . '</div>'), 'visible' => true, 'resizable' => false, 'draggable' => false, 'pinnable' => false, 'minimizable' => false, 'toggable' => false, 'hasIcon' => false);
		if ($options['noChoice'] == true) {
			$windop = array_merge($windop, array('closable' => false, 'modal' => true));
		} else {
			$windop = array_merge($windop, array('closable' => true));
		}
		$this->window($windop);
		//Get the contents from the Output Buffer
		$output = ob_get_contents();
		ob_end_clean();
		//Echo out the msgbox script
		if (isset($options['modal']) && $options['modal'] == true) {
			echo "Nimbus.msgbox(" . json_encode(array('id' => $id, 'modal' => true, 'content' => $output)) . ");\n";
		} else {
			echo "Nimbus.msgbox(" . json_encode(array('id' => $id, 'content' => $output)) . ");\n";
		}
	}

}

?>