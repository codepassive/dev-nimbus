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
 * Class for the Sanitation duties
 *
 * @category:   		Security
 * @source:		CakePHP
 */
class Sanitize {

	/**
	 * Prepare a string for safe processing
	 *
	 * @access	public
	 * @param	String $string the string to be prepared
	 * @param	Array $variables the array of variables to be inserted to the string
	 */
	public function prepare($string, $variables){
		$search = array();
		$replace = array();
		foreach ($variables as $v => $a) {
			$search[] = Sanitize::sanitizeString($v);
			$replace[] = Sanitize::sanitizeString($a);
		}
		return (str_replace($search, $replace, $string));
	}

	/**
	 * Sanitize a string
	 *
	 * @access	public
	 * @param	String $string the string to be sanitized
	 * @param	String $mode the mode the string will be sanitized with
	 */
	public function sanitize($string, $mode){
		switch ($mode) {
			case "sql":
				return Sanitize::sanitizeSQL($string);
			break;
			case "url":
				return Sanitize::sanitizeURL($string);
			break;
			case "html":
				return Sanitize::sanitizeHTML($string);
			break;
			default:
				return Sanitize::sanitizeString($string);
			break;
		}
	}

	/**
	 * Sanitize a string only in an alphanumeric state and in a specific range only(if specified)
	 *
	 * @access	public
	 * @param	String $string the string to be sanitized
	 * @param	Integer $min the minimum length the string should be
	 * @param	Integer $max the maximum length the string should be
	 */
	public function paranoid($string, $min='', $max=''){
		$string = preg_replace("/[^a-zA-Z0-9]/", "", $string);
		$len = strlen($string);
		if	((($min != '') && ($len < $min)) || (($max != '') && ($len > $max))) {
			return FALSE;
		}
		return $string;
	}

	/**
	 * Sanitize a string for SQL use
	 *
	 * @access	public
	 * @param	String $sql the sql to be sanitized
	 */
	public function sanitizeSQL($sql){
		if (function_exists('sqlite_escape_string')) {
			return sqlite_escape_string($sql);
		}
	}

	/**
	 * Sanitize a string for URL use
	 *
	 * @access	public
	 * @param	String $url the url to be sanitized
	 */
	public function sanitizeURL($url){
		return filter_var($url, FILTER_SANITIZE_URL);
	}

	/**
	 * Sanitize a url exclusively
	 *
	 * @access	public
	 * @param	String $url the url to be sanitized
	 */
	public function sanitizeString($url){
		return filter_var($url, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH, FILTER_FLAG_ENCODE_HIGH);
	}

	/**
	 * Sanitize a string for safe HTML output
	 *
	 * @access	public
	 * @param	String $html the html to be sanitized
	 */
	public function sanitizeHTML($html){
		$pattern = array('/\&/', '/</', "/>/", '/\n/', '/"/', "/'/", "/%/", '/\(/', '/\)/', '/\+/', '/-/');
		$replacement = array('&amp;', '&lt;', '&gt;', '<br>', '&quot;', '&#39;', '&#37;', '&#40;', '&#41;', '&#43;', '&#45;');
		return preg_replace($pattern, $replacement, $string);
	}

	/**
	 * Sanitize an Integer exclusively
	 *
	 * @access	public
	 * @param	Integer $integer the integer to be sanitized
	 * @param	Integer $min the minimum value the integer should be
	 * @param	Integer $max the maximum value the integer should be
	 */
	public function sanitizeInt($integer, $min='', $max=''){
		$int = intval($integer);
		if ((($min != '') && ($int < $min)) || (($max != '') && ($int > $max))) {
			return false;
		}
		return $int;
	}

	/**
	 * Sanitize a Float exclusively
	 *
	 * @access	public
	 * @param	Float $float the float to be sanitized
	 * @param	Float $min the minimum value the float should be
	 * @param	Float $max the maximum value the float should be
	 */
	public function sanitizeFloat($float, $min='', $max=''){
		$float = floatval($float);
		if ((($min != '') && ($float < $min)) || (($max != '') && ($float > $max))) {
			return false;
		}
		return $float;
	}

	/**
	 * Sanitize a string from whitespace 
	 *
	 * @access	public
	 * @param	String $string the string to be sanitized
	 */
	public function strip($string){
		$string = Sanitize::stripWhitespace($string);
		return $string;
	}

	/**
	 * Sanitize HTML from unescaped tags
	 *
	 * @access	public
	 * @param	String $string the html to be sanitized
	 * @param	Boolean $remove flag to know if html tags should be removed
	 */
	public function stripHTML($string, $remove = false){
		if ($remove) {
			$string = strip_tags($string);
		} else {
			$patterns = array("/\&/", "/%/", "/</", "/>/", '/"/', "/'/", "/\(/", "/\)/", "/\+/", "/-/");
			$replacements = array("&amp;", "&#37;", "&lt;", "&gt;", "&quot;", "&#39;", "&#40;", "&#41;", "&#43;", "&#45;");
			$string = preg_replace($patterns, $replacements, $string);
		}
		return $string;
	}

	/**
	 * Sanitize javascript for proper usage
	 *
	 * @access	public
	 * @param	String $javascript the javascript to be sanitized
	 */
	public function stripJavascript($javascript){
		return preg_replace('/(<link[^>]+rel="[^"]*stylesheet"[^>]*>|<img[^>]*>|style="[^"]*")|<script[^>]*>.*?<\/script>|<style[^>]*>.*?<\/style>|<!--.*?-->/i', '', $javascript);
	}

	/**
	 * Sanitize a string from whitespace 
	 *
	 * @access	public
	 * @param	String $string the string to be sanitized
	 */
	public function stripWhitespaces($string){
		$r = preg_replace('/[\n\r\t]+/', '', $string);
		return preg_replace('/\s{2,}/', ' ', $r);
	}

}

?>