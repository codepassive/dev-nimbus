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
 * RPC class that creates client requests
 *
 * @category:   		RPC
 * @method:		REST
 */
class RPCClient extends RPC {

	public $rpcrequest = array();

	/**
	 * Class constructor.
	 * Passes responsibility to 3rd party RPC libraries if specified, else, makes use of built-in
	 * RPC methods.
	 *
	 * @access	Public
	 * @param:	String $url the url of the request
	 * @param:	String $method the method an RPC request will be made from. default is RPC_METHOD_REST
	 * @param:	Array $options the options for the current request
	 */
	public function __construct($url, $method = RPC_METHOD_REST, $options = array()){
		parent::__construct();
		/**
		 * Do not abstract the native methods included within the libraries provided. To use the
		 * libraries included, use their common usage pattern from their respective documentations.
		 * The reason for this is that these libraries offer more strict options in XMLRPC handling such
		 * that nimbus' native RPC method uses REST which is more flexible and is not strict.
		 */
		//Get the required files when the method is not REST.
		if ($method == RPC_METHOD_SOAP) {
			require_once 'soap' . DS . 'nusoap.php';
			return new nusoap_client($url, $options['wsdl'], $options['host'], $options['port'], $options['username'], $options['password']);
		} elseif ($method == RPC_METHOD_XMLRPC) {
			require_once 'xmlrpc' . DS . 'xmlrpc.php';
			return new xmlrpc_client($url, $options['port'], $options['method']);
		}

		//Build the request paremeters for our REST method
		$this->rpcrequest['url'] = $url;
		if (isset($options['username']) && isset($options['password'])) {
			$this->rpcrequest['username'] = $options['username'];
			$this->rpcrequest['password'] = $options['password'];
		}
		$this->rpcrequest['data'] = $options['data'];
	}

	/**
	 * Makes the requests and returns the result, unserialized or raw
	 *
	 * @access:	Public
	 * @param:	Boolean $unserialize return the result unserialized. default false
	 */
	/**
	 * TODO#00007: Store Header information from a REST response and more Proper CURL handling
	 */
	public function request($unserialize = false){
		//Build the Post variables
		$post = array();		
		if (isset($this->rpcrequest['username']) && isset($this->rpcrequest['password'])) {
			$post[] = 'username=' . $this->rpcrequest['username'];
			$post[] = 'password=' . $this->rpcrequest['password'];
		}		
		//Make the String
		foreach ($this->rpcrequest['data'] as $key => $value) {
			$post[] = $key . "=" . $value;
		}
		$post = implode('&', $post);
		
		//Do the CURL
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->rpcrequest['url']);
		{
			//TODO#00007
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			//Set the Post paremeters
		    curl_setopt($curl, CURLOPT_POST, 1);
		    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		}
		//Execute the CURL session
		$return = curl_exec($ch);
		curl_close($ch);
		//Return the Result
		if ($return) {
			return ($unserialize == true) ? unserialize($return): $return;
		}
		//Set the proper Header
		header("HTTP/1.0 400 Bad Request");
		return false;
	}

}

/**
 * This is an example usage of the Client library. the client library must be accessed first from the
 * cloud superclass property named RPC.
 * 
 * The typical Talk method is formed like this, the request is passed through the third parameter
 * $client = $this->RPC->talk('http://thesis/?rpc&controller=db&action=fetch', null, array('data' => $_POST));
 *
 * The result of the request will be fetched through here, unserialized or not
 * $client->request();
 *
 * For the REST method, the controller and action must be specified in the URI string
 * and the action's parameters should be declared as POST
 */

?>