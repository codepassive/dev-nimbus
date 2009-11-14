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
 * Class for the registry pattern
 *
 * @category:   		Registry
 */
class Registry {

	/**
	 * The cache where registry values will be stored
	 *
	 * @access	private
	 */
	private $_cache = array();

	/**
	 * Singleton function
	 *
	 * @access	public
	 * @return Object instance of the object
	 */
	public static function getInstance(){
		static $_registry;
		if (!is_object($_registry)) {
			$_registry = new Registry();
		}
		return $_registry;
	}
	
	/**
	 * Set named values to registry
	 *
	 * @access	public
	 * @param String $name name of the value being set
	 * @param String $value the value being set
	 */
	public static function set(){
		$_this = Registry::getInstance();
		$args = func_get_args();
		$argC = func_num_args();
		if ($argC > 0) {
			if (is_array($args[0])) {
				foreach ($args[0] as $r => $v) {
					$_this->set($r, $v);
				}
			} else {
				$_this->_cache[(string) $args[0]] = $args[1];
			}
		} else {
			throw new NimbusException("Registry cannot set a null value to a null pointer.");
		}
	}
	
	/**
	 * Get named value from registry
	 *
	 * @access	public
	 * @param String $name name of the value being requested
	 */
	public static function get($name = null){
		$_this = Registry::getInstance();
		if (!empty($_this->_cache[$name])) {
			return $_this->_cache[$name];
		} else {
			return false;
		}
	}
	
	/**
	 * Remove named value from registry
	 *
	 * @access	public
	 * @param String $name name of the value going to be removed
	 */
	public static function remove($name = null){
		$_this = Registry::getInstance();
		unset($_this->_cache[$name]);
	}
	
	/**
	 * Dump information from the registry variable
	 *
	 * @access	public
	 * @param Array the private registry variable
	 */
	public static function dump(){
		$_this = Registry::getInstance();
		return $_this->_cache;
	}
	
}

?>