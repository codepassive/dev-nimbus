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
 * @source:				http://www.devshed.com/c/a/PHP/Storing-PHP-Sessions-in-a-Database/
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
	 * The session data
	 *
	 * @access	private
	 */
	private $_session_data = null;

	/**
	 * The DB instance
	 *
	 * @access	protected
	 */
	protected $__db;

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
	public function __construct(){
		$this->__db = new Dbo();
		// Read the maxlifetime setting from PHP
		$this->lifetime = get_cfg_var("session.gc_maxlifetime");

		// Register this object as the session handler
		session_set_save_handler(
			array($this, "open"), 
			array($this, "close"),
			array($this, "read"),
			array($this, "write"),
			array($this, "destroy"),
			array($this, "gc")
		);
	}

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
		$this->generateID();
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
	public static function set($name, $val, $scope = null){
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
	public static function remove($name, $scope = null){
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
	public static function get($name, $scope = null){
		if ($scope) {
			return (isset($_SESSION[$scope][$name])) ? $_SESSION[$scope][$name]: false;
		} else {
			return (isset($_SESSION[$name])) ? $_SESSION[$name]: false;
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

	/**
	 * Open the session and do nothing
	 *
	 * @access	public
	 * @param	String $save_path save path used, but ignored because of DB use
	 * @param	String $session_name session name for the session
	 */
	public function open($save_path, $session_name) {
		global $sess_save_path;
		$sess_save_path = $save_path;
		// Don't need to do anything. Just return TRUE.
		return true;
	}

	/**
	 * Close the session
	 *
	 * @access	public
	 */
	public function close(){
		return true;
	}

	/**
	 * Read session data from the session ID
	 *
	 * @access	public
	 * @param	String $id session ID
	 */
	public function read($id){      
		//Set empty result
		$this->_session_data = null;		
		//Fetch session data from the selected database
		$time = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$result = $this->__db->select("SELECT `session_data` FROM `sessions` WHERE `session_id` = '$id' AND `expires` > $time AND `ip` = '$ip' AND `useragent` = '$useragent'");
		if ($result) {
			$this->_session_data = $result[0]['session_data'];
		}
		return $this->_session_data;
	}

	/**
	 * Write session data to a session ID
	 *
	 * @access	public
	 * @param	String $id session ID
	 * @param	String $data session data
	 */
	public function write($id, $data){
		//Build query
		$time = time() + $this->lifetime;
		$ip = $_SERVER['REMOTE_ADDR'];
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$uid = (defined('CURRENT_USER_ID')) ? CURRENT_USER_ID: 0;
		$this->__db->query("REPLACE INTO `sessions` (`session_id`, `session_data`, `expires`, `user_id`, `ip`, `useragent`) VALUES('$id', '$data', $time, $uid, '$ip', '$useragent')");
		//$this->__db->query("INSERT INTO `sessions`(`session_id`, `session_data`, `expires`, `user_id`, `ip`, `useragent`) VALUES('$id', '$data', $time, $uid, '$ip', '$useragent')");
		return true;
	}

	/**
	 * Destroy session data from a session ID
	 *
	 * @access	public
	 * @param	String $id session ID
	 */
	public function destroy($id) {
		//Build query
		$this->__db->query("DELETE FROM `sessions` WHERE `session_id` =	'$id'");
		return true;
	}

	/**
	 * Garbage collection method
	 *
	 * @access	public
	 */
	public function gc(){
		//Build DELETE query.  Delete all records who have passed the expiration time
		$time = time();
		$this->__db->query("DELETE FROM `sessions` WHERE `expires` < $time");
		return true;
	}

}

?>