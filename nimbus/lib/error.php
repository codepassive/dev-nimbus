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
 * Class for error handling
 *
 * @category:   		Error
 */
class LibError {

	/**
	 * Error handler
	 *
	 * @param Integer $no error type
	 * @param String $str message of the error
	 * @param String $file file where the error occured
	 * @param String $line line where the error occured
	 */
	public static function register($no, $str, $file, $line){
		global $language;
		if (defined('NIMBUS_DEBUG') && NIMBUS_DEBUG === 2) {
			printf($language['error_log'], $str, $file, $line, $no);
		}
		Log::write(ERROR_LOG_FILE, sprintf($language['error_log'], $str, $file, $line, $no));
	}
	
}

/**
 * Custom error handler
 *
 * @param Integer $no error type
 * @param String $str message of the error
 * @param String $file file where the error occured
 * @param String $line line where the error occured
 */
function _error($no, $str, $file, $line) {
	LibError::register($no, $str, $file, $line);
}

?>