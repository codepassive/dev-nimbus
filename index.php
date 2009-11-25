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
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */

//Set flag as parent file
define('NIMBUSEXEC', 1);
require_once 'config.php';

//Set error reporting via the debug constant
if (NIMBUS_DEBUG === 1) {
	error_reporting(E_ERROR);
} elseif (NIMBUS_DEBUG === 2) {
	error_reporting(E_ALL);
} else {
	error_reporting(0);
}

//Set utf8 compatibility
require_once LIBRARY_DIR . 'utf8.php';
ini_set('default_charset', 'UTF-8');
nUtf8::load();

//Check if the installer is present, or if it is installed
if (!file_exists('config.php') &&
	file_exists('installer' . DS . 'index.php')) {
		header('Location: installer/index.php');
}

//Include the bootstrap files
require_once NIMBUS_DIR . 'bootstrap.php';
require_once NIMBUS_DIR . 'common.php';

//Instantiate the front controller abstraction of the kernel
$app = new Nimbus();
$app->init();

//Get the time nimbus started
$app->benchmark('app', START);

//Check if nimbus is being called for a request
if ($app->beingCalled()) {
	switch ($app->request->type) {
		//Request identifiers are prefixed with an underscore ( _ ) to avoid variable collision.
		case "app": //Internal function call to applications
			Loader::system('application');
			Application::launch();
		break;
		case "res": //Internal/External resource loader
			Loader::system('resource');
			Resource::fetch();
		break;
		case "token": //Generate an access token
			new Token();
		break;
		case "rpc": //RPC capabilities
			Loader::system('rpc');
			$rpc = new RPC();
			$rpc->listen();
		break;
		case "data": //Raw SQL Query for JS uses, of course, checks for Access Token to prevent abuse.
			Loader::system('query');
			new Query();
		break;
	}
} else {
	//Generate the base HTML canvas
	$app->canvas();
	//Load the default desktop
	Loader::system('application');
	Application::launch('desktop');
}

//Get the time nimbus stopped, and echo out if allowed
$app->benchmark('app', STOP, NIMBUS_DEBUG);

?>









<div class="log window draggable resizable">
	<div class="handleSE"></div><div class="handleE"></div><div class="handleNE"></div><div class="handleN"></div><div class="handleNW"></div><div class="handleW"></div><div class="handleSW"></div><div class="handleS"></div>
	<div class="window-title">
		<div class="title-icon"></div>
		<div class="title-caption">Nimbus Log</div>
		<div class="title-actions"></div>
	</div>
	<div class="window-inner">
		<div class="window-content-wrapper">
			<div class="window-content-outer">
				<div class="window-content-inner">
					<div class="window-toolbars"><!-- TOOLBAR --></div>
					<pre><?php print_r($app); ?></pre>
					<div class="window-statusbar"></div>
				</div>
			</div>
		</div>
	</div>
</div>