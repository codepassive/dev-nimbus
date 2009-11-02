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
 * Include the exception dependencies
 */
include 'exception' . DS . 'interface.php';
include 'exception' . DS . 'abstract.php';
 
/**
 * Class for exception handling
 *
 * @category:   		Exception
 * @source:		http://www.php.net/manual/en/language.exceptions.php#91159
 */
class NimbusException extends ExceptionAbstract implements ExceptionInterface {}

/**
 * Custom exception handler
 *
 * @param Object $e catched exception
 * @return String exception message
 */
function _exception($e) {
	echo $e->__toString();
}

?>