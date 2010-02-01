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

	/**
	 * Lock flag for the currently open file
	 *
	 * @access	public
	 */
	public $path = null;

	/**
	 * Lock flag for the currently open file
	 *
	 * @access	public
	 */
    public $sort = false;

	/**
	 * Lock flag for the currently open file
	 *
	 * @access	public
	 */
    public $mode = 0755;
	
	/**
	 * Lock flag for the currently open file
	 *
	 * @access	public
	 */
	private $_messages = array();

	/**
	 * Lock flag for the currently open file
	 *
	 * @access	public
	 */
	private $_errors = false;

	/**
	 * Lock flag for the currently open file
	 *
	 * @access	public
	 */
	private $_directories = array();

	/**
	 * Lock flag for the currently open file
	 *
	 * @access	public
	 */	
	private $_files = array();
	
	/**
	 * Class constructor
	 *
	 * @access	public
	 * @param Mixed $path path to the directory to be used
	 * @param Boolean $create create the file if it does not exist
	 * @param Mixed $mode permission set for the folder
	 */
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
	
	/**
	 * Get current working directory
	 *
	 * @access	public
	 */
	public function wd() {
		return $this->path;
	}
	
	/**
	 * Set current working directory
	 *
	 * @access	public
	 * @param String $path path to the directory to be used
	 */
	public function cd($path) {
		$path = $this->realpath($path);
		if (is_dir($path)) {
			return $this->path = $path;
		}
		return false;
	}

	/**
	 * Read folder
	 *
	 * @access	public
	 * @param Boolean $sort flag to sort files
	 * @param Mixed $exceptions an array of files to be ignored while reading
	 * @param Boolean $fullPath
	 * @return Array list of files and folders under the current folder
	 */
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

	/**
	 * Find a file or directory through a regex pattern
	 *
	 * @access	public
	 * @param String $regexpPattern the pattern used for the search
	 * @param Boolean $sort flag to sort the results
	 */
	public function find($regexpPattern = '.*', $sort = false) {
		list($dirs, $files) = $this->read($sort);
		return array_values(preg_grep('/^' . $regexpPattern . '$/i', $files)); ;
	}

	/**
	 * Map a folder and fetch everything under it
	 *
	 * @access	public
	 * @param String $source_dir the directory to be used for the search
	 * @param Boolean $top_level_only ignore everything that isn't on the top level of the source directory
	 */
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
	
	public function generate($allow = array()){
		if (!empty($allow) && !is_array($allow)) {
			$allow = explode(",", $allow);
		}
		$this->allow = $allow;
		return $this->generateList($this->path);
	}
	
	public function generateList($path, $level = 0){
		$ignore = array('cgi-bin', '.', '..');
		$dh = @opendir($path);
		$f = array();
		while( false !== ( $file = readdir( $dh ) ) ){
			if( !in_array( $file, $ignore ) ){
				if (is_dir($path . DS . $file)) {;
					$d = getDirectorySize($path . DS . $file);
					$y = explode(str_replace('\\..\\', '', USER_DIR), $path . DS . $file);
					$f[] = array(
								'name' => $file,
								'type' => 'dir',
								'size' => $d['size'],
								'path' => $y[1],
								'sub' => $this->generateList($path . DS . $file, ($level+1))
							);
				} else {
					$info = pathinfo($path . DS . $file);
					$mimetype = 'unknown';
					$ext = 'unknown';
					if (isset($info['extension'])) {
						require_once SYSTEM_DIR . 'shell' . DS . 'mimes.php';
						$ext = strtolower($info['extension']);
						if (isset($mimes)) {
							if (array_key_exists($ext, $mimes)) {
								if (is_array($mimes[$ext])) {
									$mimetype = $mimes[$ext][0];
								} else {
									$mimetype = $mimes[$ext];
								}
							}
						}
					}
					if (empty($this->allow) || in_array($info['extension'], $this->allow)) {
						$y = explode(str_replace('\\..\\', '', USER_DIR), $path . DS . $file);
						$f[] = array(
								'name' => $file,
								'type' => $ext,
								'size' => sprintf("%u", filesize($path . DS . $file)),
								'path' => $y[1],
							);
					}
				}
			}
		}
		closedir( $dh );
		return $f; 
	}

	/**
	 * Abstraction to the Folder::__recursive method
	 *
	 * @access	public
	 * @param String $pattern regex pattern used to find items in a directory
	 * @param Boolean $sort flag to sort the results
	 */
	public function recursive($pattern = '.*', $sort = false) {
		$startsOn = $this->path;
		$out = $this->__recursive($pattern, $sort);
		$this->cd($startsOn);
		return $out;
	}	

	/**
	 * Recurse through a directory and fetch an Array result
	 *
	 * @access	protected
	 * @param String $pattern regex pattern used to find items in a directory
	 * @param Boolean $sort flag to sort the results
	 */
	protected function __recursive($pattern, $sort = false) {
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

	/**
	 * Check if a path is of a windows(OS) structure
	 *
	 * @access	public
	 * @param String $path path to the directory
	 */	
	public function isWindowsPath($path) {
		if (preg_match('/^[A-Z]:\\\\/i', $path)) {
			return true;
		}
		return false;
	}

	/**
	 * Check if a path is absolute
	 *
	 * @access	public
	 * @param String $path path to the directory
	 */	
	public function isAbsolute($path) {
		$match = preg_match('/^\\//', $path) || preg_match('/^[A-Z]:\\\\/i', $path);
		return $match;
	}

	/**
	 * Abstraction to the Folder::correctSlashFor method
	 *
	 * @access	public
	 * @param String $path path to the directory
	 */	
	public function normalizePath($path) {
		return Folder::correctSlashFor($path);
	}

	/**
	 * Correct a Backslash
	 *
	 * @access	public
	 * @param String $path path to the directory
	 */	
	public function correctSlashFor($path) {
		if (Folder::isWindowsPath($path)) {
			return '\\';
		}
		return '/';
	}

	/**
	 * Add a succeeding slash to a path
	 *
	 * @access	public
	 * @param String $path path to the directory
	 */	
	public function slashTerm($path) {
		if (Folder::isSlashTerm($path)) {
			return $path;
		}
		return $path . Folder::correctSlashFor($path);
	}

	/**
	 * Append an element to a path
	 *
	 * @access	public
	 * @param String $path path to the directory
	 */	
	public function addPathElement($path, $element) {
		return Folder::slashTerm($path) . $element;
	}

	/**
	 * Check if the current working directory is on the queried path
	 *
	 * @access	public
	 * @param String $path path to the directory
	 * @param Boolean $reverse flag to reverse the directory and the current working one
	 */	
	public function inPath($path = '', $reverse = false) {
		$dir = Folder::slashTerm($path);
		$current = Folder::slashTerm($this->wd());

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

	/**
	 * Chmod a file or a folder
	 *
	 * @access	public
	 * @param String $path path to the directory
	 * @param Boolean $mode the permission
	 * @param Boolean $recursive flag to recurse through the directory
	 * @param Array $exceptions files or directories to ignore
	 */
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

	/**
	 * Create a tree from a path
	 *
	 * @access	public
	 * @param String $path path to the directory
	 * @param Mixed $exceptions files or directories to ignore
	 * @param Boolean $type join the directories and/or files
	 */	
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
	

	/**
	 * Merge the directories and files
	 *
	 * @access	public
	 * @param String $path path to the directory
	 * @param Array $exceptions files or directories to ignore
	 */	
	private function __tree($path, $exceptions) {
		if ($this->cd($path)) {
			list($dirs, $files) = $this->read(false, $exceptions, true);
			$this->_directories = array_merge($this->_directories, $dirs);
			$this->_files = array_merge($this->_files, $files);
		}
	}
	
	/**
	 * Create a folder
	 *
	 * @access	public
	 * @param String $pathname path to the directory
	 * @param Mixed $mode the permission
	 */	
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
	

	/**
	 * Get directory size
	 *
	 * @access	public
	 * @return Integer size of the directory
	 */	
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
	

	/**
	 * Delete a folder
	 *
	 * @access	public
	 * @param String $path path to the directory
	 */	
	public function delete($path = null) {
		if (!$path) {
			$path = $this->wd();
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

	/**
	 * Copy a file or folder
	 *
	 * @access	public
	 * @param Array $options directives for the copy procedure
	 */	
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

	/**
	 * Move a file or folder
	 *
	 * @access	public
	 * @param Array $options directives for the move procedure
	 */	
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
	
	/**
	 * Get the messages from the last directory operation
	 *
	 * @access	public
	 * @return String messages for the last directory operation
	 */	
	public function messages() {
		return $this->_messages;
	}
	
	/**
	 * Get the errors from the last directory operation
	 *
	 * @access	public
	 * @return String errors for the last directory operation
	 */	
	public function errors() {
		return $this->_errors;
	}
	
	/**
	 * Unix flavor command to read a folder
	 *
	 * @access	public
	 * @param Boolean $sort flag to sort files
	 * @param Mixed $exceptions an array of files to be ignored while reading
	 */	
	public function ls($sort = true, $exceptions = false) {
		return $this->read($sort, $exceptions);
	}
	
	/**
	 * Unix flavor command to create a folder
	 *
	 * @access	public
	 * @param String $pathname path to the directory
	 * @param Mixed $mode the permission
	 */	
	public function mkdir($pathname, $mode = 0755) {
		return $this->create($pathname, $mode);
	}
	
	/**
	 * Unix flavor command to copy a folder
	 *
	 * @access	public
	 * @param Array $options directives for the copy procedure
	 */	
	public function cp($options) {
		return $this->copy($options);
	}

	/**
	 * Unix flavor command to move a folder
	 *
	 * @access	public
	 * @param Array $options directives for the move procedure
	 */	
	public function mv($options) {
		return $this->move($options);
	}

	/**
	 * Unix flavor command to delete a folder
	 *
	 * @access	public
	 * @param String $path path to the directory
	 */	
	public function rm($path) {
		return $this->delete($path);
	}

	/**
	 * Get the realpath of the path supplied
	 *
	 * @access	public
	 * @param String $path path to the directory
	 */	
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

	/**
	 * Correct the path to append a slash
	 *
	 * @access	public
	 * @param String $path path to the directory
	 */	
	public function isSlashTerm($path) {
		$lastChar = $path[strlen($path) - 1];
		return $lastChar === '/' || $lastChar === '\\';
	}

}

?>