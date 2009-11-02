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
 * Class for loading UTF8 methods
 *
 * @category:   		UTF8
 * @source:		http://sourceforge.net/projects/phputf8/
 */
class nUtf8 {
	
	/**
	 * Class constructor
	 *
	 * @access	public
	 * @param  String $message message of the exception
	 * @param  Integer $code integer value of the exception code
	 */
	public function __construct(){}
	
	/**
	 * Determine if mbstring is loaded then load the proper libraries
	 *
	 * @access	public
	 */
	function load(){
		if (nUtf8::isMbstringLoaded() == true) {
			mb_internal_encoding('UTF-8');
			require_once LIBRARY_DIR . 'utf8' . DS . 'core.php';
			require_once LIBRARY_DIR . 'utf8' . DS . 'functions.php';
		} else {
			require_once LIBRARY_DIR . 'utf8' . DS . 'native.php';
		}
	}
	
	/**
	 * Determine if mbstring is loaded then load the proper libraries
	 *
	 * @access	public
	 * @return Boolean value if mbstring is loaded
	 */
	function isMbstringLoaded(){
		if (extension_loaded('mbstring') || function_exists('mb_substr')) {
			return true;
		} else {
			return false;
		}
	}
	
}

?>