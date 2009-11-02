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
function utf8_strlen($str){    
	return mb_strlen($str);
}

function utf8_strpos($str, $search, $offset = FALSE){
    if ($offset === FALSE) {
        return mb_strpos($str, $search);
    } else {
        return mb_strpos($str, $search, $offset);
    }
}

function utf8_strrpos($str, $search, $offset = FALSE){
    if ($offset === FALSE) {
        # Emulate behaviour of utf8_strrpos rather than raising warning
        if (empty($str)) {
            return FALSE;
        }
        return mb_strrpos($str, $search);
    } else {
        if (!is_int($offset)) {
            //trigger_error('utf8_strrpos expects parameter 3 to be long',E_USER_WARNING);
            return FALSE;
        }
        
        $str = mb_substr($str, $offset);
        
        if (FALSE !== ($pos = mb_strrpos($str, $search))) {
            return $pos + $offset;
        }
        
        return FALSE;
    }
}

function utf8_substr($str, $offset, $length = FALSE){
    if ($length === FALSE) {
        return mb_substr($str, $offset);
    } else {
        return mb_substr($str, $offset, $length);
    }
}

function utf8_strtolower($str){
    return mb_strtolower($str);
}

function utf8_strtoupper($str){
    return mb_strtoupper($str);
}

?>