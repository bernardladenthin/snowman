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
 * Class for snowman.
 */
class Snowman
{

    private $users;
    private $cameras;
    /** @var CSnowman $csnowman */
    private $cSnowman;

    /** @var mixed $loginuser */
    private $loginuser = false;

    /**
     * Constructor.
     * @param \stdClass $stdClass Configuration object.
     */
    public function __construct($stdClass)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->cSnowman = new CSnowman($stdClass->snowman);

        $this->users = array();
        /** @noinspection PhpUndefinedFieldInspection */
        foreach ($stdClass->users as $user) {
            $this->users[] = new User($user);
        }

        $this->cameras = array();
        /** @noinspection PhpUndefinedFieldInspection */
        foreach ($stdClass->cameras as $camera) {
            $this->cameras[] = new Camera($camera);
        }
    }

    /**
     * Returns the configuration.
     * @return CSnowman
     */
    public function getCSnowman() {
        return $this->cSnowman;
    }

    /**
     * Returns the users.
     * @link users
     * @return array array of user objects
     */
    public final function getUsers()
    {
        return $this->users;
    }

    /**
     * Returns the cameras.
     * @link cameras
     * @return array array of camera objects
     */
    public final function getCameras()
    {
        return $this->cameras;
    }

    /**
     * Returns the login users.
     * @link loginuser
     * @link isLoginOK see the isLoginOK function for more
     * @return User return a {@link user} object if isLoginOK,
     * otherwise false
     */
    public final function getLoginUser()
    {
        return $this->loginuser;
    }

    /**
     * Start a snowman session.
     * @return void
     */
    public final function session_start()
    {
        session_start();
    }

    /**
     * Clean and close a snowman session.
     * @return void
     */
    public final function session_destroy()
    {
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
    public final function refreshChmod()
    {
        foreach ($this->getCameras() as $camera) {
            /** @var Camera $camera */
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
    public final function isLoginOK($username = "", $password = "")
    {
        global $sessionUsernameParameter;
        global $sessionPasswordParameter;
        global $sessionIsLoginOkParameter;

        $_SESSION[$sessionIsLoginOkParameter] = false;
        if (!$username || !$password) {
            if (isset($_SESSION[$sessionUsernameParameter])) {
                $username = $_SESSION[$sessionUsernameParameter];
            }
            if (isset($_SESSION[$sessionPasswordParameter])) {
                $password = $_SESSION[$sessionPasswordParameter];
            }
        }

        $user = User::getObjByNameAndPassword(
            $this->getUsers(),
            $username,
            $password
        );

        if (is_object($user)) {
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
    public final function isSecureHost()
    {
        if (in_array(
            gethostbyaddr($_SERVER['REMOTE_ADDR']),
            $this->getCSnowman()->getSecureHosts()
        )
        ) {
            return true;
        }
        return false;
    }

    private final function getArchiveCameras()
    {
        $cameras = array();

        if ($this->getLoginUser()) {
            if ($this->getCSnowman()->isArchiveOnlyAccessibleCameras()) {
                $cameras = camera::getAccessableCamerasByUser(
                    $this->getCameras(),
                    $this->getLoginUser()
                );
            } else {
                $cameras = $this->getCameras();
            }
        } else {
            if ($this->getCSnowman()->isArchiveOnlyFromSecureHost()) {
                if ($this->isSecureHost()) {
                    $cameras = $this->getCameras();
                }
            } else {
                $cameras = $this->getCameras();
            }
        }
        return $cameras;
    }

    /**
     * Archive the cameras.
     * @return string log information
     */
    public final function createArchive()
    {
        $archivestring = "";
        $cameras = $this->getArchiveCameras();

        foreach ($cameras as $camera) {
            /** @var Camera $camera */
            $archivestring .= $camera->createArchive() . "\n";
        }

        $this->refreshChmod();

        return $archivestring;
    }

    /**
     * Purge the archive.
     * @return string log information
     */
    public final function purgeArchive()
    {
        $archivestring = "";
        $cameras = $this->getArchiveCameras();

        foreach ($cameras as $camera) {
            /** @var Camera $camera */
            $archivestring .= $camera->purgeArchive() . "\n";
        }

        $this->refreshChmod();

        return $archivestring;
    }

}

