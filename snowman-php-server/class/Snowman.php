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
 * The main logic of snowman. Create user-, camera- and camera-instances
 * from ini files. Manage the user request.
 */

/**
 * Class for snowman
*/
class Snowman {
	private $version;
	private $versiondate;
	private $owner;
	private $imprinturl;
	private $ajaxapiurl;
	private $liveviewurl;
	private $downloadarchiveurl;
	private $users;
	private $cameras;
	private $securehosts;

	private $loginuser = false;

	private $archiveOnlyFromSecureHost;
	private $archiveOnlyAccessableCameras;

	/**
	 * Constructor
	 * @link version
	 * @link versiondate
	 * @link owner
	 * @link imprinturl
	 * @link ajaxapiurl
	 * @link liveviewurl
	 * @link downloadarchiveurl
	 * @link securehosts
	 * @link archiveOnlyFromSecureHost
	 * @link archiveOnlyAccessableCameras
	 * @param StdClass $snowman
	 * @return void
	 */
	public function snowman(
		$snowman
	) {
		$this->version = $snowman->version;
		$this->versiondate = $snowman->versiondate;
		$this->owner = $snowman->owner;

		if (isLocalIp($_SERVER["REMOTE_ADDR"])) {
			$this->imprinturl = $snowman->localurl->imprinturl;
			$this->ajaxapiurl = $snowman->localurl->ajaxapiurl;
			$this->liveviewurl = $snowman->localurl->liveviewurl;
			$this->downloadarchiveurl = $snowman->localurl->downloadarchiveurl;
		} else {
			$this->imprinturl = $snowman->foreignurl->imprinturl;
			$this->ajaxapiurl = $snowman->foreignurl->ajaxapiurl;
			$this->liveviewurl = $snowman->foreignurl->liveviewurl;
			$this->downloadarchiveurl = $snowman->foreignurl->downloadarchiveurl;
		}

		$this->securehosts = $snowman->securehosts;
		$this->archiveOnlyFromSecureHost = $snowman->archiveOnlyFromSecureHost;
		$this->archiveOnlyAccessableCameras =
			$snowman->archiveOnlyAccessableCameras;
	}

	/**
	 * Get the snowman version.
	 * @link version
	 * @return string version as string
	 */
	public final function getVersion() {
		return $this->version;
	}

	/**
	 * Is the policy set to trigger a archive execution only by registred
	 * host.
	 * @link disableSecureHostArchive
	 * @return boolean
	 */
	public final function getArchiveOnlyFromSecureHost() {
		return $this->archiveOnlyFromSecureHost;
	}

	/**
	 * Is the policy set to trigger a archive only for accessables cameras
	 * for the calling user.
	 * @link archiveOnlyAccessableCameras
	 * @return boolean
	 */
	public final function getArchiveOnlyAccessableCameras() {
		return $this->archiveOnlyAccessableCameras;
	}

	/**
	 * Get the hosts that are defined as secure.
	 * @link securehosts
	 * @return array array of strings
	 */
	public final function getSecureHosts() {
		return $this->securehosts;
	}

	/**
	 * Get the snowman version date.
	 * @link versiondate
	 * @return string should be in format yyyy-mm-dd, but not specified
	 */
	public final function getVersiondate() {
		return $this->versiondate;
	}

	/**
	 * Get the website owner.
	 * @link owner
	 * @return string
	 */
	public final function getOwner() {
		return $this->owner;
	}

	/**
	 * Get the imprint url.
	 * @link imprinturl
	 * @return string
	 */
	public final function getImprinturl() {
		return $this->imprinturl;
	}

	/**
	 * Get the api url.
	 * @link ajaxapiurl
	 * @return string
	 */
	public final function getAjaxapiurl() {
		return $this->ajaxapiurl;
	}

	/**
	 * Get the liveview url.
	 * @link liveview
	 * @return string
	 */
	public final function getLiveviewurl() {
		return $this->liveviewurl;
	}

	/**
	 * Get the download archive url.
	 * @link liveview
	 * @return string
	 */
	public final function getDownloadarchiveurl() {
		return $this->downloadarchiveurl;
	}

	/**
	 * Get the snowman users.
	 * @link users
	 * @return array array of user objects from
	 */
	public final function getUsers() {
		return $this->users;
	}

	/**
	 * Get the snowman cameras.
	 * @link cameras
	 * @return array array of camera objects from
	 */
	public final function getCameras() {
		return $this->cameras;
	}

	/**
	 * Get the snowman users.
	 * @link loginuser
	 * @link isLoginOK see the isLoginOK function for more
	 * @return mixed return a {@link user} object if isLoginOK,
	 * otherwise false
	 */
	public final function getLoginUser() {
		return $this->loginuser;
	}

	/**
	 * Load the users from config and create objects.
	 * @link user
	 * @link users
	 * @param StdClass $users
	 * @return array array of user objects
	 */
	public final function loadUsers($users) {
		$this->users = array();
		foreach($users as $user) {
			$this->users[] = new User($user);
		}
	}

	/**
	 * Load the cameras from config and create objects.
	 * @link camera
	 * @link cameras
	 * @param StdClass $config
	 * @return array array of camera objects
	 */
	public final function loadCameras($cameras) {
		$this->cameras = array();
		foreach($cameras as $camera) {
			$this->cameras[] = new Camera($camera);
		}
	}

	/**
	 * Start a snowman session.
	 * @return void
	 */
	public final function session_start() {
		session_start();
	}

	/**
	 * Clean and close a snowman session.
	 * @return void
	 */
	public final function session_destroy() {
		global $sessionUsernameParameter;
		global $sessionPasswordParameter;
		global $sessionIsLoginOkParameter;

		unset($_SESSION[$sessionUsernameParameter]);
		unset($_SESSION[$sessionPasswordParameter]);
		unset($_SESSION[$sessionIsLoginOkParameter]);
		session_destroy();
	}

	/**
	 * Refresh the Chmod for all cameras.
	 * @return void
	 */
	public final function refreshChmod() {
		foreach($this->getCameras() as $camera) {
				$camera->chmodLogfile();
				$camera->chmodDir();
				$camera->chmodArchiveDir();
		}
	}

	/**
	 * Check for correct login data and user is not disabled.
	 * @todo implement timelimit for a ip adress to protect brute force.
	 * @param string $username optional name from the user
	 * @param string $user optional password from the user (plain text)
	 * @return bool
	 */
	public final function isLoginOK($username="", $password="") {
		global $sessionUsernameParameter;
		global $sessionPasswordParameter;
		global $sessionIsLoginOkParameter;

		$_SESSION[$sessionIsLoginOkParameter] = false;
		if(!$username || !$password) {
			if(isset($_SESSION[$sessionUsernameParameter])) {
				$username = $_SESSION[$sessionUsernameParameter];
			}
			if(isset($_SESSION[$sessionPasswordParameter])) {
				$password = $_SESSION[$sessionPasswordParameter];
			}
		}

		$user = User::getObjByNameAndPassword(
			$this->getUsers(),
			$username,
			$password
		);

		if(is_object($user)) {
			$_SESSION[$sessionIsLoginOkParameter] = true;
			$_SESSION[$sessionUsernameParameter] = $username;
			$_SESSION[$sessionPasswordParameter] = $password;
			$this->loginuser = $user;
			return true;
		} else {
			$this->loginuser = false;
			return false;
		}
	}

	/**
	 * Check if the server request is from a registered secure host.
	 * @return boolean true if secure host, otherwise false.
	 */
	public final function isSecureHost() {
		if(in_array(
			gethostbyaddr($_SERVER['REMOTE_ADDR']),
			$this->getSecureHosts()
		)) {
			return true;
		}
		return false;
	}

    private final function getArchiveCameras() {
		$cameras = array();

		if($this->getLoginUser()) {
			if( $this->getArchiveOnlyAccessableCameras() ) {
				$cameras = camera::getAccessableCamerasByUser(
					$this->getCameras(),
					$this->getLoginUser()
				);
			}
			else {
				$cameras = $this->getCameras();
			}
		} else {
			if( $this->getArchiveOnlyFromSecureHost() ) {
				if($this->isSecureHost()) {
					$cameras = $this->getCameras();
				}
			}
			else {
				$cameras = $this->getCameras();
			}
		}
        return $cameras;
    }

	/**
	 * Archive the cameras.
	 * @return string log information
	 */
	public final function createArchive() {
		$archivestring = "";
		$cameras = $this->getArchiveCameras();

		foreach($cameras as $camera) {
			$archivestring .= $camera->createArchive()."\n";
		}

		$this->refreshChmod();

		return $archivestring;
	}

	/**
	 * Purge the archive.
	 * @return string log information
	 */
	public final function purgeArchive() {
		$archivestring = "";
		$cameras = $this->getArchiveCameras();

		foreach($cameras as $camera) {
			$archivestring .= $camera->purgeArchive()."\n";
		}

		$this->refreshChmod();

		return $archivestring;
	}

}

