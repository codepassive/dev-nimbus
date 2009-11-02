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
 * Abstract Class for exception handling
 *
 * @category:   		Exception
 * @source:		http://www.php.net/manual/en/language.exceptions.php#91159
 */
abstract class ExceptionAbstract extends Exception implements ExceptionInterface {

	/**
	 * Exception message
	 *
	 * @access	protected
	 */
	protected $message = 'Unknown exception';
	
	/**
	 * User-defined exception code
	 *
	 * @access	protected
	 */
    protected $code = 0;
	
	/**
	 * Source file of exception message
	 *
	 * @access	protected
	 */
    protected $file;
	
	/**
	 * Source line of exception message
	 *
	 * @access	protected
	 */
    protected $line;
	
	/**
	 * Reserved private variables for the exception
	 *
	 * @access	private
	 */
    private $string;
    private $trace;

	/**
	 * Class constructor
	 *
	 * @access	public
	 * @param  String $message message of the exception
	 * @param  Integer $code integer value of the exception code
	 */
    public function __construct($message = null, $code = 0){
        if (!$message) {
            throw new $this('Unknown '. get_class($this));
        }
        parent::__construct($message, $code);
    }
   
	/**
	 * Custom toString method
	 *
	 * @access	public
	 * @return String exception message in detail
	 */
    public function __toString(){
        return get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n" . "{$this->getTraceAsString()}";
    }
}
?>