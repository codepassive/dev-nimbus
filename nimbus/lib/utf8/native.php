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
 * @subpackage:		Nimbus_utf8
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */
/**
 * Copyright (c) PHPUTF8
 * http://sourceforge.net/projects/phputf8/
 */
function utf8_strlen($str = null){
	return strlen($str);
}

function utf8_strpos($str = null, $search = null, $offset = null){    
        return strpos($str, $search, $offset);
}

function utf8_strrpos($str = null, $search = null, $offset = null){
        return strrpos($str, $search, $offset);
}

function utf8_substr($str = null, $offset = null, $length = null){    
	if ($length == null) {
		return substr($str, $offset);	
	} else {
		return substr($str, $offset, $length);
	}
}
function utf8_strtolower($str = null){
    return strtolower($str);
}

function utf8_strtoupper($str = null){
    return strtoupper($str);
}

function utf8_ucfirst($str = null){
    return ucfirst($str);
}

function utf8_ltrim($str = null, $charlist = null){
    return ltrim($str, $charlist);
}

function utf8_rtrim($str = null, $charlist = null){
    return rtrim($str, $charlist);
}

function utf8_trim($str = null, $charlist = null){
    return trim($str, $charlist);
}

function utf8_substr_replace($str = null, $repl = null, $start = null, $length = null){
	return substr_replace($str, $repl, $start, $length);
}

function utf8_strspn($str = null, $mask = null, $start = null, $length = null){
 	return strspn($str, $mask, $start, $length);
}

function utf8_str_split($str = null, $split_len = 1){    
    return str_split($str, $split_len);    
}

function utf8_strrev($str = null){    
    return strrev($str);
}

function utf8_str_pad($input = null, $length = null, $padStr = null, $type = STR_PAD_RIGHT){
	return str_pad($input, $length, $padStr, $type);
}

function utf8_stristr($str = null, $search = null) {    
    return stristr($str, $search);
}

function utf8_ireplace($search = null, $replace = null, $str = null, $count = null){
    return str_ireplace($search, $replace, $str, $count);
}

function utf8_strcspn($str = null, $mask = null, $start = null, $length = null){
   	return strcspn($str, $mask, $start, $length);
    
}
function utf8_ucwords($str = null) {
	return ucwords($str);
}

function utf8_strcasecmp($strX = null, $strY = null) {
    return strcasecmp($strX, $strY);
}

function utf8_ord($chr = null){
    return ord($chr);
}

function utf8_wordwrap($str = null, $width = null, $break = null, $cut = null){	
	return wordwrap($str, $width, $break, $cut);
}

function utf8_pathinfo($string = null){		
	return pathinfo($string);
}

function utf8_basename($string = null, $suffix = ''){	
	return basename($string, $suffix);
}

function utf8_getFileName($string){
	$name = utf8_basename($string);
 	return utf8_substr($name, 0, utf8_strrpos($name, '.'));
}

function utf8_getExtension($string){
	return utf8_substr($string, utf8_strrpos($string, '.') + 1, null);
}

?>