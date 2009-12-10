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
 * The Token Manager
 *
 * @category:   		Token
 */
class Token extends Cloud {

	/**
	 * Singleton function
	 *
	 * @access	Public
	 * @return Object instance of the object
	 */
	public static function getInstance(){
		static $_token;
		if (!is_object($_token)) {
			$_token = new Token(false);
		}
		return $_token;
	}

	/**
	 * Class constructor
	 *
	 * @access	Public
	 */
	public function __construct($generate = false){
		parent::__construct();
		if ($generate == true) {
			Token::generate();
		}
	}

	/**
	 * Generate a new Token and store it onto the database
	 *
	 * @access	Public
	 * @param	Mixed $request supply an array to attach to the token
	 */
	public static function generate($request = null){
		$_this = Token::getInstance();
		$public = generateHash(microtime() . $_this->config->salt);
		$private = md5($public);
		$request = (is_array($request)) ? serialize($request): null;
		$time = time() + SECURITY_HIGH;
		$_this->db->query("INSERT INTO tokens(`public`, `private`, `request`, `expires`) VALUES('$public', '$private', '$request', $time)");
		echo json_encode($public);
	}

	/**
	 * Get a request stored as a token
	 *
	 * @access	Public
	 * @param	String $token a reference to the token with the request
	 */
	public static function getRequest($token){
		$_this = Token::getInstance();
		$result = $_this->db->select("public='$token'", null, 'tokens');
		if ($result) {
			if ($result[0]['private'] == md5($result[0]['public'])) {
				return $result[0]['request'];
			}
		}
		return false;
	}

	/**
	 * Clear up the database from expired tokens
	 *
	 * @access	Public
	 */
	public static function cleanUp(){
		$_this = Token::getInstance();
		$time = time();
		$_this->db->query("DELETE FROM `tokens` WHERE `expires` < $time");
	}

}
?>