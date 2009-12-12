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
 * @subpackage:	Nimbus_system
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */
/**
 * Require the Server and Client files
 */
require_once 'kernel' . DS . 'rpc' . DS . 'server.php';
require_once 'kernel' . DS . 'rpc' . DS . 'client.php';

/**
 * Class that manages RPC methods and visibility
 *
 * @category:   		RPC
 * @method:		REST
 */
class RPC extends Cloud {

	/**
	 * Class constructor
	 *
	 * @access	Public
	 */
	public function __construct(){
		parent::__construct();
	}

	/**
	 * Makes the requests and returns the result, unserialized or raw
	 *
	 * @access:	Public
	 * @param:	String $url the url of the request
	 * @param:	String $method the method an RPC request will be made from. default is RPC_METHOD_REST
	 * @param:	Array $options the options for the current request
	 * @param:	Boolean $unserialize return the result unserialized. default false
	 */
	public function talk($url, $method = RPC_METHOD_REST, $options = array(), $unserialize = false){
		//Create an RPC Client instance
		$client = new RPCclient($url, $method, $options);
		return $client->$request($unserialize);
	}

	/**
	 * Listens to requests made to the RPC server
	 *
	 * @access:	Public
	 */
	public function listen(){
		//Create an RPC Server instance
		$server = new RPCServer();
		return $server->respond();
	}

}
?>