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
 * Create the Module class
 */
class gitModule extends Module implements ModuleInterface {

	/**
	 * Name of the Module
	 *
	 * @access	Public
	 */
	public $name = 'git';

	/**
	 * Class Construct
	 *
	 * @access	Public
	 */
	public function __construct(){
		//require_once the base class
		require_once $this->name . DS . 'gitbase.php';
		require_once $this->name . DS . 'gitcheckout.php';
		require_once $this->name . DS . 'gitclone.php';
		require_once $this->name . DS . 'git.php';
		//Initialize the base class for use
		$this->module = new Git();
	}

}

?>