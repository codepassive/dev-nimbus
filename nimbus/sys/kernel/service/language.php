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
 * @subpackage:		Nimbus_services
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */
 
/**
 * Load the languages for client API use
 */
global $language;

//Set the proper headers for the Service
header('Content-Type: text/javascript');
header('HTTP/1.1 200 OK');

//Echo out the JSON encoded Array
echo "Nimbus.language = " . json_encode($language);

?>