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
 * Include the filesystem dependencies
 */
include 'filesystem' . DS . 'folder.php';
include 'filesystem' . DS . 'file.php';
 
/**
 * Class for accessing files and folders
 *
 * @category:   		Filesystem
 * @source:		CakePHP
 */
class Filesystem {

	/**
	 * Currently open folder handle
	 *
	 * @access	public
	 */
	public $Folder = null;
	
	
	/**
	 * Currently being used file handle
	 *
	 * @access	protected
	 */
	protected $_handle = null;
	
}

?>