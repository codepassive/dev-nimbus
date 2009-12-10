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
 * Query the database from a public location
 *
 * @category:   		Query
 */
class Query extends Cloud {

	/**
	 * Class constructor
	 *
	 * @access	Public
	 */
	public function __construct(){
		//Check if a token is attached to a request
		if (isset($this->request->items['token'])) {
			$token = new Token();
			//Get the request from the token supplied
			$request = $token->getRequest($this->request->items['token']);
			if ($request['query']) {
				//Return the db query
				return $this->db->query($request['query']);
			}
		}
	}

}

?>