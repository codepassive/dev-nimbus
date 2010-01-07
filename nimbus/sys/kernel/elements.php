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
 * @subpackage:	Nimbus_api
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */

/**
 * Element base class
 *
 * @category:   		API/Elements
 */
class Elements extends API {
	
	/**
	 * Name of the Element
	 *
	 * @access:	Public
	 */
	public $name;

	/**
	 * The default option values for the element
	 *
	 * @access:	Public
	 */
	private $_defaults = array();

	/**
	 * The options set for the element
	 *
	 * @access:	Public
	 */
	public $options = array();

	/**
	 * The ID of the element
	 *
	 * @access:	Public
	 */
	public $ID;

	/**
	 * The Javascript handle of the element
	 *
	 * @access:	Public
	 */
	public $handle;

	/**
	 * The Styles for the element
	 *
	 * @access:	Public
	 */
	public $styles;

	/**
	 * Class constructor
	 *
	 * @access:	Public
	 */
	public function __construct($defaults = array(), $options = array()){
		parent::__construct();
		//Set the default values for the options
		$this->_defaults = $defaults;
		//Set the current options
		$this->options = $options;
		
		//Set the elements Properties
		$this->ID = (isset($this->options['id'])) ? $this->options['id']: $this->_defaults['id'];
		//Set the elements Properties
		$this->handle = (isset($this->options['handle'])) ? $this->options['handle']: null;
	}

	/**
	 * Include and Render the element
	 *
	 * @access:	Public
	 */
	public function render(){
		//Build the path to the file
		$file = SKIN_DIR . 'common' . DS . 'templates' . DS. $this->name . '.html';
		//Include the file
		ob_start();
		include $file;
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * Retrieve an option for the element
	 *
	 * @access:	Protected
	 * @param:	String $id the option name
	 * @param:	String $id the value of the option name
	 */
	protected function __option($id, $value = null){
		if ($value == null) {
			return (isset($this->options[$id])) ? $this->options[$id]: $this->_defaults[$id];
		} else {
			$this->options[$id] = $value;
		}
		return true;
	}

	/**
	 * Get an option for the element
	 *
	 * @access:	Public
	 * @param:	String $id the option name
	 * @param:	String $id the value of the option name
	 */
	public function option($id, $value = null){
		if ($value == null) {
			echo $this->__option($id);
		} else {
			$this->__option($id, $value);
		}
	}

	/**
	 * Return an option for the element
	 *
	 * @access:	Public
	 * @param:	String $id the option name
	 * @param:	String $id the value of the option name
	 */
	public function flag($id = null, $value = null){
		if ($id) {
			if ($value == null) {
				return $this->__option($id);
			} else {
				$this->__option($id, $value);
			}
		} else {
			return $this->options;
		}
	}

	/**
	 * Get the classes for the Element
	 *
	 * @access:	Public
	 */
	public function classes(){
		echo implode(' ', $this->__option('classes'));
	}

}

?>