<?php
/**
 * snowman-php-server - PHP script to run a snowman server.
 * http://code.google.com/p/snowman/
 *
 * Copyright (C) 2013 Bernard Ladenthin <bernard@ladenthin.net>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Main file for output a camera image.
 */

require_once('globalconfig.php');

$timeMillis = millitime();
$posix = getPosixFromMillitime($timeMillis);
$millis = getMillisFromMillitime($timeMillis);

//cache page (in seconds)
$dauer = 60 * 24 * 30 * 60; //=30 days
$exp_gmt = gmdate("D, d M Y H:i:s", $posix + $dauer) ." GMT";
$mod_gmt = gmdate("D, d M Y H:i:s", getlastmod()) ." GMT";

header("Expires: " . $exp_gmt);
header("Last-Modified: " . $mod_gmt);
header("Cache-Control: private, max-age=" . $dauer);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Expose-Headers: '.
	'snowman-timeseconds, snowman-timemillis, snowman-filename');

if(isset($_REQUEST['name']) && $isloginok) {
	$name = urldecode($_REQUEST['name']);
	$camera = camera::getObjByName($snowman->getCameras(), $name);
	if(is_object($camera)) {
		$cameraviewer = new Cameraviewer($camera);
		$filename = $cameraviewer->loadImage();
		//use a offset of 10 pixels for a view branding
		$cameraviewer->createWatermark($timeMillis,10);
		$cameraviewer->createImageBranding();

		header("snowman-timeseconds: " . $posix);
		header("snowman-timemillis: " . $millis);
		header("snowman-filename: " . $filename);

		if($_REQUEST['base64']=='true') {
			ob_start();
			imagejpeg($cameraviewer->getImage());
			$data = ob_get_clean();
			header('Content-Type: text/txt');
			echo base64_encode($data);
		} else {
			header('Content-Type: '.$cameraviewer->getContentType());
			imagejpeg($cameraviewer->getImage());
		}
		unset($cameraviewer);
		exit;
	}
}

