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
 * @subpackage:		Nimbus_shell
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */

/**
 * The Shell class. Handles output
 *
 * @category:   		Shell
 */
class Shell {

	/**
	 * Class constructor
	 *
	 * @access	public
	 */
	public function __construct(){
		require_once 'common.php';
	}

	/**
	 * Apply the style configurations to the global configuration
	 *
	 * @access	public
	 */
	public function apply($config){
		//Apply to the configuration
		$config->styles = array(
				'common' => $config->appurl . 'public/resources/skins/common/system.css',
				'favico' => $config->appurl . 'public/resources/images/favico.jpg'
			);
		//Return the configuration class
		return $config;
	}

}

?>