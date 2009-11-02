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
 * Class for logging errors or debug traces
 *
 * @category:   		Log
 */
class Log {
	
	/**
	 * Write log to a file
	 *
	 * @access	public
	 * @param String $file a Constant or a path to a file to be used
	 * @param String $message the message to be written
	 */
	public static function write($file, $message = null){
		$file = TEMPORARY_DIR . $file;
		$fh = fopen($file, 'a');
		if ($fh) {
			$now = date("F j, Y H:i:s a");
			$message = "[$now] $message\n";
			fwrite($fh, $message);
			fclose($fh);
		} else {
			throw new NimbusException("File $file could not be opened or created.");
		}
	}
	
}

?>