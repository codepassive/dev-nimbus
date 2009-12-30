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
 * @subpackage:		Nimbus_system
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */

/**
 * The Application superclass
 *
 * @category:   		Application
 */
class Application extends API {

	/**
	 * Instance information of the application
	 *
	 * @access	Public
	 */
	public $instance = array();

	/**
	 * Name of the application
	 *
	 * @access	Public
	 */
	public $name;

	/**
	 * Determines whether the application will have a forced allow permission to a user or not
	 *
	 * @access	Public
	 */
	public $force = false;
	
	/**
	 * Output of the application
	 *
	 * @access	Public
	 */
	public $output = "(function(){\n";
	
	/**
	 * Array of exposed functions for RPC usage
	 *
	 * @access	Public
	 */
	public $expose = array();

	/**
	 * Class constructor
	 *
	 * @access	Public
	 */
	public function __construct(){
		parent::__construct();
		//Load the API files
		Loader::api(array(
				//Base Classes
				'html'
			));
		//Delegate the HTML API class to usable properties
		$this->html = new HTML();
	}

	/**
	 * The constructor for an application. Executed first to gain access
	 * to the application superclass properties
	 *
	 * @access	Public
	 */
	public function __init($force = false){
		if ($this->user->isAllowed($this->name) || $force === true) {
			//Register the instance onto the system
			$this->instance = $this->register($this->name);
			//Load View and set its style
			foreach ($this->styles as $style) {
				$this->html->link($style);
			}
			//The actual output is generated with this internal method
			$this->init();
		} else {
			//Return a JSON code that permission check failed
			global $language;
			$this->msgbox(array('text' => $language['error_000G'] . ' #000G', 'type' => 'error', 'title' => $language['error_title'], 'noChoice' => true));
			exit();
		}
	}

	/**
	 * Launches an application from the application directory
	 *
	 * @access	Public
	 * @param	String $name name of the application
	 */
	public static function launch($name = null){
		//Route to the request if a launch name is not supplied
		$name = ($name) ? $name: request('app');
		//Proceed with launching the application
		if (is_array($name)) {
			foreach ($name as $n) {
				Application::launch($n);
			}
		} else {
			$path = APPLICATION_DIR . $name . DS . $name . '.php';
			if (file_exists($path)) {
				//Require the Application Index and instantiate the Application superclass				
				if ((request('token') == Session::get('token') && request('action')) || !request('action')) {
					require_once $path;
					$app = new $name();
					$action = request('action');
					if (!$action) {
						$app->__init($app->force);
						$app->view($app->main());
					} else {
						$params = array();
						$i = 0;
						foreach ($_GET as $param) {
							if ($i > 1) {
								$params[] = $param;
							}
							$i++;
						}
						call_user_func_array(array($app, $action()), $params);
					}
					echo $app->display();
				} else {
					global $language;
					$this->msgbox(array('text' => $language['error_000H'], 'type' => 'error', 'title' => $language['error_title'], 'buttons' => array('OK')));
				}
			}
			if (NIMBUS_DEBUG > 0) {
				global $language;
				Log::write(DEBUG_LOG_FILE, sprintf($language['error_000F'], $name));
			}
			return false;
		}
	}

	/**
	 * This function loads the main window method for output in an application
	 * 
	 * @access	Public
	 * @param	Mixed $location the path of the shell file relative to the application directory
	 */
	public function view($location = null){
		//Check if the main window method is present and the location is null
		if (!method_exists($this->name, 'main') && $location == null) {
			$location = APPLICATION_DIR . $this->name . DS . $location . '.php';
			if (file_exists($location) && is_readable($location)) {
				ob_start();
				include_once $location;
				$this->output .= 'var ' . $this->name . '_view = ' . json_encode(ob_get_contents()) . ";\n";
				ob_end_clean();
			} else {
				global $language;
				Log::write(ERROR_LOG_FILE, sprintf($language['error_001F'], $location));
			}
		} else {
			$this->output .= 'var ' . $this->name . '_view = ' . json_encode($location->render()) . ";\n";
		}
	}

	/**
	 * This function loads the shell file for inclusion in a content context
	 * 
	 * @access	Public
	 * @param	String $location the path of the shell file relative to the application directory
	 */
	public function useTemplate($location){
		$location = APPLICATION_DIR . $this->name . DS . $location . '.php';
		if (file_exists($location) && is_readable($location)) {
			ob_start();
			include_once $location;
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		} else {
			global $language;
			Log::write(ERROR_LOG_FILE, sprintf($language['error_001F'], $location));
		}
	}

	/**
	 * This function loads script files for Application use
	 * 
	 * @access	Public
	 * @param	String $file relative to the application directory
	 */
	public function script($file){
		$this->output .= file_get_contents(APPLICATION_DIR . $this->name . DS . $file . '.js');
	}

	/**
	 * Takes any variable and encodes it to JSON format
	 * 
	 * @access	Public
	 * @param	String $input the variable to be encoded
	 * @param	String $var the variable name
	 */
	public function json($input, $var = null){
		if ($var) {
			$this->output .= 'var ' . $this->name . '_' . $var . ' = ' . json_encode($input) . ";\n";
		} else {
			$this->output .= json_encode($input) . "\n";
		}
	}

	/**
	 * This function displays the application and initializes it
	 * 
	 * @access	Public
	 */
	public function display(){
		header('Content-Type: text/javascript');
		$this->script($this->name);
		//Display the Events
		$this->output .= $this->events();
		if (!request('action')) {
			$this->output .= "\n//Initialize the App\n" . $this->name . ".init();\n})();";
		}
		//Display the Output
		echo $this->output;
	}
	
	public function events(){
		$e = Registry::get($this->name . '-events');
		if ($e) {
			$output = "\n//Events\n";
			unset($e[0]);
			$output .= implode("\n", $e);
			return $output;
		}
		return;
	}
	
	public static function bindEvent($event, $id, $handle, $function){
		$events = Registry::get($handle . '-events');
		$script = "$('#{$id}').live('{$event}', function(){{$handle}.{$function}()});";
		if (!in_array($script, $events)) {
			$events[] = $script;
		}
		Registry::set($handle . '-events', $events);
	}

}

?>