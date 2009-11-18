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
 * @subpackage:		Nimbus_shell
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */
/**
 * @ignore
 * This file contains general abstract functions for the Application Canvas.
 * The methods generate the header, footer and body elements.
 */

/**
 * Generate the meta, links or scripts to the Head
 */
function head(){
	//Call the static shell method head
	Shell::head();
}

/**
 * Generate the HTML to be appended to the body
 */
function body(){
	//Call the static shell method body
	Shell::body();
}

/**
 * Generate the Header and Footer javascripts
 *
 * @param String $id the identifier from where to fetch the script queue
 */
function scripts($id){
	//Call the static shell method scripts
	Shell::scripts($id);
}

?>