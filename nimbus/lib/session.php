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
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */

/**
 * Class for accessing and creating sessions
 *
 * @category:   		Session
 */
/**
 * TODO#00002 - DB Support for sessions
 */
class Session {

	/**
	 * The lifetime a session should last
	 *
	 * @access	public
	 */
	public $lifetime = 1440;

	/**
	 * The session ID
	 *
	 * @access	private
	 */
	private $_sid = null;

	/**
	 * Singleton function
	 *
	 * @access	public
	 * @return Object instance of the object
	 */
	public static function getInstance(){
		static $_session;
		if (!is_object($_session)) {
			$_session = new Session();
		}
		return $_session;
	}

	/**
	 * Class constructor
	 *
	 * @access	public
	 */
	public function __construct(){}

	/**
	 * Start the session and generate a session ID
	 *
	 * @access	public
	 */
	public function start(){
		global $language;
		if (!headers_sent() && empty($_SESSION)) {
			session_start();
			$this->generateID();
			Log::write(DEBUG_LOG_FILE, "Session Class " . $language['started']);
		}
	}

	/**
	 * Generate a session ID
	 *
	 * @access	public
	 */
	public function generateID(){
		$this->_sid = session_id();
	}

	/**
	 * Clear the current session and regenerate a new one
	 *
	 * @access	public
	 */
	public function regenerateID(){
		$this->destroy(session_id());
		session_regenerate_id();
		session_id();
		$this->_sid = session_id();
	}

	/**
	 * Get the current session ID
	 *
	 * @access	public
	 */
	public function getID(){
		return $this->_sid;
	}

	/**
	 * Set a named value onto the session
	 *
	 * @access	public
	 * @param	String $name the name of the value to be set
	 * @param	String $value the value to be set
	 * @param	String $scope the scope of the value to be set to avoid collision
	 */
	public function set($name, $val, $scope = null){
		if ($scope) {
			$_SESSION[$scope][$name] = $val;
		} else {
			$_SESSION[$name] = $val;
		}
	}

	/**
	 * Remove a named value from the session
	 *
	 * @access	public
	 * @param	String $name the name of the value to be removed
	 * @param	String $scope the scope of the value to be removed if available
	 */
	public function remove($name, $scope = null){
		if ($scope) {
			unset($_SESSION[$scope][$name]);
		} else {
			unset($_SESSION[$name]);
		}
	}

	/**
	 * Get a named value from the session
	 *
	 * @access	public
	 * @param	String $name the name of the value to be fetched
	 * @param	String $scope the scope of the value to be fetched if available
	 */
	public function get($name, $scope = null){
		if ($scope) {
			return $_SESSION[$scope][$name];
		} else {
			return $_SESSION[$name];
		}
	}

	/**
	 * Stop and destroy the session
	 *
	 * @access	public
	 */
	public function stop(){
		global $language;
		session_destroy();
		$this->regenerateID();
		$_SESSION = array();
		Log::write(DEBUG_LOG_FILE, "Session Class " . $language['stopped']);
	}

}

?>