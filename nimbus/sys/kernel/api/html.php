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
 * @subpackage:		Nimbus_api
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */

/**
 * The HTML Class
 *
 * @category:   		API
 */
class HTML extends API {

	/**
	 * Class constructor
	 *
	 * @access:	Public
	 */
	public function __construct(){
		parent::__construct();
	}

	/**
	 * Add a CSS Link element on the head of the html document
	 *
	 * @access:	Public
	 * @param:		String $href the path where the css style resides
	 */
	public function link($href){
		echo "\nNimbus.HTML.head('link', 'text/css', '{$href}', 'stylesheet');\n";
	}

}
?>