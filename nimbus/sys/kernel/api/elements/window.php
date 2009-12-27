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
 * The Window
 *
 * @category:   		API/Elements
 */
class window extends Elements implements ElementInterface {

	/**
	 * Class constructor
	 *
	 * @access:	Public
	 */
	public function __construct($options = array()){
		parent::__construct(array(
			'id' => 'window-' . generateHash(microtime()),
			'classes' => array(),
			'type' => 0, //Application(0) or Dialog(1)
			'height' => 0,
			'minHeight' => null,
			'maxHeight' => null,
			'width' => 0,
			'minWidth' => null,
			'maxWidth' => null,
			'icon' => config('appurl') . 'public/resources/images/icons/Tango/16/apps/utilities-text-editor.png',
			'x' => 0,
			'y' => 0,
			'z' => 0,
			'name' => null,
			'title' => 'Default Window Title',
			'toolbars' => array(),
			'content' => array(),
			'buttons' => array(),
			'parent' => null,			
			//Flags
			'visible' => true,
			'resizable' => true,
			'draggable' => true,
			'pinnable' => false,
			'minimizable' => true,
			'toggable' => true,
			'closable' => true,
			'hasIcon' => true,	
			'modal' => false,
			//Callbacks
			'onResizing' => null,
			'onResizeStart' => null,
			'onResizeStop' => null,
			'onDraging' => null,
			'onDragStart' => null,
			'onDragStop' => null,
			'onPin' => null,
			'onUnpin' => null,
			'onMinimize' => null,
			'onMaximize' => null,
			'onToggle' => null,
			'onLoad' => null,
			'onUnload' => null
		), $options);
		//Render the position fix
		$classes = $this->flag('classes');
		if ($this->flag('x') == 'center') {
			$classes = array_merge($classes, array('center-x'));
		}
		if ($this->flag('y') == 'center') {
			$classes = array_merge($classes, array('center-y'));
		}
		if ($this->flag('modal') == true) {
			$classes = array_merge($classes, array('modal'));
		}
		$this->flag('classes', $classes);
	}

	/**
	 * Include and Render the element
	 *
	 * @access:	Public
	 */
	public function render(){
		//Build the path to the file
		$file = SKIN_DIR . 'common' . DS . 'templates' . DS. 'window.html';
		//Include the file
		include $file;
	}

	/**
	 * TEMPORARY: Display the contents from the content store
	 *
	 * @access:	Public
	 */
	public function content(){
		echo implode("", $this->flag('content'));
	}

	/**
	 * PLACEHOLDER: Display the toolbars from the toolbar store
	 *
	 * @access:	Public
	 */
	public function toolbars(){}

	/**
	 * PLACEHOLDER: Display buttons in order of arrangement in the buttons array
	 *
	 * @access:	Public
	 */
	public function buttons(){}

}

?>