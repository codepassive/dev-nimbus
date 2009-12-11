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
 * @subpackage:	Nimbus_kernel
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */

/**
 * The Hook Class
 *
 * @category:   		Hook
 */
class Hook extends Cloud {

	/**
	 * The hooks store
	 *
	 * @access	Private
	 */
	private $_hooks = array();

	/**
	 * Class constructor
	 *
	 * @access	Public
	 */
	public function __construct(){
		parent::__construct();
	}

	/**
	 * Attach a function to a namespace in the hooks store
	 *
	 * @access	Public
	 * @param	String $namespace the namespace from which the methods should come from
	 * @param Mixed $function a callable resource that will be fetched when the hook is executed
	 */
	public function attach($namespace, $functions){
		if (is_callable($functions, true, $cn)) {
			$this->_hooks[$namespace][] = array(
								'function' => $functions
							);
			return true;
		}
		return false;
	}

	/**
	 * Abstract function to execute methods from a namespace in the hooks store.
	 *
	 * @access	Public
	 * @param	String $namespace the namespace from which the methods should come from
	 * @param Mixed $param the only parameter supplied from the source method.
	 */
	public function execute(){
		$result = null;
		$args = func_get_args();
		$args_n = func_num_args();
		if ($args_n > 0) {
			if ($args[0] && isset($this->_hooks[$args[0]])) {
				$hooks = $this->_hooks[$args[0]];
				foreach ($hooks as $hook) {
					$result = ($result != null) ? $result: $args[1];
					$result = $this->__execute($hook['function'], $result);
				}
			}
		}
		return $result;
	}

	/**
	 * Execute a method coming from the abstract function
	 *
	 * @access	Protected
	 * @param	String $namespace the namespace from which the methods should come from
	 * @param Mixed $param the only parameter supplied from the source method.
	 */
	protected function __execute($function, $param = null) {
		$result = $param;
		$args = func_get_args();
		$args_n = func_num_args();
		if ($args_n > 0) {
			$result = call_user_func($function, $param);
		}
		return $result;
	}

}

?>