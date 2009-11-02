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
 * Class for loading public classes
 *
 * @category:   		Loader
 */
class Loader {

	/**
	 * Abstract function to load library classes
	 *
	 * @access	public
	 * @param  String $name file name or path denoted by a / without the .php extension
	 * @param  Boolean $instantiate flag whether to instantiate a called class
	 */
	public static function library($name, $instantiate = false){
		Loader::__load($name, LIBRARY_DIR, $instantiate);
	}
	
	/**
	 * Abstract function to load kernel classes
	 *
	 * @access	public
	 * @param  String $name file name or path denoted by a / without the .php extension
	 * @param  Boolean $instantiate flag whether to instantiate a called class
	 */
	public static function kernel($name, $instantiate = false){
		Loader::__load($name, SYSTEM_DIR . 'kernel', $instantiate);
	}
	
	/**
	 * Function to load public classes
	 *
	 * @access	protected
	 * @param  String $name file name or path denoted by a / without the .php extension
	 * @param  String $path root directory where $name can be fetched
	 * @param  Boolean $instantiate flag whether to instantiate a called class
	 * @return Object of new class instance
	 */
	protected static function __load($name, $path, $instantiate = false){
		if (is_array($name)) {
			foreach ($name as $n) {
				Loader::__load($n, $path);
			}
		} else {
			if (file_exists($path . DS . $name . '.php')) {
				require_once $path . DS . $name . '.php';
				if ($instantiate === true) {
					return new $name;
				}
				return true;
			}
			return false;
		}
	}

}
?>