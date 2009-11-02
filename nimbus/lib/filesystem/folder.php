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
 * Class for accessing folders
 *
 * @category:   		Filesystem
 * @source:		CakePHP folder.php
 */
class Folder extends Filesystem {

	public $path = null;
    public $sort = false;
    public $mode = 0755;
	
	private $_messages = array();
	private $_errors = false;
	private $_directories = array();	
	private $_files = array();

	public function __construct($path = false, $create = false, $mode = false) {
		if (empty($path)) {
			$path = '../';
		}
		if ($mode) {
			$this->mode = $mode;
		}
		if (!file_exists($path) && $create === true) {
			$this->create($path, $this->mode);
		}
		if (!Folder::isAbsolute($path)) {
			$path = realpath($path);
		}
		if (!empty($path)) {
			$this->cd($path);
		}
	}
	
	public function wd() {
		return $this->path;
	}
	
	public function cd($path) {
		$path = $this->realpath($path);
		if (is_dir($path)) {
			return $this->path = $path;
		}
		return false;
	}	

	public function read($sort = true, $exceptions = false, $fullPath = false) {
		$dirs = $files = array();
		if (is_array($exceptions)) {
			$exceptions = array_flip($exceptions);
		}
		$skipHidden = isset($exceptions['.']) || $exceptions === true;
		if (false === ($dir = @opendir($this->path))) {
			return array($dirs, $files);
		}
		while (false !== ($item = readdir($dir))) {
			if ($item === '.' || $item === '..' || ($skipHidden && $item[0] === '.') || isset($exceptions[$item])) {
				continue;
			}
			$path = Folder::addPathElement($this->path, $item);
			if (is_dir($path)) {
				$dirs[] = $fullPath ? $path : $item;
			} else {
				$files[] = $fullPath ? $path : $item;
			}
		}
		if ($sort || $this->sort) {
			sort($dirs);
			sort($files);
		}
		closedir($dir);
		return array($dirs, $files);
	}
	
	public function find($regexpPattern = '.*', $sort = false) {
		list($dirs, $files) = $this->read($sort);
		return array_values(preg_grep('/^' . $regexpPattern . '$/i', $files)); ;
	}
	
	public function map($source_dir = null, $top_level_only = false){
		$source_dir = ($source_dir == null) ? $this->path: $source_dir;
		if ($fp = @opendir($source_dir)) {
			$source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;		
			$filedata = array();
			while (false !== ($file = readdir($fp))) {
				if (strncmp($file, '.', 1) == 0) {
					continue;
				}				
				if ($top_level_only == false && @is_dir($source_dir . $file))	{
					$temp_array = array();				
					$temp_array = $this->map($source_dir . $file . DIRECTORY_SEPARATOR);				
					$filedata[$file] = $temp_array;
				} else {
					$filedata[] = $file;
				}
			}			
			closedir($fp);
			return $filedata;
		}
	}
	
	public function recursive($pattern = '.*', $sort = false) {
		$startsOn = $this->path;
		$out = $this->__recursive($pattern, $sort);
		$this->cd($startsOn);
		return $out;
	}	
	
	private function __recursive($pattern, $sort = false) {
		list($dirs, $files) = $this->read($sort);
		$found = array();

		foreach ($files as $file) {
			if (preg_match('/^' . $pattern . '$/i', $file)) {
				$found[] = Folder::addPathElement($this->path, $file);
			}
		}
		$start = $this->path;

		foreach ($dirs as $dir) {
			$this->cd(Folder::addPathElement($start, $dir));
			$found = array_merge($found, $this->recursive($pattern, $sort));
		}
		return $found;
	}
	
	public function isWindowsPath($path) {
		if (preg_match('/^[A-Z]:\\\\/i', $path)) {
			return true;
		}
		return false;
	}
	
	public function isAbsolute($path) {
		$match = preg_match('/^\\//', $path) || preg_match('/^[A-Z]:\\\\/i', $path);
		return $match;
	}
	
	public function normalizePath($path) {
		return Folder::correctSlashFor($path);
	}
	
	public function correctSlashFor($path) {
		if (Folder::isWindowsPath($path)) {
			return '\\';
		}
		return '/';
	}
	
	public function slashTerm($path) {
		if (Folder::isSlashTerm($path)) {
			return $path;
		}
		return $path . Folder::correctSlashFor($path);
	}
	
	public function addPathElement($path, $element) {
		return Folder::slashTerm($path) . $element;
	}
	
	public function inPath($path = '', $reverse = false) {
		$dir = Folder::slashTerm($path);
		$current = Folder::slashTerm($this->pwd());

		if (!$reverse) {
			$return = preg_match('/^(.*)' . preg_quote($dir, '/') . '(.*)/', $current);
		} else {
			$return = preg_match('/^(.*)' . preg_quote($current, '/') . '(.*)/', $dir);
		}
		if ($return == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	public function chmod($path, $mode = false, $recursive = true, $exceptions = array()) {
		if (!$mode) {
			$mode = $this->mode;
		}
		if ($recursive === false && is_dir($path)) {
			if (@chmod($path, intval($mode, 8))) {
				$this->_messages[] = sprintf(__('%s changed to %s', true), $path, $mode);
				return true;
			}

			$this->_errors[] = sprintf(__('%s NOT changed to %s', true), $path, $mode);
			return false;
		}
		if (is_dir($path)) {
			$paths = $this->tree($path);
			foreach ($paths as $type) {
				foreach ($type as $key => $fullpath) {
					$check = explode(DS, $fullpath);
					$count = count($check);
					if (in_array($check[$count - 1], $exceptions)) {
						continue;
					}
					if (@chmod($fullpath, intval($mode, 8))) {
						$this->_messages[] = sprintf(__('%s changed to %s', true), $fullpath, $mode);
					} else {
						$this->_errors[] = sprintf(__('%s NOT changed to %s', true), $fullpath, $mode);
					}
				}
			}
			if (empty($this->_errors)) {
				return true;
			}
		}
		return false;
	}
	
	public function tree($path, $exceptions = true, $type = null) {
		$original = $this->path;
		$path = rtrim($path, DS);
		$this->_files = array();
		$this->_directories = array($path);
		$directories = array();

		if ($exceptions === false) {
			$exceptions = true;
		}
		while (count($this->_directories)) {
			$dir = array_pop($this->_directories);
			$this->__tree($dir, $exceptions);
			$directories[] = $dir;
		}

		if ($type === null) {
			return array($directories, $this->_files);
		}
		if ($type === 'dir') {
			return $directories;
		}
		$this->cd($original);

		return $this->_files;
	}
	
	private function __tree($path, $exceptions) {
		if ($this->cd($path)) {
			list($dirs, $files) = $this->read(false, $exceptions, true);
			$this->_directories = array_merge($this->_directories, $dirs);
			$this->_files = array_merge($this->_files, $files);
		}
	}
	
	public function create($pathname, $mode = false) {
		if (is_dir($pathname) || empty($pathname)) {
			return true;
		}

		if (!$mode) {
			$mode = $this->mode;
		}

		if (is_file($pathname)) {
			$this->_errors[] = sprintf(__('%s is a file', true), $pathname);
			return false;
		}
		$nextPathname = substr($pathname, 0, strrpos($pathname, DS));

		if ($this->create($nextPathname, $mode)) {
			if (!file_exists($pathname)) {
				$old = umask(0);
				if (mkdir($pathname, $mode)) {
					umask($old);
					$this->_messages[] = sprintf(__('%s created', true), $pathname);
					return true;
				} else {
					umask($old);
					$this->_errors[] = sprintf(__('%s NOT created', true), $pathname);
					return false;
				}
			}
		}
		return true;
	}
	
	public function dirsize() {
		$size = 0;
		$directory = Folder::slashTerm($this->path);
		$stack = array($directory);
		$count = count($stack);
		for ($i = 0, $j = $count; $i < $j; ++$i) {
			if (is_file($stack[$i])) {
				$size += filesize($stack[$i]);
			} elseif (is_dir($stack[$i])) {
				$dir = dir($stack[$i]);
				if ($dir) {
					while (false !== ($entry = $dir->read())) {
						if ($entry === '.' || $entry === '..') {
							continue;
						}
						$add = $stack[$i] . $entry;

						if (is_dir($stack[$i] . $entry)) {
							$add = Folder::slashTerm($add);
						}
						$stack[] = $add;
					}
					$dir->close();
				}
			}
			$j = count($stack);
		}
		return $size;
	}
	
	public function delete($path = null) {
		if (!$path) {
			$path = $this->pwd();
		}
		$path = Folder::slashTerm($path);
		if (is_dir($path) === true) {
			$normalFiles = glob($path . '*');
			$hiddenFiles = glob($path . '\.?*');

			$normalFiles = $normalFiles ? $normalFiles : array();
			$hiddenFiles = $hiddenFiles ? $hiddenFiles : array();

			$files = array_merge($normalFiles, $hiddenFiles);
			if (is_array($files)) {
				foreach ($files as $file) {
					if (preg_match('/(\.|\.\.)$/', $file)) {
						continue;
					}
					if (is_file($file) === true) {
						if (@unlink($file)) {
							$this->_messages[] = sprintf(__('%s removed', true), $file);
						} else {
							$this->_errors[] = sprintf(__('%s NOT removed', true), $file);
						}
					} elseif (is_dir($file) === true && $this->delete($file) === false) {
						return false;
					}
				}
			}
			$path = substr($path, 0, strlen($path) - 1);
			if (rmdir($path) === false) {
				$this->_errors[] = sprintf(__('%s NOT removed', true), $path);
				return false;
			} else {
				$this->_messages[] = sprintf(__('%s removed', true), $path);
			}
		}
		return true;
	}
	
	public function copy($options = array()) {
		$to = null;
		if (is_string($options)) {
			$to = $options;
			$options = array();
		}
		$options = array_merge(array('to' => $to, 'from' => $this->path, 'mode' => $this->mode, 'skip' => array()), $options);

		$fromDir = $options['from'];
		$toDir = $options['to'];
		$mode = $options['mode'];

		if (!$this->cd($fromDir)) {
			$this->_errors[] = sprintf(__('%s not found', true), $fromDir);
			return false;
		}

		if (!is_dir($toDir)) {
			$this->mkdir($toDir, $mode);
		}

		if (!is_writable($toDir)) {
			$this->_errors[] = sprintf(__('%s not writable', true), $toDir);
			return false;
		}

		$exceptions = array_merge(array('.', '..', '.svn'), $options['skip']);
		if ($handle = @opendir($fromDir)) {
			while (false !== ($item = readdir($handle))) {
				if (!in_array($item, $exceptions)) {
					$from = Folder::addPathElement($fromDir, $item);
					$to = Folder::addPathElement($toDir, $item);
					if (is_file($from)) {
						if (copy($from, $to)) {
							chmod($to, intval($mode, 8));
							touch($to, filemtime($from));
							$this->_messages[] = sprintf(__('%s copied to %s', true), $from, $to);
						} else {
							$this->_errors[] = sprintf(__('%s NOT copied to %s', true), $from, $to);
						}
					}

					if (is_dir($from) && !file_exists($to)) {
						$old = umask(0);
						if (mkdir($to, $mode)) {
							umask($old);
							$old = umask(0);
							chmod($to, $mode);
							umask($old);
							$this->_messages[] = sprintf(__('%s created', true), $to);
							$options = array_merge($options, array('to'=> $to, 'from'=> $from));
							$this->copy($options);
						} else {
							$this->_errors[] = sprintf(__('%s not created', true), $to);
						}
					}
				}
			}
			closedir($handle);
		} else {
			return false;
		}

		if (!empty($this->_errors)) {
			return false;
		}
		return true;
	}
	
	public function move($options) {
		$to = null;
		if (is_string($options)) {
			$to = $options;
			$options = (array)$options;
		}
		$options = array_merge(array('to' => $to, 'from' => $this->path, 'mode' => $this->mode, 'skip' => array()), $options);

		if ($this->copy($options)) {
			if ($this->delete($options['from'])) {
				return $this->cd($options['to']);
			}
		}
		return false;
	}
	
	public function messages() {
		return $this->_messages;
	}
	
	public function errors() {
		return $this->_errors;
	}
	
	public function ls($sort = true, $exceptions = false) {
		return $this->read($sort, $exceptions);
	}
	
	public function mkdir($pathname, $mode = 0755) {
		return $this->create($pathname, $mode);
	}
	
	public function cp($options) {
		return $this->copy($options);
	}
	
	public function mv($options) {
		return $this->move($options);
	}
	
	public function rm($path) {
		return $this->delete($path);
	}
	
	function realpath($path) {
		$path = str_replace('/', DS, trim($path));
		if (strpos($path, '..') === false) {
			if (!Folder::isAbsolute($path)) {
				$path = Folder::addPathElement($this->path, $path);
			}
			return $path;
		}
		$parts = explode(DS, $path);
		$newparts = array();
		$newpath = '';
		if ($path[0] === DS) {
			$newpath = DS;
		}

		while (($part = array_shift($parts)) !== NULL) {
			if ($part === '.' || $part === '') {
				continue;
			}
			if ($part === '..') {
				if (count($newparts) > 0) {
					array_pop($newparts);
					continue;
				} else {
					return false;
				}
			}
			$newparts[] = $part;
		}
		$newpath .= implode(DS, $newparts);
		return Folder::slashTerm($newpath);
	}
	
	public function isSlashTerm($path) {
		$lastChar = $path[strlen($path) - 1];
		return $lastChar === '/' || $lastChar === '\\';
	}

}

?>