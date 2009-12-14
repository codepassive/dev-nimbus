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
 * @subpackage:	Nimbus_rpc
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */

/**
 * RPC class that exposes public methods
 *
 * @category:   		RPC
 */
class RPCServer extends RPC {

	private $_exposed = array();

	/**
	 * Class constructor
	 *
	 * @access	Public
	 */
	public function __construct(){
		parent::__construct();
	}

	/**
	 * Responds to a request made to the RPCserver. Does not apply to SOAP or XMLRPC
	 *
	 * @access:	Public
	 */
	public function respond(){
		//Get the request requirements
		$controller = (isset($this->request->get['controller'])) ? isset($this->request->get['controller']): false;
		$action = (isset($this->request->get['action'])) ? isset($this->request->get['action']): false;
		$params = (isset($this->request->post)) ? isset($this->request->post): false;
		//Fetch the Response
		if ($controller && $action && isset($this->_exposed[$controller][$action])) {
			$allow = true;
			if ($this->_exposed[$controller][$action]['authenticate']) {
				//Authenticate the user
				$id = $this->user->authenticate($params['username'], $params['password']);
				if (!$this->user->isAllowed(serialize(array($controller, $method)), $id)) {
					$allow = false;
				}
			}
			//Call the function if authorized
			if ($allow && method_exists($controller, $method)) {
				header("HTTP/1.0 200 OK");
				$object = new $controller();
				call_user_func_array(array($object, $method), $params);
				return true;
			}
			//Set an Unauthorized Status upon return
			header("HTTP/1.0 401 Unauthorized");
			return false;
		}
		header("HTTP/1.0 406 Not Acceptable");
		return false;
	}

	/**
	 * Expose the function for RPC calls
	 *
	 * @access:	Public
	 * @param:	String $controller the name of the controller that contains the method $action
	 * @param:	String $action the method of the controller that has the procedures
	 * @param:	Boolean $authenticate authenticate the request or not
	 */
	public function expose($controller, $action, $authenticate = true){
		//Recursive implementation
		if (is_array($action)) {
			foreach ($action as $a) {
				$this->_exposed($controller, $a, $authenticate);
			}
			return true;
		}
		//Set to the exposed store
		if (!isset($this->_exposed[$controller][$action])) {
			$this->_exposed[$controller][$action] = array(
					'name' => $action,
					'authenticate' => $authenticate
				);
			return true;
		}
		return false;
	}

}
?>