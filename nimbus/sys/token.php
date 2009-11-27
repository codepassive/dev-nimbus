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
 * The Token Manager
 *
 * @category:   		Token
 */
class Token extends Cloud {

	/**
	 * Tokens stored for use
	 *
	 * @access	Private
	 */
	private $_tokens = array();

	/**
	 * Singleton function
	 *
	 * @access	Public
	 * @return Object instance of the object
	 */
	public static function getInstance(){
		static $_token;
		if (!is_object($_token)) {
			$_token = new Token();
		}
		return $_token;
	}

	public function __construct(){}
	
	public static function create($name = null){
		$_this = Token::getInstance();
		$token = generateHash(microtime());
		if ($name) {
			if (!Session::get($name . '_token', $token)) {
				Session::set($name . '_token', $token);
				return $_this->_tokens[$name] = $token;
			}
			return Session::get($name . '_token');
		} else {
			if (!Session::get('token')) {
				Session::set('token', $token);
				return $_this->_tokens[$name] = $token;
			}
			return Session::get('token');
		}
	}

}
?>