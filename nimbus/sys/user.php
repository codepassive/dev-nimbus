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
	protected $__information = array();

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
	 * Singleton function
	 *
	 * @access	Public
	 * @return Object instance of the object
	 */
	public static function getInstance(){
		static $_user;
		if (!is_object($_user)) {
			$_user = new User();
		}
		return $_user;
	}

	/**
	 * Class constructor
	 *
	 * @access	Public
	 */
	public function __construct(){
		parent::__construct();
	}

	/**
	 * Gets the current information of the currently logged in user
	 *
	 * @access	Public
	 */
	public function current($id){
		$this->__information = (isset($this->__information['id'])) ? $this->__information: $this->session->get('user-information');
		if (isset($this->__information[$id])) {
			return $this->__information[$id];
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
		$password = generatePassword($password);
		$result = $this->db->select("username='$username' AND password='$password'", null, 'accounts');
		$this->__setCurrentUser($result[0]['account_id']);
		return ($result) ? true: false;
	}

	/**
	 * Logs out the user and recreates a session
	 *
	 * @access	Public
	 */
	public function logout($id){
		$this->session->get('user-information');
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
	}

	/**
	 * Sets the current user via the supplied user ID.
	 *
	 * @access	Protected
	 * @params	Integer $id the ID of the user
	 */
	protected function __setCurrentUser($id){
		$result = $this->db->select("account_id=$id", null, 'accounts');
		//Delegate the properties
		$this->id = $id;
		$this->username = $result[0]['username'];
		//Assign to the information
		$this->__information = array_merge($result[0], $this->meta(), $this->personal());
		$this->session->set('user-information', $this->__information);
		return true;
	}

	/**
	 * Checks against the User ACL if allowed to access an object or not
	 *
	 * @access	Public
	 */
	public function isAllowed($object){
		return true;
	}

	/**
	 * Generates the meta data of a user from the database
	 *
	 * @access	Public
	 */
	public function meta(){
		$meta = array();
		$result = $this->db->select("SELECT * FROM accounts as u,meta as m WHERE u.account_id=m.meta_owner AND m.meta_table='accounts' AND u.account_id=" . $this->id);
		if ($result) {
			foreach ($result as $r) {
				$meta[$r['meta_name']] = $r['meta_value'];
			}
		}
		//Return the metadata
		return $this->_meta = $meta;
	}

	/**
	 * Generates the Personalize options of a user from the database
	 *
	 * @access	Public
	 */
	public function personal(){
		$personal = array();
		$result = $this->db->select("SELECT * FROM accounts as u,personalize as p WHERE u.account_id=p.user_id AND u.account_id=" . $this->id);
		if ($result) {
			foreach ($result as $r) {
				$personal[$r['option_name']] = $r['option_value'];
			}
		}
		//Return the personal options
		return $this->_personal = $personal;
	}

}

?>