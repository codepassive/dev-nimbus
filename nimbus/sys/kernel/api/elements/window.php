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
	 * Name of the Element
	 *
	 * @access:	Public
	 */
	public $name = 'window';

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
			'icon' => config('appurl') . 'public/resources/images/icons/Tango/16/actions/document-new.png',
			'x' => 0,
			'y' => 0,
			'z' => 0,
			'name' => null,
			'title' => 'Default Window Title',
			'toolbars' => array(),
			'content' => array(),
			'buttons' => array(),
			'showInTaskbar' => true,
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
		if ($this->flag('x') == 'center') $classes = array_merge($classes, array('center-x'));
		if ($this->flag('y') == 'center') $classes = array_merge($classes, array('center-y'));
		if ($this->flag('modal') == true) $classes = array_merge($classes, array('modal'));
		if ($this->flag('draggable') == true) $classes = array_merge($classes, array('draggable'));
		if ($this->flag('resizable') == true) $classes = array_merge($classes, array('resizable'));
		if ($this->flag('toggable') == true) $classes = array_merge($classes, array('toggable'));
		$this->flag('classes', $classes);
		//Set the styles
		$this->styles = '';
		$this->styles .= ($this->flag('width') == 0) ? 'width:200px;': 'width:' . $this->flag('width') . ';';
		$this->styles .= ($this->flag('height') == 0) ? '': 'height:' . $this->flag('height') . ';';
	}

	/**
	 * Display the contents from the content store
	 *
	 * @access:	Public
	 */
	public function content(){
		echo implode("", $this->flag('content'));
	}

	/**
	 * Display the toolbars from the toolbar store
	 *
	 * @access:	Public
	 */
	public function toolbars($id){
		$toolbars = $this->flag('toolbars');
		if (isset($toolbars[$id])) {
			foreach($toolbars[$id] as $toolbar){
				if (is_object($toolbar)) {
					echo '<div class="toolbar-row">' . $toolbar->render() . '</div>';
				} else {
					echo '<div class="toolbar-row">' . $toolbar . '</div>';
				}
			}
		}
	}

	/**
	 * Display buttons in order of arrangement in the buttons array
	 *
	 * @access:	Public
	 */
	public function buttons(){
		$output = '';
		$buttons = $this->flag('buttons');
		$i = 1;
		foreach ($buttons as $button) {
			if (is_array($button)) {
				//For the OK or proceed button
				$hash = generateHash(microtime());
				$id = $this->handle . '-button-' . $hash;
				$output .= '<input type="button" value="' . $button[0] . '" id="' . $id . '" class="button"/>&nbsp;';
				if (isset($button[2])) {
					if (isset($button[1])) {
						Application::bindEvent('click', $id, $this->handle, $button[1], true);
					}
				} else {
					if (isset($button[1])) {
						Application::bindEvent('click', $id, $this->handle, $button[1]);
					}
				}
			} else {
				$output .= $button;
			}
			$i++;
		}
		return $output;
	}

}

?>