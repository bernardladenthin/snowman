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
use net\ladenthin\snowman\phpserver\Camera;
use net\ladenthin\snowman\phpserver\Camerawriter;

$response = new stdClass();
$response->imageUpload = new stdClass();

/**
 * File for cameraupload
 * @subpackage actions
 */
$uploadMethod = $uploadMethodWrong = -1;
$uploadMethodFile = 0;
$uploadMethodRequest = 1;

if (!$isloginok) {
    $response->imageUpload->success = false;
    $response->imageUpload->wrongLogin = true;
} else {
    if (
        isset($_REQUEST[$uploadNameCameraname])
        && isset($_FILES[$uploadNameCameraimage])
        && (
            $_FILES[$uploadNameCameraimage]['type'] == 'image/jpeg'
            || $_FILES[$uploadNameCameraimage]['type'] == 'image/jpg'
            || $_FILES[$uploadNameCameraimage]['type'] == 'application/octet-stream'
        )
    ) {
        $uploadMethod = $uploadMethodFile;
    } else if (
        isset($_REQUEST[$uploadNameCameraname])
        && isset($_REQUEST[$uploadNameCameraimage])
        && isset($_REQUEST[$uploadNameFilename])
    ) {
        $uploadMethod = $uploadMethodRequest;
    } else {
        $response->imageUpload->success = false;
        $response->imageUpload->wrongRequest = true;
    }
}

if ($isloginok && ($uploadMethod != $uploadMethodWrong)) {
    $cameraname = urldecode($_REQUEST[$uploadNameCameraname]);
    $camera = Camera::getObjByName($snowman->getCameras(), $cameraname);

    $rawFileName = "";
    $tmpFilePath = "";

    if ($uploadMethod == $uploadMethodFile) {
        $rawFileName = $_FILES[$uploadNameCameraimage]['name'];
        $tmpFilePath = $_FILES[$uploadNameCameraimage]['tmp_name'];
    } else if ($uploadMethod == $uploadMethodRequest) {
        $rawFileName = $_REQUEST[$uploadNameFilename];
        $tmpFileHandle = tmpfile();
        fwrite($tmpFileHandle, $_REQUEST[$uploadNameCameraimage]);
        fseek($tmpFileHandle, 0);
        $tmpFilePath = array_search(
            'uri',
            @array_flip(stream_get_meta_data($tmpFileHandle))
        );
    }

    if (is_object($camera)) {
        $camerawriter = new Camerawriter($camera);

        $camerawriter->loadSpecificImage($tmpFilePath);

        $form = $camerawriter->decodeFilename($rawFileName);

        if ($camerawriter->getCamera()->getCCamera()->isLogRawCameraUpload()) {
            $logmsg = "Uploaded\nRAW request: " . $rawFileName . ";";
        }

        if (is_array($form)) {
            /**
             * The upload format tells us only the begin, the fps an the
             * current one, calculate the correct time this would be rounded
             * and could have a multuple appearance if the fps > 1
             */
            $posixMillis = Camerawriter::decodedFilenameToPOSIXMillis($form);

            if ($camerawriter->getCamera()->getCCamera()->isLogRawCameraUpload()) {
                $logmsg .= "\nposixMillis: $posixMillis";
            }

            $watermarkMsg = $camerawriter->createWatermark($posixMillis);

            if ($camerawriter->getCamera()->getCCamera()->isLogRawCameraUpload()) {
                $logmsg .= "\nwatermarkMsg: $watermarkMsg\n\n";
            }

            $camerawriter->createBottomTextBranding();
            //write the image with original filename from upload
            //check before write to correct file name
            $fileinfo = pathinfo($rawFileName);

            if (
            in_array(
                $fileinfo['extension'],
                $camerawriter->getCamera()->getCCamera()->getImageExtensions()
            )
            ) {
                $filename = "";
                if ($camera->getCCamera()->isCamerawriterFormatPosixMillis()) {
                    //use POSIX millis format
                    $filename =
                        $form['cameraname'] .
                        '_' .
                        (string)$posixMillis .
                        '.' .
                        $form['extension'];
                } else {
                    //use original filename
                    $filename = $rawFileName;
                }

                $success = $camerawriter->writeToFile($filename);


                if ($camerawriter->getCamera()->getCCamera()->isLogRawCameraUpload()) {
                    $camerawriter->getCamera()->writeLog($logmsg);
                }

                if ($success) {
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
    } else {
        $response->imageUpload->success = false;
        $response->imageUpload->unknownCamera = true;
    }
} else {
    $response->imageUpload->success = false;
    $response->imageUpload->unknownError = true;
}

$json = json_encode($response);

if ($jsonpCallback) {
    $json = $jsonpCallback . "(" . $json . ")";
}
echo $json;

