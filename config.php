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

//Paths
define('DS', DIRECTORY_SEPARATOR);
define('REALROOT', dirname(__FILE__) . DS);
define('DATAROOT', DS . '..' . DS . 'zMKsl29HstPbvo4UaAm2qJ89' . DS);
define('NIMBUS_DIR', REALROOT . 'nimbus' . DS);
define('PUBLIC_DIR', REALROOT . 'public' . DS);
define('USER_DIR', DATAROOT . 'usr' . DS);
define('APPLICATION_DIR', NIMBUS_DIR . 'app' . DS);
define('TEMPORARY_DIR', NIMBUS_DIR . 'tmp' . DS);
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
define('NIMBUS_UPDATE_URL', 'http://synapse.nimbusinitiative.org/');

//Include the Application constants
include NIMBUS_DIR . 'constant.php';
?>