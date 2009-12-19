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
	 * Holds the scripts that has been loaded onto the cloud
	 *
	 * @access	protected
	 */
	protected $__loaded = array(
						'services' => array(),
						'modules' => array(),
					);

	/**
	 * Variable that holds the benchmarks for the system
	 *
	 * @access	protected
	 */
	protected $__benchmarks;

	/**
	 * Singleton function
	 *
	 * @access	public
	 * @return Object instance of the object
	 */
	public static function getInstance(){
		static $_super;
		if (!is_object($_super)){
			$_super = new Cloud();
		}
		return $_super;
	}
	
	/**
	 * Class constructor
	 *
	 * @access	public
	 */
	public function __construct(){
		global $language;
		$_this = Registry::getInstance();
		//load the hook manager
		Loader::kernel('hooks');
		//Include and instantiate the library files
		Loader::library(array('session', 'dbo'));
		//Initialize the Database Layer
		$this->db = new Dbo();
		//Get configuration
		$result = $this->db->select(null, null, 'options');
		$this->config = new stdClass();
		foreach ($result as $name) {
			$this->config->{$name['option_name']} = $name['option_value'];
		}
		//Set the paths	- PATH Declaration is in the wrong place #TODO 0004
		$this->config->path = array('scripts' => $this->config->appurl . 'public/resources/scripts/', 'skins' => $this->config->appurl . 'public/resources/skins/', 'images' => $this->config->appurl . 'public/resources/images/', 'media' => $this->config->appurl . 'public/resources/media/');
		//Set Timezone
		date_default_timezone_set($this->config->timezone);
		//Create the request class
		$this->_request();
		//Create the session
		$this->session = new Session();
	}

	/**
	 * Instantiator of the superclass. Hides everything from child classes
	 *
	 * @access	public
	 */
	public function init(){
		//Start the session
		$this->session->start();
		//Load shell
		Loader::shell('shell');
		//Load the token and user manager
		Loader::system(array('rpc', 'token', 'user'));
		//Create the RPC property
		$this->RPC = new RPC();
		//Clear up the token store
		Token::cleanUp();
		//load the interfaces
		Loader::kernel(array('services', 'application', 'elements'));
		//Load the initial services
		$this->service(array_merge(array('security'), unserialize($this->config->init_services)));
		//Load the extensions
		$this->module(unserialize($this->config->init_modules));
		//load the API
		Loader::kernel('api');
	}

	/**
	 * Abstract function to load services into the cloud class
	 *
	 * @access	public
	 * @param	Mixed $services a string filename or an array of filenames to be loaded
	 */
	public function service($services = null){
		$this->__load('services', $services);
	}

	/**
	 * Abstract function to load modules into the cloud class
	 *
	 * @access	public
	 * @param	Mixed $modules a string filename or an array of filenames to be loaded
	 */
	public function module($modules = null){
		$this->__load('modules', $modules);
	}

	/**
	 * Method to load extensions into the cloud class
	 *
	 * @access	public
	 * @param	String $name the namespace for the extensions to load
	 * @param	Mixed $name a string filename or an array of filenames to be loaded
	 */
	protected function __load($name, $files){
		if ($files) {
			if (is_array($files)) {
				foreach ($files as $file) {
					$this->__load($name, $file);
				}
			} else {
				if (!in_array($files, $this->__loaded[$name])) {
					global $language;
					switch ($name) {
						case "services":
							if (!Loader::kernel('service' . DS . $files)) {
								trigger_error(sprintf($language['error_000E'], strtoupper($files)));
								return false;
							}
						break;
						case "modules":
							if (!Loader::module($files)) {
								trigger_error(sprintf($language['error_001E'], strtoupper($files)));
								return false;
							}
						break;
					}
					if (class_exists($files)) {
						define(strtoupper($files) . "_LOADED", 1);
						$this->__loaded[$name][] = $files;
						if (method_exists($files, 'getInstance')) {
							$s = $files::getInstance();
						} else {
							$s = new $files();
						}
						return $s;
					}
				}
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
		if (!empty($_GET) || !empty($_POST)) {
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
		if (!isset($this->request->items)) {
			if ($mark == 'start' && !isset($this->__benchmarks[$id])) {
				$this->__benchmarks[$id]['start'] = microtime(true);
			} else {
				$total_time = microtime(true) - $this->__benchmarks[$id]['start'];
				if ($output > 0) {
					global $language;
					printf('<!-- ' . $language['benchmark_output'] . ' -->', $id, round($total_time * 1000));
				}
			}
		}
	}
	
}

?>