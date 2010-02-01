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
 * @subpackage:		Nimbus_system
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */
/**
 * @ignore
 *   This file contains functions and definitions used throughout the Nimbus
 *   system. Please do not edit this file to prevent System breakdown.
 */
/**
 * Function to check compatibility for the environment
 */
function checkCompatibility(){
	global $language;
	$incompatibilities = array();
	$missing = array();

	/**
	 * Check PHP environment
	 */
	if (!version_compare(PHP_VERSION, '5.2.5', '>=')) {
		$incompatibilities[] = array($language['error_000A'], '000A');
	}
	if (!extension_loaded('sqlite') && !extension_loaded('pdo_sqlite')) {
		$incompatibilities[] = array($language['error_001A'], '001A');
	}
	if ((!is_writable('/../.nimbus') && !is_readable('/../.nimbus')) && (!is_writable('/../.nimbus') && !is_readable('/../.nimbus'))) {
		$incompatibilities[] = array($language['error_002A'], '002A');
	}
	if (!ini_get('file_uploads')) {
		$incompatibilities[] = array($language['error_003A'], '003A');
	}

	/**
	 * Check for Optional Extensions
	 */
	if (!extension_loaded('mbstring')) {
		$missing[] = array($language['error_000B'], '000B');
	}
	if (!extension_loaded('ftp')) {
		$missing[] = array($language['error_001B'], '001B');
	}
	if (!extension_loaded('json')) {
		$missing[] = array($language['error_002B'], '002B');
	}
	if (!extension_loaded('soap')) {
		$missing[] = array($language['error_003B'], '003B');
	}
	if (!extension_loaded('xml') && !extension_loaded('xmlreader') && !extension_loaded('xmlwriter')) {
		$missing[] = array($language['error_004B'], '004B');
	}
	if (!extension_loaded('xmlrpc')) {
		$missing[] = array($language['error_005B'], '005B');
	}
	if (!extension_loaded('zip')) {
		$missing[] = array($language['error_006B'], '006B');
	}
	if (!extension_loaded('zlib')) {
		$missing[] = array($language['error_007B'], '007B');
	}
	if (!function_exists('mail')) {
		$missing[] = array($language['error_008B'], '008B');
	}
	if (!extension_loaded('curl')) {
		$missing[] = array($language['error_009B'], '009B');
	}

	/**
	 * End the application when an incompatibility has been determined
	 */
	if (!empty($incompatibilities)) {
		include SYSTEM_DIR . 'shell' . DS . 'view' . DS . 'compatibility.html';
		exit;
	}
}

/**
 * Deserialize serialized values from an array for use
 */
function deserialize($arr){
	return array_map('unserialize', $arr);
}

/**
 * Function to implement a basic inflector
 *
 * @poram String $key the associative key for the language array
 */
function __($name, $replace = null, $echo = true) {
	global $language;
	if (!is_array($replace)) {
		$replace = array($replace);
	}
	if (strstr($name, "/")) {
		$tree = explode("/", $name);
		$language[$name] = $language[$tree[0]][$tree[1]];
	}
	if (isset($language[$name])) {
		if (!empty($replace)) {
			$lang = call_user_func_array('sprintf', array_merge(array($language[$name]), $replace));
		} else {
			$lang = $language[$name];
		}
		if ($echo === true) {
			echo $lang;
		} else {
			return $lang;
		}
	}
}

/**
 * Check if a URL is really a URL
 *
 * @poram String $url the url to be parsed
 */
function is_url($url){
	$parts = parse_url($url);
	if (!isset($parts) || !isset($parts['host']) || !empty($parts) || !empty($parts['host'])) {
		return false;
	}
	return true;
}

/**
 * Generate a hashed string from an input
 *
 * @poram String $input the string to be hashed
 */
function generateHash($input = null){
	$input = ($input == null) ? microtime(): $input;
	return substr(sha1(md5($input)), 0, 9);
}

/**
 * Generate a password hash from a string
 *
 * @poram String $password the password to be hashed
 */
function generatePassword($password){
	return sha1(md5($password . config('salt')));
}

/**
 * Get the request values
 */
function request($name){
	$user = Cloud::getInstance();
	if (isset($user->request->items[$name]) && empty($user->request->items[$name])) {
		return true;
	} else if(isset($user->request->items[$name]) && !empty($user->request->items[$name])) {
		return $user->request->items[$name];
	}
	return false;
}

function personal($name){
	$personal = Session::get('user-information');
	if (isset($personal->$name)) {
		return $personal->$name;
	}
	return false;
}

/**
 * Get the configuration Value of the specified name
 */
function config($name){
	$user = Cloud::getInstance();
	return (isset($user->config->$name)) ? $user->config->$name: false;
}


/**
 * ------------------------------------------------------------
 *  This Article is written by S Pradeep for Programming Forum
 *                 http://www.go4expert.com/
 * ------------------------------------------------------------
 */
function getDirectorySize($path){
  $totalsize = 0;
  $totalcount = 0;
  $dircount = 0;
  if ($handle = opendir ($path)) {
    while (false !== ($file = readdir($handle))) {
      $nextpath = $path . '/' . $file;
      if ($file != '.' && $file != '..' && !is_link ($nextpath)) {
        if (is_dir ($nextpath)) {
          $dircount++;
          $result = getDirectorySize($nextpath);
          $totalsize += $result['size'];
          $totalcount += $result['count'];
          $dircount += $result['dircount'];
        } elseif (is_file ($nextpath)) {
          $totalsize += filesize ($nextpath);
          $totalcount++;
        }
      }
    }
  }
  closedir ($handle);
  $total['size'] = $totalsize;
  $total['count'] = $totalcount;
  $total['dircount'] = $dircount;
  return $total;
}

function sizeFormat($size){
    if ($size<1024) {
        return $size." bytes";
    } else if ($size<(1024*1024)) {
        $size=round($size/1024,1);
        return $size." KB";
    } else if($size<(1024*1024*1024)) {
        $size=round($size/(1024*1024),1);
        return $size." MB";
    } else {
        $size=round($size/(1024*1024*1024),1);
        return $size." GB";
    }
}

?>