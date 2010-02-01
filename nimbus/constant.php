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
/**
 * @ignore
 *   This file contains constants used throughout the system. If you would
 *   like to add a constant used in an application/plugin or a custom module,
 *   please declare it on the file it is needed or in the file named common.php.
 *   It is important that you do this to avoid future compatibility issues as
 *   this file will be updated as common.php wont.
 */

//Benchmark Constants
define('START', 'start');
define('STOP', 'stop');

//Security Level
define('SECURITY_HIGH', 900);
define('SECURITY_NORMAL', 1800);
define('SECURITY_LOW', 3600);

//RPC Definitions
define('RPC_METHOD_REST', 'REST');
define('RPC_METHOD_SOAP', 'SOAP');
define('RPC_METHOD_XMLRPC', 'XMLRPC');

//Log Files
define('ERROR_LOG_FILE', 'logs' . DS . 'error');
define('DEBUG_LOG_FILE', 'logs' . DS . 'debug');

//Defaults
define('CONFIG_LANGUAGE', 'en-us');
define('CONFIG_TIMEZONE', 'asia/manila');
define('CONFIG_AUTOUPDATE', 1);
define('CONFIG_UPDATESERVER', 'http://thesis/apps/');
define('CONFIG_NAMESPACE', 'http://ns.nimbus.iamjamoy.com/');
define('CONFIG_APPURL', 'http://thesis/');
define('CONFIG_APPNAME', 'Nimbus');
define('CONFIG_ALLOWREGISTRATION', 0);
define('CONFIG_SMTP_URL', '');
define('CONFIG_SMTP_LOGIN', '');
define('CONFIG_SMTP_PASSWORD', '');
define('CONFIG_SMTP_PORT', 110);
define('CONFIG_ROOT_USER', 1);
define('CONFIG_DATE_FORMAT', 'F j, Y');
define('CONFIG_TIME_FORMAT', 'H:i a');
define('CONFIG_PINGSERVER', 'http://ping.iamjamoy.com/');
define('CONFIG_DEFAULT_THEME', 'default');
define('CONFIG_SALT', '965dbaac085fc891bfbbd4f9d145bbc8');
define('CONFIG_ACCOUNTBRIDGING', '1');
define('CONFIG_TRANSPORT', 'xmlrpc');
define('CONFIG_PARTITION', 1073741824);
define('CONFIG_PARTITION_PER_USER', 1073741824);
define('CONFIG_REFRESH_RATE', 5);
define('CONFIG_BACKGROUND_WALLPAPER', 'img://wallpapers/default.jpg');
define('CONFIG_INIT_MODULES', 'a:0:{}');
define('CONFIG_INIT_SERVICES', 'a:0:{}');
define('CONFIG_MULTIUSER', 0);
define('CONFIG_CACHE', 0);
define('CONFIG_INIT_SCRIPTS', 'a:0:{}');
define('CONFIG_SECURITY', 900);
define('CONFIG_LANGUAGES', 'a:1:{s:5:"en-us";s:10:"English-US";}');
define('CONFIG_APPLICATIONS', '');
define('CONFIG_THEMES', 'a:1:{i:0;s:7:"default";}');
define('CONFIG_FONT_SIZE', 100);

?>