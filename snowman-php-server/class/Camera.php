<?php
/**
 * snowman-php-server - PHP script to run a snowman server.
 * https://github.com/bernardladenthin/snowman
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
 * Class for single camera
 */
class Camera
{

    /** @var CCamera $ccamera */
    private $cCamera;

    /**
     * Constructor.
     * @param \stdClass $stdClass Configuration object.
     */
    public function __construct($stdClass)
    {
        $this->cCamera = new CCamera($stdClass);
    }

    /**
     * Returns the configuration.
     * @return CCamera
     */
    public function getCCamera() {
        return $this->cCamera;
    }

    /**
     * Get array of access granted cameras for a single user.
     * @param array $cameras array of {@link camera} objects
     * @param user $user
     * @return array array of access granted {@link camera} objects.
     */
    public static function getAccessableCamerasByUser($cameras, $user)
    {
        $accessable = array();

        foreach ($cameras as $camera) {
            /** @var Camera $camera */
            if ($camera->isAccessGranted($user)) {
                $accessable[] = $camera;
            }
        }

        return $accessable;
    }

    /**
     * Return an camera object if equal camera name found.
     * @param array $cameras array of {@link camera} objects
     * @param string $name
     * @return Camera return a camera object if success, otherwise null
     */
    public static function getObjByName($cameras, $name)
    {
        foreach ($cameras as $camera) {
            /** @var Camera $camera */
            if ($camera->getCCamera()->getName() == $name) {
                return $camera;
            }
        }
        return null;
    }

    /**
     * Chmod the camera directory.
     * @link dir
     * @return void
     */
    public final function chmodDir()
    {
        if (is_dir($this->getCCamera()->getDir())) {
            chmod($this->getCCamera()->getDir(), 0750);
        }
    }

    /**
     * Chmod the archive log file.
     * @link arhiveLogFile
     * @return void
     */
    public final function chmodLogfile()
    {
        if ($this->getCCamera()->getArchive()->getLogFile()) {
            if (is_dir($this->getCCamera()->getArchive()->getLogFile())) {
                chmod($this->getCCamera()->getArchive()->getLogFile(), 0750);
            }
        }
    }

    /**
     * Chmod recursive the archive directory.
     * @link archiveDir
     * @return void
     */
    public final function chmodArchiveDir()
    {
        recursiveChmod($this->getCCamera()->getArchive()->getDir(), 0640, 0750);
    }

    /**
     * Get latest images from the camera.
     * @param boolean $delay
     * @return string return image path if success, otherwise false
     */
    public final function getImagesLast($delay = true)
    {
        $images = $this->getImages($delay);

        if (is_array($images)) {
            return array_pop($images);
        }

        return null;
    }

    /**
     * Get images from the camera.
     * @param boolean $delay
     * @param integer $maxFiles
     * @return mixed return array of strings if success, otherwise false
     */
    private final function getImages($delay = true)
    {
        $images = $this->getImagesAll(
            $this->getCCamera()->getMaximumFilesystemFileArray()
        );

        if ($delay) {
            if (count($images) <= $this->getCCamera()->getDelay()) {
                return null;
            }

            $images = array_slice($images, 0, -$this->getCCamera()->getDelay());
        }
        return $images;
    }

    /**
     * Return all images from a camera.
     * @param $maxFiles
     * @return mixed return an array of images if success, otherwise false
     */
    private final function getImagesAll($maxFiles)
    {
        $imagesall = array();
        if (
        is_dir(
            $this->getCCamera()->getDir()
        )
        ) {
            // by default file names are sorted alphabetical in ascending order
            $files = scandir($this->getCCamera()->getDir());
            $files = removeDotFiles($files);
            foreach ($files as $file) {
                if ($maxFiles-- == 0) {
                    break;
                }

                $fileinfo = pathinfo(
                    $this->getCCamera()->getDir() . DIRECTORY_SEPARATOR . $file
                );

                if (isset($fileinfo['extension'])) {

                    $extension = $fileinfo['extension'];
                    $extensions = $this->getCCamera()->getImageExtensions();
                    $isValidExtension = false;

                    if ($this->getCCamera()->isImageExtensionsCaseSensitive()) {
                        if (in_array($extension, $extensions)) {
                            $isValidExtension = true;
                        }
                    } else {
                        if (in_arrayi($extension, $extensions)) {
                            $isValidExtension = true;
                        }
                    }

                    if ($isValidExtension) {
                        $path =
                            $fileinfo['dirname'] .
                            DIRECTORY_SEPARATOR .
                            $fileinfo['basename'];
                        if (is_readable($path)) {
                            $imagesall[] = $path;
                        }
                    }

                }
            }
            sort($imagesall);
        }

        return $imagesall;
    }

    /**
     * Check granted acces for an user.
     * @param user $user
     * @return boolean true if access granted.
     */
    public final function isAccessGranted($user)
    {
        /**
         * never change the order!
         * UserDeny has the highest priority
         * GroupAllow has the lowest priority
         */

        if ($user->isInUsers($this->getCCamera()->getCAcl()->getUsers()->getDeny())) {
            return false;
        }

        if ($user->isInUsers($this->getCCamera()->getCAcl()->getUsers()->getAllow())) {
            return true;
        }

        if ($user->isInGroups($this->getCCamera()->getCAcl()->getGroups()->getDeny())) {
            return false;
        }

        if ($user->isInGroups($this->getCCamera()->getCAcl()->getGroups()->getAllow())) {
            return true;
        }

        return false;
    }

    /**
     * Create the archive for a camera.
     * @param boolean $delay if true,
     * the internal {@link $delay} of images is used.
     * @return string return the log string
     */
    public final function createArchive($delay = true)
    {
        $images = $this->getImages($delay);

        $logmsg = "\"" . $this->getCCamera()->getName() . "\": ";
        if (!is_array($images) || count($images) <= 0) {
            $logmsg .= "Nothing to archive.";
            $this->writeLog($logmsg);
            return $logmsg;
        }

        $logmsg .= "slice image array from: " . count($images) . " to ";
        $images = array_slice($images, 0, $this->getCCamera()->getArchive()->getMaxFiles());
        $logmsg .= count($images) . "; ";

        $zipArchive = false;
        $archivedFiles = array();
        $movedFiles = array();
        $movedFilesFailed = array();
        $archiveDir = $this->getCCamera()->getArchive()->getDir();

        // build the optional date path
        $optionalDatePath = "";
        if ($this->getCCamera()->getArchive()->getDirDate()) {
            $optionalDatePath = date($this->getCCamera()->getArchive()->getDirDate());
        }

        $destPath = $archiveDir . DIRECTORY_SEPARATOR . $optionalDatePath;

        if ($this->getCCamera()->getArchive()->isPackageFormatZip()) {
            nis_dir_mkdir($destPath);

            $zipArchive = new \ZipArchive();
            $zipFilename =
                $destPath .
                DIRECTORY_SEPARATOR .
                $this->getCCamera()->getName() .
                date($this->getCCamera()->getArchive()->getPackageFormatZipName()) .
                ".zip";

            if ($zipArchive->open($zipFilename, \ZipArchive::CREATE) !== TRUE) {
                $logmsg .=
                    "ERROR: Can not open \"$zipFilename\" in function createArchive()";
                $this->writeLog($logmsg);
                return $logmsg;
            }

            foreach ($images as $image) {
                $zipArchive->addFile($image);
                $archivedFiles[] = $image;
            }

            $zipArchive->close();

        } else if ($this->getCCamera()->getArchive()->getPackageFormatCustomExec()) {
            nis_dir_mkdir($destPath);
            $tmpPath = $this->getCCamera()->getTmpPathPrepend() . $this->getCCamera()->getName() . "_" . floor(millitime());
            nis_dir_mkdir($tmpPath);

            foreach ($images as $image) {
                $path_parts = pathinfo($image);
                $newname = sprintf(
                    $this->getCCamera()->getArchive()->getPackageFormatCustomExecImageFormat(),
                    count($movedFiles)
                );
                $newname = $tmpPath . DIRECTORY_SEPARATOR . $newname;

                if (rename($image, $newname)) {
                    $movedFiles[] = $image;
                } else {
                    $movedFilesFailed[] = $image;
                }
            }

            if (count($movedFiles) > 0) {
                $exec = sprintf(
                    $this->getCCamera()->getArchive()->getPackageFormatCustomExec(),
                    $this->getCCamera()->getName(),
                    date(
                        $this->getCCamera()->getArchive()->getPackageFormatCustomExecDateParameter()
                    ),
                    $tmpPath,
                    $destPath
                );
                $logmsg .= "exec:[ " . $exec . " ]";
                $logmsg .= "exec return:[ " . shell_exec($exec) . " ]";
            }
        }

        if ($this->getCCamera()->getArchive()->isImageUnlink()) {
            foreach ($archivedFiles as $rmfile) {
                unlink($rmfile);
            }
        }

        if (count($archivedFiles) > 0) {
            $logmsg .= " " . count($archivedFiles) . " images archived as zip;";
        }

        if (count($movedFilesFailed) > 0) {
            $logmsg .= " ERROR: " . count($movedFilesFailed) . " images failed to move;";
        }

        if (count($movedFiles) > 0) {
            $logmsg .= " " . count($movedFiles) . " images moved;";
        }

        $this->writeLog($logmsg);
        return $logmsg;
    }

    /**
     * Append a log entry for the camera.
     * @param string $logmsg
     * @return bool true if success, otherwise false
     */
    public final function writeLog($logmsg)
    {
        if ($this->getCCamera()->getArchive()->getLogFile()) {
            $logmsg = "(" . date("Y-m-d H:i:s") . "): $logmsg\n";
            $hArchiveLogFile = fopen($this->getCCamera()->getArchive()->getLogFile(), "a");

            if ($hArchiveLogFile) {
                fprintf($hArchiveLogFile, $logmsg);
                fclose($hArchiveLogFile);
            } else {
                return false;
            }

        }
        return true;
    }

    /**
     * Purge the archive from a camera.
     * @param boolean $delay if true,
     * the internal {@link $delay} of images is used.
     * @return string return the log string
     */
    public final function purgeArchive($delay = true)
    {
        $logmsg = "";
        $archiveListing = $this->getArchiveListing();

        $archiveDir = $this->getCCamera()->getArchive()->getDir();
        $fullList = Camera::archiveListingToPathList($archiveListing, "");
        $removedCount = 0;
        $timeLimit = time() - $this->getCCamera()->getArchive()->getPurgeTimeLimit();

        foreach ($fullList as $element) {
            $filename = $archiveDir . $element;
            if (filemtime($filename) < $timeLimit) {
                unlink($filename);
                $removedCount++;
            }
        }
        removeEmptySubfolders($archiveDir);

        $logmsg .= "removed " . $removedCount . " archives;";

        $this->writeLog($logmsg);
        return $logmsg;
    }

    /**
     * Get a listing of all archived files.
     * @param boolean $delay if true,
     * the internal {@link $delay} of images is used.
     * @return string return the log string
     */
    public final function getArchiveListing($dir = null)
    {
        if ($dir == null) {
            $dir = $this->getCCamera()->getArchive()->getDir();
        }

        $listDir = array();
        if ($handler = opendir($dir)) {
            while (($sub = readdir($handler)) !== FALSE) {
                if (!isDotFile($sub)) {
                    $path = $dir . DIRECTORY_SEPARATOR . $sub;

                    if (is_file($path)) {
                        $fileinfo = pathinfo(
                            $path
                        );

                        if (isset($fileinfo['extension'])) {

                            $extension = $fileinfo['extension'];
                            $extensions = $this->getCCamera()->getArchive()->getExtensions();
                            $isValidExtension = false;

                            if ($this->getCCamera()->getArchive()->isExtensionsCaseSensitive()) {
                                if (in_array($extension, $extensions)) {
                                    $isValidExtension = true;
                                }
                            } else {
                                if (in_arrayi($extension, $extensions)) {
                                    $isValidExtension = true;
                                }
                            }

                            if ($isValidExtension) {
                                $listDir[] = $sub;
                            }

                        }

                    } else if (is_dir($path)) {
                        $listDir[$sub] = $this->getArchiveListing($path);
                    }
                }
            }
            closedir($handler);
        }

        if (contains_array($listDir)) {
            //sort directories
            ksort($listDir);
        } else {
            //sort the filename
            sort($listDir);
        }

        return $listDir;
    }

    public static function archiveListingToPathList($listing, $prepend = "")
    {
        $list = array();
        $prestring = $prepend . DIRECTORY_SEPARATOR;
        foreach ($listing as $key => $value) {
            if (is_string($value)) {
                $list[] = $prestring . $value;
            } else if (is_array($value)) {
                $list = array_merge($list, Camera::archiveListingToPathList($value, $prestring . $key));
            }
        }
        return $list;
    }
}
