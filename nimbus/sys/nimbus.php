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
 * The front controller for the Cloud Superclass
 *
 * @category:   		Cloud
 */
class Nimbus extends Cloud {

	/**
	 * Class constructor
	 *
	 * @access	public
	 */
	public function __construct(){
		parent::__construct();
	}

	/**
	 * Determines whether the application has a request
	 *
	 * @access	public
	 * @return	Boolean
	 */
	public function beingCalled(){
		if (!empty($this->request)) {
			return true;
		}
		return false;
	}

}

class Application {public static function launch(){}}

?>