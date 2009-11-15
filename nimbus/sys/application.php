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
 * The Application superclass
 *
 * @category:   		Application
 */
class Application extends API {

	public function __construct(){
		$this->init();
	}
	
	public function __init(){
		parent::__construct();
	}

	public static function launch($name){
		if (is_array($name)) {
			foreach ($name as $n) {
				Application::launch($n);
			}
		} else {
			$path = APPLICATION_DIR . $name . DS . $name . '.php';
			if (file_exists($path)) {
				require_once $path;
				$app = new $name();
				$app->__init();
				$app->init();
			}
			if (NIMBUS_DEBUG > 0) {
				global $language;
				//Log::write(DEBUG_LOG_FILE, sprintf($language['error_000C'], $path . $name . '.php'));
			}
			return false;
		}
	}

}

?>