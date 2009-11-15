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
 * Class for the Security Layer
 *
 * @category:   		Security
 * @source:		http://www.namepros.com/code/515465-php-security-class.html
 */
class Security {

	/**
	 * The php configuration value for the magic_quotes_gpc
	 *
	 * @access	protected
	 */
    protected $magic_quotes_gpc = false;

	/**
	 * Class constructor
	 *
	 * @access	public
	 */
    public function __construct(){		
		if (get_magic_quotes_runtime()) {
			set_magic_quotes_runtime(0);
		}
		
		if (get_magic_quotes_gpc()) {
			$this->magic_quotes_gpc = true;
		}		
		if (ini_get('register_globals')) {
			if (isset($_REQUEST['GLOBALS'])) {
				exit('Illegal attack on global variable.');
			}
			$_REQUEST = array();
			$preserve = array('GLOBALS', '_REQUEST', '_GET', '_POST', '_FILES', '_COOKIE', '_SERVER', '_ENV', '_SESSION');
			foreach ($GLOBALS as $key => $value) {
				if (!in_array($key, $preserve)) {
					global $$key;
					$$key = null;                        
					unset($GLOBALS[$key], $$key);
				}
			}
		}		
		if (is_array($_POST)) {
			foreach ($_POST as $key => $value) {
				$_POST[$this->clean_input_keys($key)] = $this->clean_input_data($value);
			}
		} else {
			$_POST = array();
		}		
		if (is_array($_GET)) {
			foreach ($_GET as $key => $value) {
				$_GET[$this->clean_input_keys($key)] = $this->clean_input_data($value);
			}
		} else {
			$_GET = array();
		}		
		if (is_array($_COOKIE)) {
			foreach ($_COOKIE as $key => $value) {
				$_COOKIE[$this->clean_input_keys($key)] = $this->clean_input_data($value);
			}
		} else {
			$_COOKIE = array();
		}
		$_REQUEST = array_merge($_GET, $_POST);
    }

	/**
	 * Cleans data to prevent xss
	 *
	 * @access	public
	 * @param Array $data the array of requests from $_POST, $_GET or others
	 */
    public function xss_clean($data){
        if (empty($data)) {
            return $data;
		}
            
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->xss_clean($data);
            }            
            return $data;
        }
        
        // http://svn.bitflux.ch/repos/public/popoon/trunk/classes/externalinput.php
        // +----------------------------------------------------------------------+
        // | Copyright (c) 2001-2006 Bitflux GmbH                                 |
        // +----------------------------------------------------------------------+
        // | Licensed under the Apache License, Version 2.0 (the "License");      |
        // | you may not use this file except in compliance with the License.     |
        // | You may obtain a copy of the License at                              |
        // | http://www.apache.org/licenses/LICENSE-2.0                           |
        // | Unless required by applicable law or agreed to in writing, software  |
        // | distributed under the License is distributed on an "AS IS" BASIS,    |
        // | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or      |
        // | implied. See the License for the specific language governing         |
        // | permissions and limitations under the License.                       |
        // +----------------------------------------------------------------------+
        // | Author: Christian Stocker <chregu@bitflux.ch>                        |
        // +----------------------------------------------------------------------+
        
        // Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);
        
        return $data;
    }

	/**
	 * Cleans keys to prevent invalid entries
	 *
	 * @access	public
	 * @param Array $data the array of requests from $_POST, $_GET or others
	 */
    protected function clean_input_keys($data){
        $chars = 'a-zA-Z';        
        if (!preg_match('#^[' . $chars . '0-9:_.-]++$#uD', $data)) {
            exit('Illegal key characters in global data');
        }        
        return $data;
    }

	/**
	 * Cleans data to prevent invalid entries
	 *
	 * @access	public
	 * @param Array $data the array of requests from $_POST, $_GET or others
	 */
    protected function clean_input_data($data){
        if (is_array($data)) {
            $new_array = array();
            foreach ($data as $key => $value) {
                $new_array[$this->clean_input_keys($key)] = $this->clean_input_data($value);
            }            
            return $new_array;
        }
        
        if ($this->magic_quotes_gpc === true) {
            $data = stripslashes($data);
        }        
        $data = $this->xss_clean($data);        
        return $data;
    } 

	/**
	 * Filter a haystack with a specified callback
	 *
	 * @access	public
	 * @param Array $haystack the array to be filtered
	 * @param String $callback the name of the function to be used
	 */
	public function filter($haystack, $callback){
		if (is_array($haystack)) {
			return array_filter($haystack, $callback);
		}
		return false;
	}

	/**
	 * Hash a string using an exisiting method
	 *
	 * @access	public
	 * @param String $string the string to be hashed
	 * @param String $method the name of the function to be used
	 */
	public function hash($string, $method){
		$algos = $this->getHashers();
		if (in_array($method, $algos)) {
			if (file_exists($string)) {
				return hash_file($string, $method);
			} elseif (is_string($string)) {
				return hash($string, $method);
			}
		}
	}

	/**
	 * Get the currently enabled hashers for the PHP installation
	 *
	 * @access	public
	 */
	public function getHashers(){
		return hash_algos();
	}
	
}

?>