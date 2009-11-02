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

	public $lock = null;
	public $name = null;
	public $path = null;
	
	protected $__info = array();

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
	
	public function __destruct(){
		$this->close();
	}
	
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
	
	public function close(){	
		if (!is_resource($this->handle)) {
			return true;
		}
		return fclose($this->handle);
	}
	
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
	
	public function append($data, $force = false) {
		return $this->write($data, 'a', $force);
	}
	
	public function delete(){
		clearstatcache();
		if ($this->exists()) {
			return unlink($this->path);
		}
		return false;
	}
	
	public function info(){
		if ($this->__info == null) {
			$this->__info = pathinfo($this->path);
		}
		if (!isset($this->info['filename'])) {
			$this->__info['filename'] = $this->name();
		}
		return $this->__info;
	}
	
	public function extension(){
		if ($this->__info == null) {
			$this->__info();
		}
		if (isset($this->__info['extension'])) {
			return $this->__info['extension'];
		}
		return false;
	}
	
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
	
	public function size(){
		if ($this->exists()) {
			return filesize($this->path);
		}
		return false;
	}
	
	public function wd(){
		if (is_null($this->path)) {
			$this->path = $this->Folder->slashTerm($this->Folder->wd()) . $this->name;
		}
		return $this->path;
	}
	
	public function group(){
		if ($this->exists()) {
			return filegroup($this->path);
		}
		return false;
	}
	
	public function owner(){
		if ($this->exists()) {
			return fileowner($this->path);
		}
		return false;
	}
	
	public function perms() {
		if ($this->exists()) {
			return substr(sprintf('%o', fileperms($this->path)), -4);
		}
		return false;
	}	
	
	function lastAccess() {
		if ($this->exists()) {
			return fileatime($this->path);
		}
		return false;
	}
	
	function lastChange() {
		if ($this->exists()) {
			return filemtime($this->path);
		}
		return false;
	}
	
	public function readable(){
		return is_readable($this->path);
	}
	
	public function writable(){
		return is_writable($this->path);
	}
	
	public function writeable(){
		return is_writable($this->path);
	}
	
	public function executable(){
		return is_executable($this->path);
	}
	
	public function exists(){
		return (file_exists($this->path) && is_file($this->path));
	}
	
}

?>