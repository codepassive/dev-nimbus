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

class Resource extends API {

	public $path = null;

	public function __construct($where = null){
		parent::__construct();
		$this->path = ($where) ? $where: $this->request->items[0]['value'];
		$this->fetch();
	}
	
	public function fetch(){
		global $mimes;
		$where = $this->path;
		$path = explode("://", $where);
		Loader::shell('mimes');
		switch ($path[0]) {
			case "http":
			case "https":
			case "ftp":
				header('Location: ' . $where);
			break;
			case "app":
				$appname = explode("/", $path[1]);
				$appname = $appname[0];
				$id = $this->user->current('id');
				$id = ($id) ? $id: null;
				if ($this->user->isAllowed($appname)) {
					if (file_exists(APPLICATION_DIR . $path[1])) {
						$info = pathinfo(APPLICATION_DIR . $path[1]);
						$ext = $info['extension'];
						if (array_key_exists($ext, $mimes)) {
							if (is_array($mimes[$ext])) {
								$mimetype = explode("/", $mimes[$ext][0]);
								$mimetype = $mimetype[0];
							} else {
								$mimetype = explode("/", $mimes[$ext]);
								$mimetype = $mimetype[0];
							}
							$path = APPLICATION_DIR . $path[1];
							switch ($mimetype) {
								case "application":
									header('Content-Description: File Transfer');
									header('Content-Type: application/octet-stream');
									header('Content-Disposition: attachment; filename=' . basename($path));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
									header('Pragma: public');
									header('Content-Length: ' . filesize($path));
									readfile($path);
								break;
								case "image":
									$img = explode("/", $mimes[$ext][0]);
									$imgtype = $img[1];
									switch ($imgtype) {
										case "gif":
											$image = @imagecreatefromgif($path);
											header('Content-Type: ' . $mimes[$ext][0]);
											imagegif($image);
										break;
										case "png":
											$image = @imagecreatefrompng($path);
											imagealphablending($image, true);
											imagesavealpha($image, true);
											header('Content-Type: ' . $mimes[$ext][0]);
											imagepng($image);
										break;
										case "jpeg":
										default:
											$image = @imagecreatefromjpeg($path);
											header('Content-Type: ' . $mimes[$ext][0]);
											imagejpeg($image);
										break;
									}
									imagedestroy($image);
								break;
								case "text":
									header("Content-type: " . $mimes[$ext]);
									echo file_get_contents($path);
								break;
							}
						}
					}
				}
			break;
			case "user":
				if ($this->user->isLoggedIn()) {
					$username = $this->user->current('username');
					$path = USER_DIR . $username . DS . 'drives' . DS . $path[1];
					if (file_exists($path)) {
						$info = pathinfo($path);
						$ext = $info['extension'];
						if (array_key_exists($ext, $mimes)) {
							if (is_array($mimes[$ext])) {
								$mimetype = explode("/", $mimes[$ext][0]);
								$mimetype = $mimetype[0];
							} else {
								$mimetype = explode("/", $mimes[$ext]);
								$mimetype = $mimetype[0];
							}
							switch ($mimetype) {
								case "application":
									header('Content-Description: File Transfer');
									header('Content-Type: application/octet-stream');
									header('Content-Disposition: attachment; filename=' . basename($path));
									header('Content-Transfer-Encoding: binary');
									header('Expires: 0');
									header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
									header('Pragma: public');
									header('Content-Length: ' . filesize($path));
									readfile($path);
								break;
								case "image":
									$img = explode("/", $mimes[$ext][0]);
									$imgtype = $img[1];
									switch ($imgtype) {
										case "gif":
											$image = @imagecreatefromgif($path);
											header('Content-Type: ' . $mimes[$ext][0]);
											imagegif($image);
										break;
										case "png":
											$image = @imagecreatefrompng($path);
											imagealphablending($image, true);
											imagesavealpha($image, true);
											header('Content-Type: ' . $mimes[$ext][0]);
											imagepng($image);
										break;
										case "jpeg":
										default:
											$image = @imagecreatefromjpeg($path);
											header('Content-Type: ' . $mimes[$ext][0]);
											imagejpeg($image);
										break;
									}
									imagedestroy($image);
								break;
								case "text":
									header("Content-type: " . $mimes[$ext]);
									echo file_get_contents($path);
								break;
							}
						}
					}
				}
			break;
			case "public":
				if (file_exists(PUBLIC_DIR . $path[1])) {
					$path =  $this->config->appurl . 'public/' . $path[1];
					header('Location: ' . $path);
				}
			break;
			case "img":
			case "image":
				$type = explode(".", $path[1]);
				$type = $type[(count($type) - 1)];
				$path =  $this->config->appurl . 'public/resources/images/' . $path[1];
				switch (strtolower($type)) {
					case "gif":
						$image = @imagecreatefromgif($path);
						header('Content-Type: image/' . strtolower($type));
						imagegif($image);
					break;
					case "png":
						$image = @imagecreatefrompng($path);
						imagealphablending($image, true);
						imagesavealpha($image, true);
						header('Content-Type: image/' . strtolower($type));
						imagepng($image);
					break;
					case "jpeg":
					case "jpg":
					default:
						$image = @imagecreatefromjpeg($path);
						header('Content-Type: image/' . strtolower($type));
						imagejpeg($image);
					break;
				}
				imagedestroy($image);
			break;
			case "script":
			case "js":
				header('Content-type: text/javascript');
				if (file_exists(SCRIPT_DIR . $path[1])) {
					echo file_get_contents(SCRIPT_DIR . $path[1]);
				}
				return false;
			break;
			case "css":
				header('Content-type: text/css');
				if (file_exists(SKIN_DIR . $path[1])) {
					echo file_get_contents(SKIN_DIR . $path[1]);
				}
				return false;			
			break;
		}
	}
	
}
?>