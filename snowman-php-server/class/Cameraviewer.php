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
 * View a single camera
 */

/**
 * Class for single cameraviewer
*/
class Cameraviewer {
	private $camera;
	private $image = "";

	/**
	 * Constructor
	 * @link camera
	 * @param camera $camera a camera instance
	 */
	public function cameraviewer($camera) {
		$this->camera = $camera;
	}

	/**
	 * Get the camera object.
	 * @link camera
	 * @return camera the {@link camera} instance
	 */
	public final function getCamera() {
		return $this->camera;
	}

	/**
	 * Get the image resource.
	 * @return resource the {@link image} resource
	 */
	public final function getImage() {
		return $this->image;
	}

	/**
	 * Load a specific image from filename
	 * @param string $filename
	 * @param bool $fileerrormessage
	 * @return void
	 */
	public final function loadSpecificImage($filename,$fileerrormessage=true) {
		//destroy image, if allready exist
		if($this->getImage()) {
			imagedestroy($this->getImage());
		}

		$givenwidth = $width = $this->getCamera()->getWidth();
		$givenheight = $height = $this->getCamera()->getHeight();

		$this->image  = imagecreatetruecolor(
			$givenwidth,
			$givenheight
		);

		$imagefromfile = false;
		if($filename) {
			$imagefromfile = @imagecreatefromjpeg($filename);
		}

		if(!$filename || !$imagefromfile) {
			if($fileerrormessage) {
				$this->createFileErrorMessage($filename);
			}
		} else {
			$filewidth = imagesx($imagefromfile);
			$fileheight = imagesy($imagefromfile);
			$fileratio = $filewidth / $fileheight;

			if($width/$height > $fileratio) {
				$width = $height*$fileratio;
			} else {
				$height = $width/$fileratio;
			}

			imagecopyresampled(
				$this->getImage(),
				$imagefromfile,
				0,
				0,
				0,
				0,
				$width,
				$height,
				$filewidth,
				$fileheight
			);
		}
	}

	/**
	 * Load latest image from camera
	 * @param bool $fileerrormessage
	 * @return filename of loaded image
	 */
	public final function loadImage($fileerrormessage=true) {
		$filename = $this->getCamera()->getImagesLast();
		if($filename) {
			$image = @imagecreatefromjpeg($filename);
			$this->image = $image;
		}
		$this->loadSpecificImage($filename,$fileerrormessage);
		return $filename;
	}

	/**
	 * Create a message in the cameraviewer image
	 * @param string $msg the message
	 * @param integer $x1 start position x
	 * @param integer $y1 start position y
	 * @param integer $x2 end position x
	 * @param integer $y2 end position y
	 * @return void
	 */
	public final function createMessage($msg,$x1,$y1,$x2,$y2) {
		$image = $this->getImage();
		$bgc = imagecolorallocate($image, 255, 255, 255);
		$tc  = imagecolorallocate($image, 0, 0, 0);
		imagefilledrectangle($image, $x1, $y1, $x2, $y2, $bgc);
		imagestring($image, 1, $x1+2, $y1+1, $msg, $tc);
	}

	/**
	 * Create a file error message in the cameraviewer image.
	 * @param string $filename the path
	 * @return void
	 */
	private final function createFileErrorMessage($filename) {
		$msg = "[No file for view (Buffer underrun)]";
		if($filename) {
			$msg = 'Error loading file: '.$filename;
		}
		$yOffset=20;
		$this->createMessage(
			$msg,
			0,
			$yOffset,
			$this->getCamera()->getWidth(),
			$yOffset+10
		);
	}

	/**
	 * Create a watermark in the cameraviewer image.
	 * This brand the program name and date in the image.
	 * @param integer $POSIXMillis a optional given timestamp e.g.
	 * for upload, unit [ms]
	 * @param integer $yOffset a horizontal offset for the message
	 * @return string the watermark string
	 */
	public final function createWatermark($POSIXMillis=false, $yOffset=0) {
		if(!$POSIXMillis) {
			$POSIXMillis = millitime();
		}

		$posix = getPosixFromMillitime($POSIXMillis);
		$millis = getMillisFromMillitime($POSIXMillis);

		$date = date("l, Y-m-d H:i:s",$posix);
		$millistring = str_pad((string)$millis,3,"0");
		$date .= " [" . $millistring . ']';
		$date .= " " . date_default_timezone_get();

		$msg = $this->getCamera()->getTopTextBranding();
		$msg .= $date;

		$this->createMessage(
			$msg,
			0,
			$yOffset,
			$this->getCamera()->getWidth(),
			10+$yOffset
		);
		return $msg;
	}

	/**
	 * Create a bottom text branding in the cameraviewer image.
	 * This brand the configurated string in the image.
	 * @return void
	 */
	public final function createBottomTextBranding() {
		$msg = $this->getCamera()->getBottomTextBranding();
		$this->createMessage(
			$msg,
			0,
			$this->getCamera()->getHeight()-10,
			$this->getCamera()->getWidth(),
			$this->getCamera()->getHeight()
		);
	}

	/**
	 * Get the content type from the cameraviewer image.
	 * @return string the content type
	 */
	public final function getContentType() {
		return 'image/jpeg';
	}

	/**
	 * Destructor
	 * @link $image
	 * @return void
	 */
	function __destruct() {
		$image = $this->getImage();
		if($image) {
			imagedestroy($image);
		}
	}
}

