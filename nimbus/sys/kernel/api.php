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
	 * Instances of running modules or applications on the system
	 *
	 * @access	Public
	 */
	public $instances = array();

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
		$this->user = User::getInstance();
		
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

}

?>