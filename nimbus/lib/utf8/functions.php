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

function utf8_ucfirst($str){
    switch (utf8_strlen($str)) {
        case 0:
            return '';
        break;
        case 1:
            return utf8_strtoupper($str);
        break;
        default:
            preg_match('/^(.{1})(.*)$/us', $str, $matches);
            return utf8_strtoupper($matches[1]) . $matches[2];
        break;
    }
}

function utf8_ltrim($str, $charlist = FALSE){
    if ($charlist === FALSE) return ltrim($str);
    
    //quote charlist for use in a characterclass
    $charlist = preg_replace('!([\\\\\\-\\]\\[/^])!', '\\\${1}', $charlist);
    
    return preg_replace('/^['.$charlist.']+/u', '', $str);
}

function utf8_rtrim($str, $charlist = FALSE){
    if ($charlist === FALSE) return rtrim($str);
    
    //quote charlist for use in a characterclass
    $charlist = preg_replace('!([\\\\\\-\\]\\[/^])!', '\\\${1}', $charlist);
  
    return preg_replace('/['.$charlist.']+$/u', '', $str);
}

function utf8_trim($str, $charlist = FALSE){
    if ($charlist === FALSE) return trim($str);
    return utf8_ltrim(utf8_rtrim($str, $charlist), $charlist);
}

function utf8_substr_replace($str, $repl, $start , $length = NULL){
    preg_match_all('/./us', $str, $ar);
    preg_match_all('/./us', $repl, $rar);
    if ($length === NULL) {
        $length = utf8_strlen($str);
    }
    array_splice($ar[0], $start, $length, $rar[0]);
    return join('', $ar[0]);
}

function utf8_strspn($str, $mask, $start = NULL, $length = NULL){
    
    $mask = preg_replace('!([\\\\\\-\\]\\[/^])!', '\\\${1}', $mask);
    
    if ($start !== NULL || $length !== NULL) {
        $str = utf8_substr($str, $start, $length);
    }
        
    preg_match('/^['.$mask.']+/u', $str, $matches);
    
    if (isset($matches[0])) {
        return utf8_strlen($matches[0]);
    }
    
    return 0;
}

function utf8_str_split($str, $split_len = 1){
    
    if (!preg_match('/^[0-9]+$/', $split_len) || $split_len < 1 ) {
        return FALSE;
    }
    
    $len = utf8_strlen($str);
    if ($len <= $split_len) {
        return array($str);
    }
    
    preg_match_all('/.{'.$split_len.'}|[^\x00]{1,'.$split_len.'}$/us', $str, $ar);
    return $ar[0];
    
}

function utf8_strrev($str){
    preg_match_all('/./us', $str, $ar);
    return join('', array_reverse($ar[0]));
}

function utf8_str_pad($input, $length, $padStr = ' ', $type = STR_PAD_RIGHT){
    
    $inputLen = utf8_strlen($input);
    if ($length <= $inputLen) {
        return $input;
    }
    
    $padutf8_strlen = utf8_strlen($padStr);
    $padLen = $length - $inputLen;
    
    if ($type == STR_PAD_RIGHT) {
        $repeatTimes = ceil($padLen / $padutf8_strlen);
        return utf8_substr($input . str_repeat($padStr, $repeatTimes), 0, $length);
    }
    
    if ($type == STR_PAD_LEFT) {
        $repeatTimes = ceil($padLen / $padutf8_strlen);
        return utf8_substr(str_repeat($padStr, $repeatTimes), 0, floor($padLen)) . $input;
    }
    
    if ($type == STR_PAD_BOTH) {
        
        $padLen/= 2;
        $padAmountLeft = floor($padLen);
        $padAmountRight = ceil($padLen);
        $repeatTimesLeft = ceil($padAmountLeft / $padutf8_strlen);
        $repeatTimesRight = ceil($padAmountRight / $padutf8_strlen);
        
        $paddingLeft = utf8_substr(str_repeat($padStr, $repeatTimesLeft), 0, $padAmountLeft);
        $paddingRight = utf8_substr(str_repeat($padStr, $repeatTimesRight), 0, $padAmountLeft);
        return $paddingLeft . $input . $paddingRight;
    }
    
    trigger_error('utf8_str_pad: Unknown padding type (' . $type . ')',E_USER_ERROR);
}

function utf8_stristr($str, $search){
    
    if (utf8_strlen($search) == 0) {
        return $str;
    }
    
    $lstr = utf8_strtolower($str);
    $lsearch = utf8_strtolower($search);
    preg_match('/^(.*)'.preg_quote($lsearch).'/Us', $lstr, $matches);
    
    if (count($matches) == 2) {
        return utf8_substr($str, utf8_strlen($matches[1]));
    }
    
    return FALSE;
}

function utf8_ireplace($search, $replace, $str, $count = NULL){
    
    if (!is_array($search)) {
        
        $slen = utf8_strlen($search);      
        if ($slen == 0) {
            return $str;
        }
        
        $lendif = utf8_strlen($replace) - utf8_strlen($search);
        $search = utf8_strtolower($search);
        
        $search = preg_quote($search);
        $lstr = utf8_strtolower($str);
        $i = 0;
        $matched = 0;
        while ( preg_match('/(.*)'.$search.'/Us', $lstr, $matches) ) {
            if ( $i === $count ) {
                break;
            }
            $mlen = utf8_strlen($matches[0]);
            $lstr = utf8_substr($lstr, $mlen);
            $str = utf8_substr_replace($str, $replace, $matched+utf8_strlen($matches[1]), $slen);
            $matched += $mlen + $lendif;
            $i++;
        }
        return $str;        
    } else {
        foreach (array_keys($search) as $k) {            
            if (is_array($replace)) {                
                if (array_key_exists($k,$replace)) {                    
                    $str = utf8_ireplace($search[$k], $replace[$k], $str, $count);                    
                } else {                
                    $str = utf8_ireplace($search[$k], '', $str, $count);                    
                }                
            } else {            
                $str = utf8_ireplace($search[$k], $replace, $str, $count);                
            }
        }
        return $str;        
    }
}

function utf8_strcspn($str, $mask, $start = NULL, $length = NULL){
    
    if (empty($mask) || utf8_strlen($mask) == 0) {
        return NULL;
    }
    
    $mask = preg_replace('!([\\\\\\-\\]\\[/^])!', '\\\${1}', $mask);
    
    if ($start !== NULL || $length !== NULL) {
        $str = utf8_substr($str, $start, $length);
    }
        
    preg_match('/^[^'.$mask.']+/u', $str, $matches);
    
    if (isset($matches[0])) {
        return utf8_strlen($matches[0]);
    }
    
    return 0;
    
}

function utf8_ucwords($str){
    // Note: [\x0c\x09\x0b\x0a\x0d\x20] matches;
    // form feeds, horizontal tabs, vertical tabs, linefeeds and carriage returns
    // This corresponds to the definition of a "word" defined at http://www.php.net/ucwords
    $pattern = '/(^|([\x0c\x09\x0b\x0a\x0d\x20]+))([^\x0c\x09\x0b\x0a\x0d\x20]{1})[^\x0c\x09\x0b\x0a\x0d\x20]*/u';
    return preg_replace_callback($pattern, 'utf8_ucwords_callback', $str);
}

function utf8_ucwords_callback($matches){
    $leadingws = $matches[2];
    $ucfirst = utf8_strtoupper($matches[3]);
    $ucword = utf8_substr_replace(ltrim($matches[0]), $ucfirst, 0, 1);
    return $leadingws . $ucword;
}

function utf8_strcasecmp($strX, $strY){
    $strX = utf8_strtolower($strX);
    $strY = utf8_strtolower($strY);
    return strcmp($strX, $strY);
}

function utf8_ord($chr){
    
    $ord0 = ord($chr);
    
    if ($ord0 >= 0 && $ord0 <= 127) {
        return $ord0;
    }
    
    if (!isset($chr{1})) {
        trigger_error('Short sequence - at least 2 bytes expected, only 1 seen');
        return FALSE;
    }
    
    $ord1 = ord($chr{1});
    if ($ord0 >= 192 && $ord0 <= 223) {
        return ( $ord0 - 192 ) * 64 
            + ( $ord1 - 128 );
    }
    
    if (!isset($chr{2})) {
        trigger_error('Short sequence - at least 3 bytes expected, only 2 seen');
        return FALSE;
    }
    $ord2 = ord($chr{2});
    if ($ord0 >= 224 && $ord0 <= 239) {
        return ($ord0-224)*4096 
            + ($ord1-128)*64 
                + ($ord2-128);
    }
    
    if (!isset($chr{3})) {
        trigger_error('Short sequence - at least 4 bytes expected, only 3 seen');
        return FALSE;
    }
    $ord3 = ord($chr{3});
    if ($ord0>=240 && $ord0<=247) {
        return ($ord0-240)*262144 
            + ($ord1-128)*4096 
                + ($ord2-128)*64 
                    + ($ord3-128);
    
    }
    
    if (!isset($chr{4})) {
        trigger_error('Short sequence - at least 5 bytes expected, only 4 seen');
        return FALSE;
    }
    $ord4 = ord($chr{4});
    if ($ord0>=248 && $ord0<=251) {
        return ($ord0-248)*16777216 
            + ($ord1-128)*262144 
                + ($ord2-128)*4096 
                    + ($ord3-128)*64 
                        + ($ord4-128);
    }
    
    if (!isset($chr{5})) {
        trigger_error('Short sequence - at least 6 bytes expected, only 5 seen');
        return FALSE;
    }
    if ($ord0>=252 && $ord0<=253) {
        return ($ord0-252) * 1073741824 
            + ($ord1-128)*16777216 
                + ($ord2-128)*262144 
                    + ($ord3-128)*4096 
                        + ($ord4-128)*64 
                            + (ord($c{5})-128);
    }
    
    if ($ord0 >= 254 && $ord0 <= 255) { 
        trigger_error('Invalid UTF-8 with surrogate ordinal '.$ord0);
        return FALSE;
    }
    
}

function utf8_wordwrap($str, $width=75, $break='\n', $cut=false){
	$newString = '';
	if ($cut == true) {
		$y = 1;
		for ($x=0;$x < utf8_strlen($str);$x++) {						
			$newString .= utf8_substr($str, $x,1);
			if($y == $width){
				$newString .= $break;
				$y = 1;	
			}else{
				$y++;
			}
		}		
		return $newString;
	}
	
	$y = 1;
	for ($x=0;$x < utf8_strlen($str);$x++) {			
		$newString .= utf8_substr($str, $x, 1);
		if ($str{$x} == ' ') {			
			if ($y >= $width) {
				$newString .= $break;	
				$y = 1;
			} else {
				$y++;
			}
		}			
	}
	
	return $newString;
}

function utf8_pathinfo($string){		
	$info['basename'] = utf8_basename($string);
	$info['dirname'] = dirname($string);
	$info['filename'] = utf8_getFileName($string);
	$info['extension'] = utf8_getExtension($string);
	
	return $info;
}

function utf8_basename($string, $suffix=''){	
	//Checking if path are from windows...\	
	if (utf8_strpos($string, '\\') !== false) {
		//Replacing all \ with /
		$string = utf8_ireplace('\\', '/', $string);
	}
	
	//Remove the last character if it is a slash
	if (utf8_strrpos($string, '/') == utf8_strlen($string) - 1) {
		$string = utf8_substr($string, 0, utf8_strlen($string) - 1) ;		
	}	
	//TODO: check if dirname is more speed than utf8_str*** functions.
	//If is a directory	
	if (utf8_strpos($string, '/') && utf8_strrpos($string, '/') == utf8_strlen($string) - 1) {		
		while (utf8_strrpos($string, '/') == utf8_strlen($string) - 1) {			
			$string = utf8_substr($string, 0, utf8_strrpos($string, '/'));
		}		
	}
	
	if (utf8_strpos($string,'/') !== false) {
		$string =  utf8_substr($string, utf8_strrpos($string, '/') + 1, utf8_strlen($string));	
	}
		
	if ($suffix != '') {
		$pos = utf8_strrpos($string, $suffix);
		if($pos === false){
			return $string;
		}else{
			return utf8_substr($string, 0, utf8_strrpos($string, $suffix));
		}
	}
	
	return $string;
}

function utf8_getFileName($string){
	$name = utf8_basename($string);
 	return utf8_substr($name, 0, utf8_strrpos($name, '.'));
}

function utf8_getExtension($string){
	return utf8_substr($string, utf8_strrpos($string, '.') + 1);
}

?>