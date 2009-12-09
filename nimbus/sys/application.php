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
 * @subpackage:		Nimbus_system
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */

/**
 * The Application superclass
 *
 * @category:   		Application
 */
class Application extends API {

	/**
	 * Instance information of the application
	 *
	 * @access	Public
	 */
	public $instance = array();

	/**
	 * Name of the application
	 *
	 * @access	Public
	 */
	public $name;

	/**
	 * Determines whether the application will have a forced allow permission to a user or not
	 *
	 * @access	Public
	 */
	public $force = false;
	
	/**
	 * Output of the application
	 */
	public $output;

	/**
	 * Class constructor
	 *
	 * @access	Public
	 */
	public function __construct(){
		parent::__construct();
	}

	/**
	 * The constructor for an application. Executed first to gain access
	 * to the application superclass properties
	 *
	 * @access	Public
	 */
	public function __init($force = false){
		if ($this->user->isAllowed($this->name) || $force === true) {
			//The actual output is generated with this internal method
			$this->init();
		} else {
			//Return a JSON code that permission check failed
			global $language;
			echo json_encode(array('error' => $language['error_000G'] . '#000G'));
			exit();
		}
	}

	/**
	 * Launches an application from the application directory
	 *
	 * @access	Public
	 * @param	String $name name of the application
	 */
	public static function launch($name = null){
		//Route to the request if a launch name is not supplied
		$name = ($name) ? $name: request('app');
		//Proceed with launching the application
		if (is_array($name)) {
			foreach ($name as $n) {
				Application::launch($n);
			}
		} else {
			$path = APPLICATION_DIR . $name . DS . $name . '.php';
			if (file_exists($path)) {
				//Require the Application Index and instantiate the Application superclass				
				if ((request('token') == Session::get('token') && request('action')) || !request('action')) {
					require_once $path;
					$app = new $name();
					$action = request('action');
					if (!$action) {
						$app->__init($app->force);
					} else {
						$params = array();
						$i = 0;
						foreach ($_GET as $param) {
							if ($i > 1) {
								$params[] = $param
							}
							$i++;
						}
						call_user_func_array(array($app, $action()), $params);
					}
					echo $app->display();
				} else {
					global $language;
					echo json_encode(array('message' => $language['error_000H']));
				}
			}
			if (NIMBUS_DEBUG > 0) {
				global $language;
				Log::write(DEBUG_LOG_FILE, sprintf($language['error_000F'], $name));
			}
			return false;
		}
	}

}

?>