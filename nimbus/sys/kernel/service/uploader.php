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
 * @subpackage:		Nimbus_services
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */
 
class uploaderService extends API {

	public function __construct(){
		parent::__construct();
	}

	public function listen(){
		$path = (!empty($this->request->get['path'])) ? $this->request->get['path']: 'root' . DS . 'Uploads';
		if (!file_exists(USER_DIR . $this->user->username . DS . "drives" . DS . $path . DS)) {
			$path = 'root' . DS . 'Uploads';
		}
		if ($this->user->isLoggedIn() || isset($_GET['PHPSESSID'])) {
			$filename = array();
			$i = 0;
			$upload_name = "file";
			$count = count($_FILES[$upload_name]['name']);
			if ($count > 1) {
				while ($count--) {
					$file[$upload_name]['name'] = $_FILES[$upload_name]['name'][$i];
					$file[$upload_name]['type'] = $_FILES[$upload_name]['type'][$i];
					$file[$upload_name]['tmp_name'] = $_FILES[$upload_name]['tmp_name'][$i];
					$file[$upload_name]['error'] = $_FILES[$upload_name]['error'][$i];
					$file[$upload_name]['size'] = $_FILES[$upload_name]['size'][$i];
					$filename[] = $this->upload($file, $path);
					$i++;
				}
			} else {
				if (is_array($_FILES[$upload_name]['name'])) {
					$file[$upload_name]['name'] = $_FILES[$upload_name]['name'][$i];
					$file[$upload_name]['type'] = $_FILES[$upload_name]['type'][$i];
					$file[$upload_name]['tmp_name'] = $_FILES[$upload_name]['tmp_name'][$i];
					$file[$upload_name]['error'] = $_FILES[$upload_name]['error'][$i];
					$file[$upload_name]['size'] = $_FILES[$upload_name]['size'][$i];
				} else {
					$file[$upload_name]['name'] = $_FILES[$upload_name]['name'];
					$file[$upload_name]['type'] = $_FILES[$upload_name]['type'];
					$file[$upload_name]['tmp_name'] = $_FILES[$upload_name]['tmp_name'];
					$file[$upload_name]['error'] = $_FILES[$upload_name]['error'];
					$file[$upload_name]['size'] = $_FILES[$upload_name]['size'];
				}
				$filename[] = $this->upload($file, $path);
			}
			echo json_encode($filename);
		}
	}

	public function upload($filestream, $path){
		if ($this->user->isLoggedIn() || isset($_GET['PHPSESSID'])) {
			
			if (!isset($this->user->username)) {
				$username = $_GET['username'];
			} else {
				$username = $this->user->username;
			}

			//Check post_max_size (http://us3.php.net/manual/en/features.file-upload.php#73762)
			$POST_MAX_SIZE = ini_get('post_max_size');
			$unit = strtoupper(substr($POST_MAX_SIZE, -1));
			$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));

			if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
				header("HTTP/1.1 500 Internal Server Error"); // This will trigger an uploadError event in SWFUpload
				echo "POST exceeded maximum allowed size.";
			}

		// Settings
			$save_path = USER_DIR . $username . DS . "drives" . DS . $path . DS;
			$upload_name = "file";
			$max_file_size_in_bytes = 2147483647;				// 2GB in bytes
			$extension_whitelist = array("jpg", "gif", "png");	// Allowed file extensions
			$valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';				// Characters allowed in the file name (in a Regular Expression format)
			
		// Other variables	
			$MAX_FILENAME_LENGTH = 260;
			$file_name = "";
			$file_extension = "";
			$uploadErrors = array(
				0=>"There is no error, the file uploaded with success",
				1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini",
				2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
				3=>"The uploaded file was only partially uploaded",
				4=>"No file was uploaded",
				6=>"Missing a temporary folder"
			);


		// Validate the upload
			if (!isset($filestream[$upload_name])) {
				HandleError("No upload found in \$filestream for " . $upload_name);
			} else if (isset($filestream[$upload_name]["error"]) && $filestream[$upload_name]["error"] != 0) {
				HandleError($uploadErrors[$filestream[$upload_name]["error"]]);
			} else if (!isset($filestream[$upload_name]["tmp_name"]) || !@is_uploaded_file($filestream[$upload_name]["tmp_name"])) {
				HandleError("Upload failed is_uploaded_file test.");
			} else if (!isset($filestream[$upload_name]['name'])) {
				HandleError("File has no name.");
			}
			
		// Validate the file size (Warning: the largest files supported by this code is 2GB)
			$file_size = @filesize($filestream[$upload_name]["tmp_name"]);
			if (!$file_size || $file_size > $max_file_size_in_bytes) {
				HandleError("File exceeds the maximum allowed size");
			}
			
			if ($file_size <= 0) {
				HandleError("File size outside allowed lower bound");
			}


		// Validate file name (for our purposes we'll just remove invalid characters)
			$file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($filestream[$upload_name]['name']));
			if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {
				HandleError("Invalid file name");
			}


		// Validate that we won't over-write an existing file
			if (file_exists($save_path . $file_name)) {
				$file_name = generateHash() . '_' . $file_name;
				HandleError("File with this name already exists");
			}

		// Validate file extension
			$path_info = pathinfo($filestream[$upload_name]['name']);
			$file_extension = $path_info["extension"];
			$is_valid_extension = false;
			foreach ($extension_whitelist as $extension) {
				if (strcasecmp($file_extension, $extension) == 0) {
					$is_valid_extension = true;
					break;
				}
			}
			if (!$is_valid_extension) {
				HandleError("Invalid file extension");
			}

		// Validate file contents (extension and mime-type can't be trusted)
			/*
				Validating the file contents is OS and web server configuration dependant.  Also, it may not be reliable.
				See the comments on this page: http://us2.php.net/fileinfo
				
				Also see http://72.14.253.104/search?q=cache:3YGZfcnKDrYJ:www.scanit.be/uploads/php-file-upload.pdf+php+file+command&hl=en&ct=clnk&cd=8&gl=us&client=firefox-a
				 which describes how a PHP script can be embedded within a GIF image file.
				
				Therefore, no sample code will be provided here.  Research the issue, decide how much security is
				 needed, and implement a solution that meets the needs.
			*/


		// Process the file
			/*
				At this point we are ready to process the valid file. This sample code shows how to save the file. Other tasks
				 could be done such as creating an entry in a database or generating a thumbnail.
				 
				Depending on your server OS and needs you may need to set the Security Permissions on the file after it has
				been saved.
			*/
			if (!@move_uploaded_file($filestream[$upload_name]["tmp_name"], $save_path.$file_name)) {
				HandleError("File could not be saved.");
			}
			
			$y = explode(str_replace('\\..\\', '', USER_DIR),  $save_path . $file_name);
			return array(				
					'name' => $file_name,
					'type' => $filestream[$upload_name]['type'],
					'size' => $file_size,
					'path' => $y[1]
				);

		}
	}

}

function HandleError($message) {
	//echo $message;
}

$uploader = new uploaderService(); 
$uploader->listen();
 
?>