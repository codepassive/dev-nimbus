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
	 * Gets the current information of the currently logged in user
	 *
	 * @access	Public
	 */
	public function current($id){
		$information = Session::get('uinfo');
		if (isset($information[$id])) {
			//Return the information
			return $information[$id];
		}	
		return false;
	}

	/**
	 * Checks against the User ACL if allowed to access an object or not
	 *
	 * @access	Public
	 */
	public function isAllowed($object){
		return true;
	}

}

?>