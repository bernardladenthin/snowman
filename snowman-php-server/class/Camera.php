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
 * Class for single camera
*/
class Camera {
	/**
	 * Name of the camera.
	 * @var string
	 */
	private $name;

	/**
	 * File directory of camera images.
	 * @var string
	 */
	private $dir;

	/**
	 * Refresh time for images.
	 * @var integer
	 */
	private $refresh;

	/**
	 * Number of latest images are ignored to get a viewing delay.
	 * @var integer
	 */
	private $delay;

	/**
	 * Camera image width.
	 * @var integer
	 */
	private $width;

	/**
	 * Camera image height.
	 * @var integer
	 */
	private $height;

	/**
	 * Top branding string for camera.
	 * @var string
	 */
	private $toptextbranding;

	/**
	 * Bottom branding string for camera.
	 * @var string
	 */
	private $bottomtextbranding;

	/**#@+
	 * @var array array of strings
	 */
	/**
	 * Array of groups allowed to watching. Lowest rights priority.
	 */
	private $groupallow;

	/**
	 * Array of groups denied to watching.
	 * Higher rights priority as {@link $groupallow}.
	 */
	private $groupdeny;

	/**
	 * Array of users allowed to watching.
	 * Higher rights priority as {@link $groupdeny}.
	 */
	private $userallow;

	/**
	 * Array of users denied to watching.
	 * Higher rights priority as {@link $userallow}.
	 */
	private $userdeny;
	/**#@-*/

	/**
	 * Prepend path for the tmp path creation.
	 * @var string
	 */
	private $tmpPathPrepend;

	/**
	 * Path to archive directory.
	 * @var string
	 */
	private $archiveDir;

	/**
	 * Maximum count of images for one archive.
	 * @var integer
	 */
	private $archiveMaxFiles;

	/**
	 * The file extension for a camera image.
	 * @var array
	 */
	private $imageExtensions;

	/**
	 * Check the image extension in case sensitive mode.
	 * @var boolean
	 */
	private $imageExtensionsCaseSensitive;

	/**
	 * Use the POSIX millis format to write a image.
	 * @var boolean
	 */
	private $camerawriterFormatPosixMillis;

	/**
	 * Maximum count for a file array from the filesystem.
	 * @var integer
	 */
	private $maximumFilesystemFileArray;

	/**
	 * Use a zip container for a archive.
	 * @var boolean
	 */
	private $archivePackageFormatZip;

	/**
	 * Use a custom date for the filename.
	 * @var string
	 */
	private $archivePackageFormatZipName;

	/**
	 * Use a custom execution to archive. Use false to deactivate.
	 * @var string, boolean
	 */
	private $archivePackageFormatCustomExec;

	/**
	 * Use a custom format for moved files before trigger the custom execution.
	 * @var string
	 */
	private $archivePackageFormatCustomExecImageFormat;

	/**
	 * Use a custom date for the output name of the custom execution result.
	 * @var string
	 */
	private $archivePackageFormatCustomExecDateParameter;

	/**
	 * Create a year directory for archive.
	 * @var boolean
	 */
	private $archiveDirDate;

	/**
	 * Unlink (remove) images after archive.
	 * @var boolean
	 */
	private $archiveImageUnlink;

	/**
	 * Path for the archive log file.
	 * @var string
	 */
	private $archiveLogFile;

	/**
	 * Constructor
	 * {@link name}
	 * {@link dir}
	 * {@link refresh}
	 * {@link delay}
	 * {@link width}
	 * {@link height}
	 * {@link toptextbranding}
	 * {@link bottomtextbranding}
	 * {@link groupallow}
	 * {@link groupdeny}
	 * {@link userallow}
	 * {@link userdeny}
	 * {@link tmpPathPrepend}
	 * {@link archiveDir}
	 * {@link archiveMaxFiles}
	 * {@link imageExtensions}
	 * {@link imageExtensionsCaseSensitive}
	 * {@link camerawriterFormatPosixMillis}
	 * {@link maximumFilesystemFileArray}
	 * {@link archivePackageFormatZip}
	 * {@link archivePackageFormatZipName}
	 * {@link archivePackageFormatCustomExec}
	 * {@link archivePackageFormatCustomExecImageFormat}
	 * {@link archivePackageFormatCustomExecDateParameter}
	 * {@link archiveDirDate}
	 * {@link archiveImageUnlink}
	 * {@link archiveLogFile}
	 * @param StdClass $camera
	 * @return void
	 */
	public function camera(
		$stdClass
	) {
		$this->name = $stdClass->name;
		$this->dir = $stdClass->dir;
		$this->refresh = intval($stdClass->refresh);
		$this->delay = intval($stdClass->delay);
		$this->width = intval($stdClass->width);
		$this->height = intval($stdClass->height);
		$this->toptextbranding = $stdClass->toptextbranding;
		$this->bottomtextbranding = $stdClass->bottomtextbranding;

		if(!is_array($stdClass->groupallow)) {
			printf('Warning: $groupallow should be an array.');
		}
		$this->groupallow = $stdClass->groupallow;

		if(!is_array($stdClass->groupdeny)) {
			printf('Warning: $groupdeny should be an array.');
		}
		$this->groupdeny = $stdClass->groupdeny;

		if(!is_array($stdClass->userallow)) {
			printf('Warning: $userallow should be an array.');
		}
		$this->userallow = $stdClass->userallow;

		if(!is_array($stdClass->userdeny)) {
			printf('Warning: $userdeny should be an array.');
		}
		$this->userdeny = $stdClass->userdeny;

		$this->tmpPathPrepend = $stdClass->tmpPathPrepend;
		$this->archiveDir = $stdClass->archiveDir;
		$this->archiveMaxFiles = intval($stdClass->archiveMaxFiles);

		if(!is_array($stdClass->imageExtensions)) {
			printf('Warning: $imageExtensions should be an array.');
		}
		$this->imageExtensions = $stdClass->imageExtensions;

		$this->imageExtensionsCaseSensitive =
			$stdClass->imageExtensionsCaseSensitive;

		$this->camerawriterFormatPosixMillis =
			$stdClass->camerawriterFormatPosixMillis;

		$this->maximumFilesystemFileArray =
			$stdClass->maximumFilesystemFileArray;

		$this->archivePackageFormatZip = $stdClass->archivePackageFormatZip;
		$this->archivePackageFormatZipName =
			$stdClass->archivePackageFormatZipName;

		$this->archivePackageFormatCustomExec =
			$stdClass->archivePackageFormatCustomExec;

		$this->archivePackageFormatCustomExecImageFormat =
			$stdClass->archivePackageFormatCustomExecImageFormat;

		$this->archivePackageFormatCustomExecDateParameter =
			$stdClass->archivePackageFormatCustomExecDateParameter;

		$this->archiveDirDate = $stdClass->archiveDirDate;
		$this->archiveImageUnlink = $stdClass->archiveImageUnlink;
		$this->archiveLogFile = $stdClass->archiveLogFile;
	}

	/**
	 * Return name from a camera.
	 * @link name
	 * @return string name
	 */
	public final function getName() {
		return $this->name;
	}

	/**
	 * Return dir from a camera.
	 * @link dir
	 * @return string dir
	 */
	public final function getDir() {
		return $this->dir;
	}

	/**
	 * Return refresh time from a camera.
	 * @link refresh
	 * @return integer refresh
	 */
	public final function getRefresh() {
		return $this->refresh;
	}

	/**
	 * Return delay from a camera.
	 * @link delay
	 * @return integer delay
	 */
	public final function getDelay() {
		return $this->delay;
	}

	/**
	 * Return width from a camera.
	 * @link width
	 * @return integer width
	 */
	public final function getWidth() {
		return $this->width;
	}

	/**
	 * Return height from a camera.
	 * @link height
	 * @return integer height
	 */
	public final function getHeight() {
		return $this->height;
	}

	/**
	 * Return top branding string from a camera.
	 * @link toptextbranding
	 * @return string toptextbranding
	 */
	public final function getTopTextBranding() {
		return $this->toptextbranding;
	}

	/**
	 * Return bottom branding string from a camera.
	 * @link bottomtextbranding
	 * @return string bottomtextbranding
	 */
	public final function getBottomTextBranding() {
		return $this->bottomtextbranding;
	}

	/**
	 * Return array of group name strings.
	 * @link groupallow
	 * @return array array of strings
	 */
	public final function getGroupAllow() {
		return $this->groupallow;
	}

	/**
	 * Return array of group name strings.
	 * @link groupdeny
	 * @return array array of strings
	 */
	public final function getGroupDeny() {
		return $this->groupdeny;
	}

	/**
	 * Return array of user name strings.
	 * @link userallow
	 * @return array array of strings
	 */
	public final function getUserAllow() {
		return $this->userallow;
	}

	/**
	 * Return array of user name strings.
	 * @link userdeny
	 * @return array array of strings
	 */
	public final function getUserDeny() {
		return $this->userdeny;
	}

	/**
	 * Return the prepend path for the tmp path creation.
	 * @link tmpPathPrepend
	 * @return string tmp path prepend
	 */
	public final function getTmpPathPrepend() {
		return $this->tmpPathPrepend;
	}

	/**
	 * Return the dir for archive from a camera.
	 * @link archiveDir
	 * @return string archive path
	 */
	public final function getArchiveDir() {
		return $this->archiveDir;
	}

	/**
	 * Return maximum archive files from a camera.
	 * @link archiveMaxFiles
	 * @return integer maximum files
	 */
	public final function getArchiveMaxFiles() {
		return $this->archiveMaxFiles;
	}

	/**
	 * Getter for <code>$archivePackageFormatZip</code>.
	 * @link archivePackageFormatZip
	 * @return boolean
	 */
	public final function getArchivePackageFormatZip() {
		return $this->archivePackageFormatZip;
	}

	/**
	 * Getter for <code>$archivePackageFormatZipName</code>.
	 * @link archivePackageFormatZipName
	 * @return boolean
	 */
	public final function getArchivePackageFormatZipName() {
		return $this->archivePackageFormatZipName;
	}

	/**
	 * Getter for <code>$archivePackageFormatCustomExec</code>.
	 * @link archivePackageFormatCustomExec
	 * @return string, boolean
	 */
	public final function getArchivePackageFormatCustomExec() {
		return $this->archivePackageFormatCustomExec;
	}

	/**
	 * Getter for <code>$archivePackageFormatCustomExecImageFormat</code>.
	 * @link archivePackageFormatCustomExecImageFormat
	 * @return string
	 */
	public final function getArchivePackageFormatCustomExecImageFormat() {
		return $this->archivePackageFormatCustomExecImageFormat;
	}

	/**
	 * Getter for <code>$archivePackageFormatCustomExecDateParameter</code>.
	 * @link archivePackageFormatCustomExecDateParameter
	 * @return string
	 */
	public final function getArchivePackageFormatCustomExecDateParameter() {
		return $this->archivePackageFormatCustomExecDateParameter;
	}

	/**
	 * Getter for <code>$archiveDirDate</code>.
	 * @link archiveDirDate
	 * @return boolean
	 */
	public final function getArchiveDirDate() {
		return $this->archiveDirDate;
	}

	/**
	 * Getter for <code>$archiveImageUnlink</code>.
	 * @link archiveImageUnlink
	 * @return boolean
	 */
	public final function getArchiveImageUnlink() {
		return $this->archiveImageUnlink;
	}

	/**
	 * Getter for <code>$archiveLogFile</code>.
	 * @link archiveLogFile
	 * @return boolean
	 */
	public final function getArchiveLogFile() {
		return $this->archiveLogFile;
	}

	/**
	 * Return the image extensions from a camera.
	 * @link imageExtensions
	 * @return array return an array of of strings for image extensions
	 */
	public final function getImageExtensions() {
		return $this->imageExtensions;
	}

	/**
	 * Getter for <code>$imageExtensionsCaseSensitive</code>.
	 * @link imageExtensionsCaseSensitive
	 * @return boolean
	 */
	public final function getImageExtensionsCaseSensitive() {
		return $this->imageExtensionsCaseSensitive;
	}

	/**
	 * Getter for <code>camerawriterFormatPosixMillis</code>.
	 * @link camerawriterFormatPosixMillis
	 * @return boolean
	 */
	public final function getCamerawriterFormatPosixMillis() {
		return $this->camerawriterFormatPosixMillis;
	}

	/**
	 * Getter for <code>maximumFilesystemFileArray</code>.
	 * @link maximumFilesystemFileArray
	 * @return integer
	 */
	public final function getMaximumFilesystemFileArray() {
		return $this->maximumFilesystemFileArray;
	}

	/**
	 * Chmod the camera directory.
	 * @link dir
	 * @return void
	 */
	public final function chmodDir() {
		if(is_dir($this->getDir())) {
			chmod($this->getDir(), 0750);
		}
	}

	/**
	 * Chmod the archive log file.
	 * @link arhiveLogFile
	 * @return void
	 */
	public final function chmodLogfile() {
		if($this->getArchiveLogFile()) {
			if(is_dir($this->getArchiveLogFile())) {
				chmod($this->getArchiveLogFile(), 0750);
			}
		}
	}

	/**
	 * Chmod recursive the archive directory.
	 * @link archiveDir
	 * @return void
	 */
	public final function chmodArchiveDir() {
		recursiveChmod($this->getArchiveDir(), 0640, 0750);
	}

	/**
	 * Return all images from a camera.
	 * @param $maxFiles
	 * @return mixed return an array of images if success, otherwise false
	 */
	private final function getImagesAll($maxFiles) {
		$imagesall = array();
		if(
			is_dir(
				$this->getDir()
			)
		) {
			$files = scandir($this->getDir());
			$files = removeDotFiles($files);
			foreach($files as $file) {
				if($maxFiles-- == 0) {
					break;
				}

				$fileinfo = pathinfo(
					$this->getDir().DIRECTORY_SEPARATOR.$file
				);

				if( isset($fileinfo['extension']) ) {

					$extension = $fileinfo['extension'];
					$extensions = $this->getImageExtensions();

					if(!$this->getImageExtensionsCaseSensitive()) {
						$extension = strtoupper($extension);
						$extensionsUpper = array();
						foreach($extensions as $e) {
							$extensionsUpper[] = strtoupper($e);
						}
						$extensions = $extensionsUpper;
					}

					if(in_array($extension, $extensions)) {
						$path =
							$fileinfo['dirname'].
							DIRECTORY_SEPARATOR.
							$fileinfo['basename'];
						if(is_readable($path)){
							$imagesall[] = $path;
						}
					}

				}
			}
			asort($imagesall);
		}

		return $imagesall;
	}

	/**
	 * Get images from the camera.
	 * @param boolean $delay
	 * @param integer $maxFiles
	 * @return mixed return array of strings if success, otherwise false
	 */
	private final function getImages($delay=true) {
		$images = $this->getImagesAll(
			$this->getMaximumFilesystemFileArray()
		);

		if($delay) {
			if(count($images) <= $this->getDelay()) {
				return null;
			}

			$images = array_slice($images, 0, -$this->getDelay());
		}
		return $images;
	}

	/**
	 * Get latest images from the camera.
	 * @param boolean $delay
	 * @return string return image path if success, otherwise false
	 */
	public final function getImagesLast($delay=true) {
		$images = $this->getImages($delay);

		if(is_array($images)) {
			return array_pop($images);
		}

		return null;
	}

	/**
	 * Check granted acces for an user.
	 * @param user $user
	 * @return boolean true if access granted.
	 */
	public final function isAccessGranted($user) {
		/**
		 * never change the order!
		 * UserDeny has the highest priority
		 * GroupAllow has the lowest priority
		 */

		if($user->isInUsers($this->getUserDeny())) {
			return false;
		}

		if($user->isInUsers($this->getUserAllow())) {
			return true;
		}

		if($user->isInGroups($this->getGroupDeny())) {
			return false;
		}

		if($user->isInGroups($this->getGroupAllow())) {
			return true;
		}

		return false;
	}

	/**
	 * Get array of access granted cameras for a single user.
	 * @param array $cameras array of {@link camera} objects
	 * @param user $user
	 * @return array array of access granted {@link camera} objects.
	 */
	public final static function getAccessableCamerasByUser($cameras, $user) {
		$accessable = array();

		foreach($cameras as $camera) {
			if($camera->isAccessGranted($user)) {
				$accessable[] = $camera;
			}
		}

		return $accessable;
	}

	/**
	 * Return an camera object if equal camera name found.
	 * @param array $cameras
	 * @param string $name
	 * @return mixed return a user object if success, otherwise false
	 */
	public final static function getObjByName($cameras,$name) {
		foreach($cameras as $camera) {
			if($camera->getName() == $name) {
				return $camera;
			}
		}
		return false;
	}

	/**
	 * Append a log entry for the camera.
	 * @param string $logmsg
	 * @return bool true if success, otherwise false
	 */
	public final function writeLog($logmsg) {
		if($this->getArchiveLogFile()) {
			$logmsg = "(".date("Y-m-d H:i:s")."): $logmsg\n";
			$hArchiveLogFile = fopen($this->getArchiveLogFile(), "a");

			if($hArchiveLogFile) {
				fprintf($hArchiveLogFile, $logmsg);
				fclose($hArchiveLogFile);
			} else {
				return false;
			}

		}
		return true;
	}

	/**
	 * Create the archive for a camera.
	 * @param boolean $delay if true,
	 * the internal {@link $delay} of images is used.
	 * @return string return the log string
	 */
	public final function createArchive($delay=true) {
		$images = $this->getImages($delay);

		$logmsg = "\"".$this->getName()."\": ";
		if(!is_array($images) || count($images) <= 0) {
			$logmsg .="Nothing to archive.";
			$this->writeLog($logmsg);
			return $logmsg;
		}

		$logmsg .= "slice image array from: ".count($images). " to ";
		$images = array_slice($images, 0, $this->getArchiveMaxFiles());
		$logmsg .= count($images). "; ";

		$zipArchive = false;
		$archivedFiles = array();
		$movedFiles = array();
		$movedFilesFailed = array();
		$archiveDir = $this->getArchiveDir();

		// build the optional date path
		$optionalDatePath = "";
		if($this->getArchiveDirDate()) {
			$optionalDatePath = date($this->getArchiveDirDate());
		}

		$destPath = $archiveDir.DIRECTORY_SEPARATOR.$optionalDatePath;

		if($this->getArchivePackageFormatZip()) {
			nis_dir_mkdir($destPath);

			$zipArchive = new ZipArchive();
			$zipFilename =
				$destPath.
				DIRECTORY_SEPARATOR.
				$this->getName().
				date($this->getArchivePackageFormatZipName()).
				".zip";

			if($zipArchive->open($zipFilename, ZIPARCHIVE::CREATE) !== TRUE) {
				$logmsg .=
					"ERROR: Can not open \"$zipFilename\" in function createArchive()";
				$this->writeLog($logmsg);
				return $logmsg;
			}

			foreach($images as $image) {
				$zipArchive->addFile($image);
				$archivedFiles[] = $image;
			}

			$zipArchive->close();

		} else if($this->getArchivePackageFormatCustomExec()) {
			nis_dir_mkdir($destPath);
			$tmpPath = $this->getTmpPathPrepend().$this->getName()."_".floor(millitime());
			nis_dir_mkdir($tmpPath);

			foreach($images as $image) {
				$path_parts = pathinfo($image);
				$newname = sprintf(
					$this->getArchivePackageFormatCustomExecImageFormat(),
					count($movedFiles)
				);
				$newname = $tmpPath.DIRECTORY_SEPARATOR.$newname;

				if(rename ($image, $newname)) {
					$movedFiles[] = $image;
				} else {
					$movedFilesFailed[] = $image;
				}
			}

			if(count($movedFiles) >0) {
				$exec = sprintf(
					$this->getArchivePackageFormatCustomExec(),
					$this->getName(),
					date(
						$this->getArchivePackageFormatCustomExecDateParameter()
					),
					$tmpPath,
					$destPath
				);
				$logmsg .= "exec:[ ".$exec. " ]";
				$logmsg .= "exec return:[ ".shell_exec($exec). " ]";
			}
		}

		if($this->getArchiveImageUnlink()) {
			foreach($archivedFiles as $rmfile) {
				unlink($rmfile);
			}
		}

		if(count($archivedFiles) > 0) {
			$logmsg .= " ".count($archivedFiles)." images archived as zip;";
		}

		if(count($movedFilesFailed) > 0) {
			$logmsg .= " ERROR: ".count($movedFilesFailed)." images failed to move;";
		}

		if(count($movedFiles) > 0) {
			$logmsg .= " ".count($movedFiles)." images moved;";
		}

		$this->writeLog($logmsg);
		return $logmsg;
	}
}

