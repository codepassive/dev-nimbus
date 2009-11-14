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
 * @subpackage:		Nimbus_language
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */
/**
 * Errors
 * errors are in english to avoid confusion
 */
$language['error_log'] = '%s on %s:%d <strong>[%d]</strong>';
//00xA - Requirements
$language['error_000A'] = 'Nimbus requires PHP 5.3 or later to run properly.';
$language['error_001A'] = 'Your PHP configuration should support PDO/Sqlite.';
$language['error_002A'] = 'Your Installation folder and the .nimbus data folder should have at least 0755 permission to work properly.';
$language['error_003A'] = 'Your PHP configuration should support file uploads.';
//00xB - Optional Checks
$language['error_000B'] = 'MBSTRING support not detected. MBSTRING will be used along with UTF-8 encoding.';
$language['error_001B'] = 'FTP support not detected. FTP will be used by most project management programs.';
$language['error_002B'] = 'JSON support not detected. JSON will be used by nimbus internal and external API calls.';
$language['error_003B'] = 'SOAP support not detected. SOAP will be used by most project management programs.';
$language['error_004B'] = 'XML support not detected. XML is the most common way of transport, Applications might use this feature.';
$language['error_005B'] = 'XMLRPC support not detected. XMLRPC will be used by most project management programs.';
$language['error_006B'] = 'ZIP support not detected. File archiving will not be available.';
$language['error_007B'] = 'ZLIB support not detected. GZIP comperssion will not be available.';
$language['error_008B'] = 'Native Mail function disabled.';
//00xC - Loader
$language['error_000C'] = 'Loader cannot find the file %s. Make sure you are loading the correct file';
//00xD - Database
$language['error_000D'] = 'You have supplied an invalid Connection String or Configuration Array.';
$language['error_001D'] = 'You haven\'t opened a database yet.';
$language['error_002D'] = 'You have a malformed query, DBO::query() cannot continue. This is the error: %s';
$language['error_003D'] = 'You are inserting nothing to database table %s.';
$language['error_004D'] = 'You are updating nothing to database table %s.';

?>