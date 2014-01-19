<?php
/**
 * snowman-php-server - PHP script to run a snowman server.
 * http://code.google.com/p/snowman/
 *
 * Copyright (C) 2013 Bernard Ladenthin <bernard.ladenthin@gmail.com>
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
 * Main file to receive a camera image.
 */
require_once('globalconfig.php');

$response = new stdClass();
$response->imageUpload = new stdClass();

/**
 * File for cameraupload
 * @subpackage actions
 */
if(
		$isloginok
	&&	isset($_REQUEST[$camerauploadNameParameter])
	&&	isset($_FILES[$camerauploadFileParameter])
	&&	(
			$_FILES[$camerauploadFileParameter]['type'] == 'image/jpeg'
		||	$_FILES[$camerauploadFileParameter]['type'] == 'image/jpg'
	)
) {
	$cameraname = urldecode($_REQUEST[$camerauploadNameParameter]);
	$camera = camera::getObjByName($snowman->getCameras(), $cameraname);
	if(is_object($camera)) {
		$camerawriter = new Camerawriter($camera);

		$camerawriter->loadSpecificImage(
			$_FILES[$camerauploadFileParameter]['tmp_name']
		);

		$form = $camerawriter->decodeFilename(
			$_FILES[$camerauploadFileParameter]['name']
		);

		if($camerawriter->getCamera()->getLogRawCameraUpload()) {
			$logmsg = "Uploaded\nRAW request: ".$_FILES[$camerauploadFileParameter]['name'].";";
		}

		if(is_array($form)) {
			/**
			 * The upload format tells us only the begin, the fps an the
			 * current one, calculate the correct time this would be rounded
			 * and could have a multuple appearance if the fps > 1
			 */
			$posixMillis = Camerawriter::decodedFilenameToPOSIXMillis($form);

			if($camerawriter->getCamera()->getLogRawCameraUpload()) {
				$logmsg .= "\nposixMillis: $posixMillis";
			}

			$watermarkMsg = $camerawriter->createWatermark($posixMillis);

			if($camerawriter->getCamera()->getLogRawCameraUpload()) {
				$logmsg .= "\nwatermarkMsg: $watermarkMsg\n\n";
			}

			$camerawriter->createBottomTextBranding();
			//write the image with original filename from upload
			//check before write to correct file name
			$fileinfo = pathinfo($_FILES[$camerauploadFileParameter]['name']);
			if(
				in_array(
					$fileinfo['extension'],
					$camerawriter->getCamera()->getImageExtensions()
				)
			) {
				$filename = "";
				if($camera->getCamerawriterFormatPosixMillis()) {
					//use POSIX millis format
					$filename =
					$form['cameraname'] .
					'_' .
					(string)$posixMillis .
					'.' .
					$form['extension'];
				} else {
					//use original filename
					$filename = $_FILES[$camerauploadFileParameter]['name'];
				}

				$success = $camerawriter->writeToFile($filename);


				if($camerawriter->getCamera()->getLogRawCameraUpload()) {
					$camerawriter->getCamera()->writeLog($logmsg);
				}

				if($success) {
					unset($camerawriter);
					$response->imageUpload->success = true;
				} else {
					$response->imageUpload->success = false;
					$response->imageUpload->writeToFileError = true;
				}
			} else {
				$response->imageUpload->success = false;
				$response->imageUpload->unknownFileExtension = true;
			}
		} else {
			$response->imageUpload->success = false;
			$response->imageUpload->unknownFileNameFormat = true;
		}
	}
	else {
		$response->imageUpload->success = false;
		$response->imageUpload->unknownCamera = true;
	}
} else {
	$response->imageUpload->success = false;
	$response->imageUpload->wrongRequest = true;
}

$json = json_encode($response);

if($jsonpCallback) {
	$json = $jsonpCallback."(".$json.")";
}
echo $json;

