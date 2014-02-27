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

namespace net\ladenthin\snowman\phpserver;

/**
 * Writer class for single camerawriter
 */
class Camerawriter extends Cameraviewer
{

    /**
     * Constructor
     * @link camera
     * @param camera $camera a camera instance
     */
    public function __construct($camera)
    {
        parent::__construct($camera);
    }

    /**
     * Decode a uploaded filename.
     * @param string $filename a filename
     * @return mixed array with decoded filename or false if wrong format
     */
    public static final function decodeFilename($filename)
    {
        $fsFilename = array();
        $fs = '_';
        $extFs = '.';
        $fsCount = substr_count($filename, $fs);
        // we need a format like camera1_1234567890_1_00000000.jpeg
        // count _ for a minimum size of field
        // the camera name could contain variable
        if ($fsCount < 3) {
            return false;
        }

        //get the extension
        $extP = strripos($filename, $extFs);
        $fsFilename['extension'] = substr($filename, $extP + 1);

        $p = strripos($filename, $fs);
        // add one to skip fs
        $fsFilename['filenumber'] = substr($filename, $p + 1);
        //cut substr from filename, add 1 to skip fs
        $filename = substr($filename, 0, -(strlen($fsFilename['filenumber']) + 1));

        //now it should look like camera1_1234567890_1
        //repeat the procedure to get the fps
        $p = strripos($filename, $fs);
        // add one to skip fs
        $fsFilename['fps'] = substr($filename, $p + 1);
        //cut substr from filename, add 1 to skip fs
        $filename = substr($filename, 0, -(strlen($fsFilename['fps']) + 1));

        //now it should look like camera1_1234567890
        //repeat the procedure to get the POSIX time
        $p = strripos($filename, $fs);
        // add one to skip fs
        $fsFilename['posix'] = substr($filename, $p + 1);
        //cut substr from filename, add 1 to skip fs
        $filename = substr($filename, 0, -(strlen($fsFilename['posix']) + 1));

        //now it should look like camera1
        $fsFilename['cameraname'] = $filename;

        //intval the numbers
        $fsFilename['filenumber'] = intval($fsFilename['filenumber']);
        $fsFilename['fps'] = intval($fsFilename['fps']);
        $fsFilename['posix'] = intval($fsFilename['posix']);

        //return the decoded filename
        return $fsFilename;
    }

    /**
     * Decode a uploaded filename to POSIX millis (POSIX time * 1000).
     * TODO: Only run on 64bit machines?
     * @param array $decodedFilename a decoded filename
     * @return int with decoded POSIX millis
     */
    public static final function decodedFilenameToPOSIXMillis(
        $decodedFilename
    )
    {
        $milli = 1000;
        //picture each milliseconds
        $pem = ((1 / $decodedFilename['fps']) * $milli);
        $POSIXOffset = $pem * $decodedFilename['filenumber'];

        $POSIXMillis = $decodedFilename['posix'] * $milli;
        $POSIXMillis += $POSIXOffset;
        $POSIXMillis = intval(floor($POSIXMillis));
        return $POSIXMillis;
    }

    /**
     * Write the image to file
     * @param string $filename a filename
     * @return bool Returns true on success or false on failure.
     */
    public final function writeToFile($filename)
    {
        $image = $this->getImage();
        nis_dir_mkdir($this->getCamera()->getCCamera()->getDir());
        return imagejpeg($image, $this->getCamera()->getCCamera()->getDir() . '/' . $filename);
    }

    /**
     * Destructor
     * @return void
     */
    function __destruct()
    {
        parent::__destruct();
    }
}

