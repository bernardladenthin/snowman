<?php
/**
 * snowman-php-server - PHP script to run a snowman server.
 * https://github.com/bernardladenthin/snowman
 *
 * Copyright (C) 2014 Bernard Ladenthin <bernard.ladenthin@gmail.com>
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
 * Configuration for the snowman-php-server.
 */
class CSnowman
{
    private $version;
    private $versionDate;
    private $owner;
    private $lcTime;
    private $defaultTimezone;

    /* URLs */
    private $imprintUrl;
    private $ajaxApiUrl;
    private $liveViewUrl;
    private $downloadArchiveUrl;

    /* special flags */
    private $archiveOnlyFromSecureHost;
    private $archiveOnlyAccessibleCameras;
    private $secureHosts;
    private $xSendFile;

    /**
     * Constructor.
     * @param \stdClass $stdClass Configuration object.
     */
    public function __construct($stdClass)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->version = validateString($stdClass->version);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->versionDate = validateString($stdClass->versionDate);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->owner = validateString($stdClass->owner);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->lcTime = validateString($stdClass->lcTime);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->defaultTimezone = validateString($stdClass->defaultTimezone);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->archiveOnlyFromSecureHost = boolval($stdClass->archiveOnlyFromSecureHost);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->archiveOnlyAccessibleCameras = boolval($stdClass->archiveOnlyAccessibleCameras);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->secureHosts = validateArray($stdClass->secureHosts);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->xSendFile = boolval($stdClass->xSendFile);

        /** @noinspection PhpUndefinedFieldInspection */
        $url = isLocalIp($_SERVER["REMOTE_ADDR"]) ? $stdClass->localUrl : $stdClass->noLocalUrl;

        /** @noinspection PhpUndefinedFieldInspection */
        $this->imprintUrl = validateString($url->imprintUrl);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->ajaxApiUrl = validateArray($url->ajaxApiUrl);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->liveViewUrl = validateString($url->liveViewUrl);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->downloadArchiveUrl = validateString($url->downloadArchiveUrl);
    }

    /**
     * Returns the version.
     * @return string should be in format x.y.z, but not specified.
     */
    public final function getVersion()
    {
        return $this->version;
    }

    /**
     * Returns the version date.
     * @return string should be in format yyyy-mm-dd, but not specified.
     */
    public final function getVersiondate()
    {
        return $this->versionDate;
    }

    /**
     * Returns the website owner.
     * @return string
     */
    public final function getOwner()
    {
        return $this->owner;
    }

    /**
     * Returns the lc time.
     * @return string
     */
    public final function getLcTime()
    {
        return $this->lcTime;
    }

    /**
     * Returns the default timezone.
     * @return string
     */
    public final function getDefaultTimezone()
    {
        return $this->defaultTimezone;
    }

    /**
     * Returns the imprint url.
     * @return string
     */
    public final function getImprintUrl()
    {
        return $this->imprintUrl;
    }

    /**
     * Returns the api url.
     * @return string
     */
    public final function getAjaxApiUrl()
    {
        return $this->ajaxApiUrl;
    }

    /**
     * Returns the liveview url.
     * @return string
     */
    public final function getLiveViewUrl()
    {
        return $this->liveViewUrl;
    }

    /**
     * Returns the download archive url.
     * @return string
     */
    public final function getDownloadArchiveUrl()
    {
        return $this->downloadArchiveUrl;
    }

    /**
     * Is the policy set to trigger a archive execution only by registred
     * hosts.
     * @return boolean
     */
    public final function isArchiveOnlyFromSecureHost()
    {
        return $this->archiveOnlyFromSecureHost;
    }

    /**
     * Is the policy set to trigger a archive only for accessible cameras
     * for the calling user.
     * @return boolean
     */
    public final function isArchiveOnlyAccessibleCameras()
    {
        return $this->archiveOnlyAccessibleCameras;
    }

    /**
     * Returns the hosts that are defined as secure.
     * @return array array of strings
     */
    public final function getSecureHosts()
    {
        return $this->secureHosts;
    }

    /**
     * Is the policy set an archive will be downloaded via mod-xsendfile.
     * @return boolean
     */
    public final function isXSendFile()
    {
        return $this->xSendFile;
    }

}
