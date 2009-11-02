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

//Compatibility checks
require_once SYSTEM_DIR . 'common.php';
checkCompatibility();

//Set maximum execution time
set_time_limit(0);

//Include common files
require_once 'common.php';
require_once 'loader.php';

//Load base libraries
Loader::library(array('filesystem', 'log', 'error', 'exception', 'registry'));

//Set PHP error and exception handler
set_error_handler('_error');
set_exception_handler('_exception');

//Load the base kernel libraries
Loader::kernel(array('cloud', 'nimbus'));

//Include function aliases
require_once 'alias.php';

?>