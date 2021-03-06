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
 * @subpackage:		Nimbus_services
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */
 
class shareService extends API {

	public function __construct(){
		parent::__construct();
		//Params
		if (isset($this->request->get['key'])) {
			$key = $this->request->get['key'];
			$request = $this->db->query("SELECT * FROM share WHERE key='" . $key . "'");
			if (!empty($request)) {
				include_once 'nimbus/sys/resource.php';
				new Resource($request[0]['resource'], true);
			}
		}
	}

}

new shareService(); 
 
?>