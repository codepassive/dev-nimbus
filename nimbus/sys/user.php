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
 * The User Manager
 *
 * @category:   		User
 */
class User extends Cloud {

	/**
	 * The user's ID
	 *
	 * @access	Public
	 */
	public $id = null;

	/**
	 * The user's username
	 *
	 * @access	Public
	 */
	public $username = null;

	/**
	 * The user's information
	 *
	 * @access	Public
	 */
	protected $__information;

	/**
	 * The user's meta data
	 *
	 * @access	Private
	 */
	private $_meta = array();

	/**
	 * The user's personal options
	 *
	 * @access	Private
	 */
	private $_personal = array();

	/**
	 * Class constructor
	 *
	 * @access	Public
	 */
	public function __construct(){
		parent::__construct();
		if ($this->isLoggedIn()) {
			$this->__information = (isset($this->__information->id)) ? $this->__information: $this->session->get('user-information');
			$this->id = $this->__information->account_id;
			$this->username = $this->__information->username;
			$this->meta();
			$this->personal();
		}
	}

	/**
	 * Gets the current information of the currently logged in user
	 *
	 * @access	Public
	 */
	public function current($id){
		$this->__information = (isset($this->__information->id)) ? $this->__information: $this->session->get('user-information');
		if (isset($this->__information->$id)) {
			return $this->__information->$id;
		}
		return null;
	}

	/**
	 * Logs in with the supplied username and password
	 *
	 * @access	Public
	 * @params	String $username the username of the user
	 * @params	String $password the password of the user
	 */
	public function login($username, $password){
		$this->logout();
		$result = $this->authenticate($username, $password, true);
		if ($result) {
			$this->__setCurrentUser($result['account_id']);
			return true;
		}
		return false;
	}

	/**
	 * Authenticate a username and password from the user database
	 *
	 * @access	Public
	 * @params	String $username the username of the user
	 * @params	String $password the password of the user
	 */
	public function authenticate($username, $password, $return = false){
		$password = generatePassword($password);
		$result = $this->db->select("username='$username' AND password='$password'", null, 'accounts');
		if ($result) {
			if ($return == true) {
				return $result[0]['account_id'];
			}
			return true;
		}
		return false;
	}

	/**
	 * Logs out the user and recreates a session
	 *
	 * @access	Public
	 */
	public function logout(){
		//Reset the class properties
		$this->id = $this->username = $this->__information = $this->_meta = $this->_personal = null;
		$this->session->regenerateID();
		return true;
	}

	/**
	 * Register from a request
	 *
	 * @access	Public
	 */
	public function register(){
		$meta = array();
		$personal = array();
		$default = array(
				'username' => '',
	            'password' => '',
	            'created' => time(),
	            'online' => 0,
	            'email' => '',
	            'first_name' => '',
	            'last_name' => '',
	            'nick_name' => '',
	            'website' => '',
	            'AIM' => '',
	            'Yahoo' => '',
	            'Gtalk' => '',
	            'Skype' => '',
	            'Description' => '',
	            'theme' => 'default',
	            'language' => 'en-us',
	            'background' => 'a:0:{}',
	            'desktop' => 'a:0:{}',
	            'startup' => 'a:0:{}',
	            'active_apps' => 'a:0:{}',
	            'refresh_rate' => 5,
	            'window' => 'a:0:{}',
	            'shortcuts' => 1
			);
		//Insert the User
		$username = $this->request->post['username'];
		$password = generatePassword($this->request->post['password']);
		$created = time();
		$this->db->query("INSERT INTO accounts(`username`, `password`, `created`, `online`) VALUES('$username', '$password', '$created', 0)");
		$id = $this->db->insertID;
		//Go through the request
		foreach ($this->default as $default) {
			if (isset($this->request->post['meta_' . $default])) {
				$meta[$default] = $this->request->post['meta_' . $default];
			}
			if (isset($this->request->post['personal_' . $default])) {
				$personal[$default] = $this->request->post['personal_' . $default];
			}
		}
		//Meta
		foreach ($meta as $m => $v) {
			$this->db->query("INSERT INTO meta(`meta_name`, `meta_value`, `meta_owner`, `meta_table`) VALUES('$m', '$v', '$id', 'accounts')");
		}
		//Personal
		foreach ($personal as $p => $v) {
			$this->db->query("INSERT INTO personalize(`user_id`, `option_name`, `option_value`) VALUES($id, '$p', '$v')");
		}
		//return the ID
		return $id;
	}

	/**
	 * Sets the current user via the supplied user ID.
	 *
	 * @access	Protected
	 * @params	Integer $id the ID of the user
	 */
	protected function __setCurrentUser($id){
		$this->__information = new stdClass();
		$result = $this->db->select("account_id=$id", null, 'accounts');
		if ($result) {
			//Delegate the properties
			$this->id = $id;
			$this->username = $result[0]['username'];
			//Assign to the information
			foreach ($result[0] as $n => $v) {
				$this->__information->$n = $v;
			}
			$this->meta();
			$this->personal();
			define('CURRENT_USER_ID', $this->id);
			unset($this->__information->password);
			$this->session->set('user-information', $this->__information);
			//Set a cookie to know that a session is active
			setcookie('_nimbus_user', 1, time() + config('security'));
			return true;
		}
		return false;
	}

	/**
	 * Checks against the User ACL if allowed to access an object or not
	 *
	 * @access	Public
	 * @param	String $object the handle name to an object
	 */
	public function isAllowed($object, $id = null){
		if ($this->isLoggedIn()) {
			$id = $this->id;
		}
		$id = ($id) ? $id: 0;
		$object = strtolower($object);
		$result = $this->db->select("SELECT * FROM accounts as u,acl as a WHERE ($id=a.accessor_id AND a.resource_handle='$object') OR (0=a.accessor_id AND a.resource_handle='$object')");
		if ($result) {
			return ($result[0]['allow'] == 1);
		}
		return false;
	}

	/**
	 * Checks if a user is logged in
	 *
	 * @access	Public
	 */
	public function isLoggedIn(){
		return (isset($_COOKIE['_nimbus_user']) && $this->session->get('user-information'));
	}

	/**
	 * Generates the meta data of a user from the database
	 *
	 * @access	Public
	 */
	public function meta(){
		$result = $this->db->select("SELECT * FROM accounts as u,meta as m WHERE u.account_id=m.meta_owner AND m.meta_table='accounts' AND u.account_id=" . $this->id);
		if ($result) {
			foreach ($result as $r) {
				$this->__information->{$r['meta_name']} = $this->_meta[$r['meta_name']] = $r['meta_value'];
			}
		}
		//Return the metadata
		return $this->_meta;
	}

	/**
	 * Generates the Personalize options of a user from the database
	 *
	 * @access	Public
	 */
	public function personal(){
		$result = $this->db->select("SELECT p.* FROM accounts as u,personalize as p WHERE u.account_id=p.user_id AND u.account_id=" . $this->id);
		if ($result) {
			foreach ($result as $r) {
				$this->__information->{$r['option_name']} = $this->_personal[$r['option_name']] = $r['option_value'];
			}
		}
		//Return the personal options
		return $this->_personal;
	}

}

?>