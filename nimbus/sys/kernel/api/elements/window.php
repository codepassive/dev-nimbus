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
 * @subpackage:		Nimbus_API
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */

class Window {

	public $html = '';
	
	public $options = array();
	public $toolbars = array();

	public function __construct($options){
		$this->options = $options;
		
		//Classes
		if ($options['resizable'] === true) {
			$this->options['classes'][] = 'resizable';
		}
		if ($options['draggable'] === true) {
			$this->options['classes'][] = 'draggable';
		}
		
		//Window Properties
		$this->options['resizable'] = (@$options['resizable'] === true) ? true: false;
		$this->options['draggable'] = (@$options['draggable'] === true) ? true: false;
		$this->options['minimizable'] = (@$options['minimizable'] === true) ? true: false;
		$this->options['closable'] = (@$options['closable'] === true) ? true: false;
		$this->options['toggable'] = (@$options['toggable'] === true) ? true: false;
	}
	
	public function content($html){
		$this->options['content'] .= (is_object($html)) ? $html->text: $html;
	}
	
	public function toolbar(){
		//dummy
		$this->toolbars['menu'] = '';
		$this->toolbars['status'] = '';
	}
	
	public function buttons(){}
	
	public function display(){
		ob_start();	
		//Delegations
		$window = $this->options;
		$content = $this->options['content'];
		$toolbar = $this->toolbars;
		
		//Include the shell file
		include 'shell/window.php';
		$this->html = ob_get_contents();
		ob_end_clean();
		echo "var " . $this->options['name'] . "_view = " . json_encode($this->html) . ";\nvar view_id = '" . $this->options['pid'] . "';\n";

	}

}
 
?>