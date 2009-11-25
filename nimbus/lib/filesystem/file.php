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
 * Class for accessing files
 *
 * @category:   		Filesystem
 * @source:		CakePHP file.php
 */
class File extends Filesystem {

	/**
	 * Lock flag for the currently open file
	 *
	 * @access	public
	 */
	public $lock = null;
	
	/**
	 * Name of the currently open file
	 *
	 * @access	public
	 */
	public $name = null;
	
	/**
	 * Path for the currently open file
	 *
	 * @access	public
	 */
	public $path = null;
	
	/**
	 * Information about the currently open file
	 *
	 * @access	protected
	 */
	protected $__info = array();

	/**
	 * Class constructor
	 *
	 * @access	public
	 * @param String $path path to a file to be used
	 * @param Boolean $create create the file if it does not exist
	 * @param Integer $mode permission set for the folder
	 */
	public function __construct($path, $create = false, $mode = 0755) {
		$this->Folder = new Folder(dirname($path), $create, $mode);
		if (!is_dir($path)) {
			$this->name = basename($path);
		}
		$this->wd();
		if (!$this->exists()) {
			if ($create === true) {
				if ($this->create() === false) { //$this->safe($path) && 
					return false;
				}
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Class destructor
	 *
	 * @access	public
	 */
	public function __destruct(){
		$this->close();
	}
	
	/**
	 * Create file
	 *
	 * @access	public
	 */
	public function create(){
		$dir = $this->Folder->wd();
		if (is_dir($dir) && is_writable($dir) && !$this->exists()) {
			$old = umask(0);
			if (touch($this->path)) {
				umask($old);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Open file
	 *
	 * @access	public
	 * @param String $mode the mode how a file should be opened
	 * @param Boolean $force
	 */
	public function open($mode = 'r', $force = false) {
		if (!$force && is_resource($this->_handle)) {
			return true;
		}
		clearstatcache();
		if ($this->exists() === false) {
			if ($this->create() === false) {
				return false;
			}
		}

		$this->_handle = fopen($this->path, $mode);
		if (is_resource($this->_handle)) {
			return true;
		}
		return false;
	}
	
	/**
	 * Close file
	 *
	 * @access	public
	 */
	public function close(){	
		if (!is_resource($this->_handle)) {
			return true;
		}
		return fclose($this->_handle);
	}
	
	/**
	 * Read file
	 *
	 * @access	public
	 * @param Mixed $bytes offset byte from where to start reading
	 * @param String $mode the mode how the file should be read
	 * @param Boolean $force
	 */
	public function read($bytes = false, $mode = 'rb', $force = false) {
		if ($bytes === false && $this->lock === null) {
			return file_get_contents($this->path);
		}
		if ($this->open($mode, $force) === false) {
			return false;
		}
		if ($this->lock !== null && flock($this->_handle, LOCK_SH) === false) {
			return false;
		}
		if (is_int($bytes)) {
			return fread($this->_handle, $bytes);
		}
		$data = '';
		while (!feof($this->_handle)) {
			$data .= fgets($this->_handle, 4096);
		}
		$data = trim($data);
		if ($this->lock !== null) {
			flock($this->_handle, LOCK_UN);
		}
		if ($bytes === false) {
			$this->close();
		}
		return $data;
	}
	
	/**
	 * Write to file
	 *
	 * @access	public
	 * @param String $data data to be written to the file
	 * @param String $mode the mode how the file should be written
	 * @param Boolean $force
	 */
	public function write($data, $mode = 'w', $force = false) {
		$success = false;
		if ($this->open($mode, $force) === true) {
			if ($this->lock !== null) {
				if (flock($this->_handle, LOCK_EX) === false) {
					return false;
				}
			}

			if (fwrite($this->_handle, $data) !== false) {
				$success = true;
			}
			if ($this->lock !== null) {
				flock($this->_handle, LOCK_UN);
			}
		}
		return $success;
	}
	
	/**
	 * Append data to file
	 *
	 * @access	public
	 * @param String $data data to be appended to the file
	 * @param Boolean $force
	 */
	public function append($data, $force = false) {
		return $this->write($data, 'a', $force);
	}
	
	/**
	 * Delete file
	 *
	 * @access	public
	 */
	public function delete(){
		clearstatcache();
		if ($this->exists()) {
			return unlink($this->path);
		}
		return false;
	}
	
	/**
	 * Read file
	 *
	 * @access	public
	 * @return Array information about the file
	 */
	public function info(){
		if ($this->__info == null) {
			$this->__info = pathinfo($this->path);
		}
		if (!isset($this->info['filename'])) {
			$this->__info['filename'] = $this->name();
		}
		return $this->__info;
	}
	
	/**
	 * Get file extension
	 *
	 * @access	public
	 * @return String the file extension
	 */
	public function extension(){
		if ($this->__info == null) {
			$this->__info();
		}
		if (isset($this->__info['extension'])) {
			return $this->__info['extension'];
		}
		return false;
	}
	
	/**
	 * Get file name
	 *
	 * @access	public
	 * @return String the file name
	 */
	public function name(){
		if ($this->__info == null) {
			$this->__info();
		}
		if (isset($this->__info['extension'])) {
			return basename($this->name, '.'.$this->__info['extension']);
		} elseif ($this->name) {
			return $this->name;
		}
		return false;
	}
	
	/**
	 * Get file size
	 *
	 * @access	public
	 * @return String the file size
	 */
	public function size(){
		if ($this->exists()) {
			return filesize($this->path);
		}
		return false;
	}
	
	/**
	 * Get working directory
	 *
	 * @access	public
	 * @return String current working directory
	 */
	public function wd(){
		if (is_null($this->path)) {
			$this->path = $this->Folder->slashTerm($this->Folder->wd()) . $this->name;
		}
		return $this->path;
	}
	
	/**
	 * Get file group
	 *
	 * @access	public
	 * @return String the file group
	 */
	public function group(){
		if ($this->exists()) {
			return filegroup($this->path);
		}
		return false;
	}
	
	/**
	 * Get file owner
	 *
	 * @access	public
	 * @return String the file owner
	 */
	public function owner(){
		if ($this->exists()) {
			return fileowner($this->path);
		}
		return false;
	}
	
	/**
	 * Get file permissions
	 *
	 * @access	public
	 * @return String the file permission
	 */
	public function perms() {
		if ($this->exists()) {
			return substr(sprintf('%o', fileperms($this->path)), -4);
		}
		return false;
	}	
	
	/**
	 * Get file last access time
	 *
	 * @access	public
	 * @return String the file last access time
	 */
	function lastAccess() {
		if ($this->exists()) {
			return fileatime($this->path);
		}
		return false;
	}
	
	/**
	 * Get file last change time
	 *
	 * @access	public
	 * @return String the file last change time
	 */
	function lastChange() {
		if ($this->exists()) {
			return filemtime($this->path);
		}
		return false;
	}
	
	/**
	 * Check if a file is readable
	 *
	 * @access	public
	 */
	public function readable(){
		return is_readable($this->path);
	}
	
	/**
	 * Check if a file is writable
	 *
	 * @access	public
	 */
	public function writable(){
		return is_writable($this->path);
	}
	
	/**
	 * Alias to File::is_writable
	 *
	 * @access	public
	 */
	public function writeable(){
		return is_writable($this->path);
	}
	
	/**
	 * Check if a file is executable
	 *
	 * @access	public
	 */
	public function executable(){
		return is_executable($this->path);
	}
	
	/**
	 * Check if a file exists
	 *
	 * @access	public
	 */
	public function exists(){
		return (file_exists($this->path) && is_file($this->path));
	}
	
}

?>