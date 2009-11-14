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
 * @subpackage:		Nimbus_kernel
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */

/**
 * The Cloud Superclass
 *
 * @category:   		Cloud
 */
class Cloud {

	/**
	 * Language placeholder
	 *
	 * @access	public
	 */
	public $language;
	
	/**
	 * Request placeholder
	 *
	 * @access	public
	 */
	public $request;
	
	/**
	 * Variable that holds the benchmarks for the system
	 *
	 * @access	protected
	 */
	protected $__benchmarks;

	/**
	 * Class constructor
	 *
	 * @access	public
	 */
	public function __construct(){
		global $language;
		$_this = Registry::getInstance();		
		//Use the current language in the class
		$this->language = $language;
		//Create the request class
		$this->_request();
		//Include the library files
		Loader::library(array('session', 'dbo'));
	}

	/**
	 * Generates the request class
	 *
	 * @access	public
	 */
	private function _request(){
		//Create the request class
		$this->request = new stdClass();
		//Reset the global $_REQUEST variable
		$_REQUEST = array();
		//Set the necessary items to the public properties of the request class
		$this->request->items = $_REQUEST = array_merge($_GET, $_POST);
		//Properties to get the $_POST, $_GET and the $_FILES superglobal
		$this->request->post = $_POST;
		$this->request->get = $_GET;
		$this->request->files = $_FILES;
		//Provide a numerical index for the request items
		//TODO#00001 - Not comfortable with implementation
		foreach ($this->request->items as $i => $v) {
			$this->request->items[] = array('name' => $i, 'value' => $v);
		}
		//Information about the request
		$this->request->type = $this->request->items[0]['name'];
	}
	
	/**
	 * Benchmarking utility for the system
	 *
	 * @access	public
	 * @param  String $id ID of the script to be benchmark
	 * @param  String $mark start or stop counter
	 * @param  Boolean $output determine whether a benchmark should be printed out
	 */
	public function benchmark($id, $mark, $output = 0){
		if ($mark == 'start' && !isset($this->__benchmarks[$id])) {
			$this->__benchmarks[$id]['start'] = microtime(true);
		} else {
			$total_time = microtime(true) - $this->__benchmarks[$id]['start'];
			if ($output > 0) {
				printf($this->language['benchmark_output'], $id, round($total_time * 1000));
			}
		}
	}
	
}

?>