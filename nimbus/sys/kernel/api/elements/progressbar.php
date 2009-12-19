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
 * The Progressbar
 *
 * @category:   		API/Elements
 */
class progressbar extends Elements implements ElementInterface {

	/**
	 * Class constructor
	 *
	 * @access:	Public
	 */
	public function __construct($options = array()){
		parent::__construct(array(
						'id' => generateHash(microtime()),
						'classes' => array(),
						'width' => 0
					), $options);
	}

	/**
	 * Include and Render the element
	 *
	 * @access:	Public
	 */
	public function render(){
		//Build the path to the file
		$file = SKIN_DIR . 'common' . DS . 'templates' . DS. 'progressbar.html';
		//Include the file
		include $file;
	}

}

?>