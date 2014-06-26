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
 * Class for AJAX downloadarchive request.
 */
class Downloadarchive
{
    /**
     * The snowman instance.
     * @link snowman
     * @var snowman
     */
    private $snowman;

    /**
     * JSON request from client.
     */
    private $JSONRequest;

    /**
     * The camera handle.
     * @var Camera
     */
    private $camera;

    /**
     * The safe archive path.
     * @var array
     */
    private $safepath;

    /**
     * Constructor
     * {@link JSONRequest}
     * @param \stdClass $JSONRequest
     * @return void
     */
    public function __construct($snowman, $JSONRequest)
    {
        $this->snowman = $snowman;
        $this->JSONRequest = $JSONRequest;
        $this->safepath = array();

        /** @noinspection PhpUndefinedFieldInspection */
        if (isset($this->JSONRequest->downloadArchive)) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->requestNodeDownloadArchive($this->JSONRequest->downloadArchive);
        }
    }

    /**
     * Parse downloadArchive node.
     * @return void
     */
    private final function requestNodeDownloadArchive($node)
    {
        /*
        //var_dump($node);
        object(stdClass)#16 (1) {
          ["path"]=>
          array(6) {
            [0]=>
            string(7) "camera0"
            [1]=>
            string(4) "2013"
            [2]=>
            string(2) "07"
            [3]=>
            string(2) "25"
            [4]=>
            string(2) "15"
            [5]=>
            string(21) "20130725_15_30_01.mp4"
          }
        }
        */
        global $isloginok;
        $ok = false;
        $path = null;
        $cameraname = null;
        $this->camera = null;

        if (isset($node->path)) {
            $path = $node->path;
            $cameraname = array_shift($path);
        } else {
            //invalid
            echo("invalid 0");
            $this->safeClean();
            return;
        }


//TODO: TEST OVERFLOW UNDERRUN ERROR AND CATCH THE EXCEPTION
        if ($isloginok) {

            $accessgrantedcameras = camera::getAccessableCamerasByUser(
                $this->snowman->getCameras(),
                $this->snowman->getLoginUser()
            );

            foreach ($accessgrantedcameras as $camera) {
                /** @var Camera $camera */
                if ($camera->getCCamera()->getName() == $cameraname) {
                    $this->camera = $camera;
                    break;
                }
            }

        } else {
            //login incorrect
            echo("invalid 1");
            $this->safeClean();
            return;
        }

        if (isset($this->camera)) {

            $archiveListing = $camera->getArchiveListing();
            /*
            //var_dump($archiveListing);
            array(1) {
              [2013]=>
              array(3) {
                ["07"]=>
                array(7) {
                  [25]=>
                  array(9) {
                    [15]=>
                    array(7) {
                      [0]=>
                      string(21) "20130725_15_25_29.mp4"
                      [1]=>
                      string(21) "20130725_15_30_01.mp4"
                      [2]=>
                      string(21) "20130725_15_35_01.mp4"
                      [3]=>
                      string(21) "20130725_15_40_01.mp4"
                      [4]=>
                      string(21) "20130725_15_45_02.mp4"
                      [5]=>
                      string(21) "20130725_15_50_01.mp4"
                      [6]=>
                      string(21) "20130725_15_55_01.mp4"
                    }
            */
            //ignore the last element, we check it with in_array
            $nextNode = $archiveListing;
            for ($i = 0; $i < count($path) - 1; $i++) {
                if (!isset($nextNode[$path[$i]])) {
                    //echo("--------\n");
                    //var_dump($path[$i]);
                    //invalid
                    echo("invalid 2");
                    $this->safeClean();
                    return;
                } else {
                    $nextNode = $nextNode[$path[$i]];
                    $this->safepath[] = $path[$i];
                }
            }

            $lastElement = count($path) - 1;
            //check the last element, nextNode should be the last node
            if (!in_array($path[$lastElement], $nextNode)) {
                //invalid
                echo("invalid 3");
                $this->safeClean();
                return;
            } else {
                $this->safepath[] = $path[$lastElement];
                //all fine
            }
        } else {
            //no camera found
            $this->safeClean();
            return;
        }
    }

    /**
     * Security reason.
     * @return void
     */
    private final function safeClean()
    {
        $this->camera = null;
        $this->safepath = null;
    }

    /**
     * Get the camera.
     * @return Camera the camera handle.
     */
    private final function getCamera()
    {
        return $this->camera;
    }

    /**
     * Get the safe path of the archive file.
     * @return array the safe path
     */
    private final function getSafepath()
    {
        return $this->safepath;
    }

    /**
     * Indicates whether the request is correct.
     * @return true if the download request is correct, otherwise false
     */
    public final function isSafeArchiveRequest()
    {
        if (!isset($this->camera) || !isset($this->safepath)) {
            return false;
        }
        if (count($this->safepath) < 1) {
            return false;
        }
        return true;
    }

    /**
     * Indicates whether the request is correct.
     * @return true if the download request is correct, otherwise false
     */
    public final function sendArchive()
    {
        $file = $this->getCamera()->getCCamera()->getArchive()->getDir();
        foreach ($this->getSafepath() as $sub) {
            $file .= DIRECTORY_SEPARATOR;
            $file .= $sub;
        }

        if (!file_exists($file)) {
            return;
        }

        if ($this->snowman->getCSnowman()->isXSendFile()) {
            header("X-Sendfile: $file");
            header("Content-type: application/octet-stream");
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            ob_clean();
            flush();
            exit;
        } else {
            // do a range-download only for devices that supports byte-ranges
            if (isset($_SERVER['HTTP_RANGE'])) {
                rangeDownload($file);
            } else {
                // fallback download
                set_time_limit(0);
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                ob_clean();
                flush();
                readfile($file);
                exit;
            }
        }
    }

}

