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
//App Information
define('SYS_NAME', 'Nimbus');
define('SYS_MAJOR_VERSION', '1');
define('SYS_MINOR_VERSION', '0.122b');
define('SYS_BUILD', '3672d49'); //change this
define('SYS_BUILD_NAME', 'Aurora');

//Paths
define('DS', DIRECTORY_SEPARATOR);
define('REALROOT', dirname(__FILE__) . DS);
define('NIMBUS_DIR', REALROOT . 'nimbus' . DS);
define('DATA_DIR', DS . '..' . DS . '.nimbus' . DS);
define('PUBLIC_DIR', REALROOT . 'public' . DS);
define('USER_DIR', DATA_DIR . 'usr' . DS);
define('APPLICATION_DIR', DATA_DIR . 'app' . DS);
define('TEMPORARY_DIR', DATA_DIR . 'tmp' . DS);
define('DB_DIR', DATA_DIR . 'dat' . DS);
define('CACHE_DIR', DATA_DIR . 'cache' . DS);
define('SYSTEM_DIR', NIMBUS_DIR . 'sys' . DS);
define('LIBRARY_DIR', NIMBUS_DIR . 'lib' . DS);
define('MODULE_DIR', NIMBUS_DIR . 'mod' . DS);
define('RESOURCE_DIR', PUBLIC_DIR . 'resources' . DS);
define('LANGUAGE_DIR', RESOURCE_DIR . 'languages' . DS);
define('SKIN_DIR', RESOURCE_DIR . 'skins' . DS);
define('IMAGES_DIR', RESOURCE_DIR . 'images' . DS);
define('MEDIA_DIR', RESOURCE_DIR . 'media' . DS);
define('SCRIPT_DIR', RESOURCE_DIR . 'scripts' . DS);

//Information
define('NIMBUS_DEBUG', 2); //1 for normal debug, 2 for whole dump debug, 0 for production
define('NIMBUS_UPDATE_URL', 'http://synapse.nimbusdesktop.org/');
define('DEFAULT_LANGUAGE', 'en-us');
define('DEFAULT_ENCODING', 'utf-8');

//Include the Application constants
include NIMBUS_DIR . 'constant.php';

?>