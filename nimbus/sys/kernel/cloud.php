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
	 * Configuration placeholder
	 *
	 * @access	public
	 */
	public $config;
	
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
		//Include and instantiate the library files
		Loader::library(array('session', 'dbo'));
		$this->session = new Session();
		$this->session->start();
		$this->db = new Dbo();
		//Get configuration
		$result = $this->db->select(null, null, 'options');
		$this->config = new stdClass();
		foreach ($result as $name) {
			$this->config->{$name['option_name']} = $name['option_value'];
		}
		//Set Timezone
		date_default_timezone_set($this->config->timezone);
		//Load the initial services
		$this->service(array_merge(array('sanitize'), unserialize($this->config->init_services)));
		//Create the request class
		$this->_request();
		//Load the extensions
		$this->module(unserialize($this->config->init_modules));
	}
	
	public function service($services = null){
		if ($services) {
			if (is_array($services)) {
			
			} else {
			
			}
		}
	}
	public function module($modules = null){
		if ($modules) {
			if (is_array($modules)) {
			
			} else {
			
			}
		}
	}

	/**
	 * Generates the request class
	 *
	 * @access	public
	 */
	private function _request(){
		//Create the request class
		$this->request = new stdClass();
		if ($_GET || $_POST) {
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