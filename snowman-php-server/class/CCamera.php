<?php
/**
 * snowman-php-server - PHP script to run a snowman server.
 * http://code.google.com/p/snowman/
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
 * Configuration for a camera.
 */
class CCamera
{
    private $name;
    private $dir;
    private $refresh;
    private $delay;
    private $width;
    private $height;
    private $toptextbranding;
    private $bottomtextbranding;
    private $cAcl;
    private $tmpPathPrepend;
    private $imageExtensions;
    private $imageExtensionsCaseSensitive;
    private $camerawriterFormatPosixMillis;
    private $maximumFilesystemFileArray;
    private $archive;
    private $logRawCameraUpload;

    /**
     * Constructor.
     * @param \stdClass $stdClass Configuration object.
     */
    public function __construct($stdClass)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->name = validateString($stdClass->name);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->toptextbranding = validateString($stdClass->toptextbranding);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->bottomtextbranding = validateString($stdClass->bottomtextbranding);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->dir = validateString($stdClass->dir);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->refresh = intval($stdClass->refresh);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->delay = intval($stdClass->delay);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->width = intval($stdClass->width);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->height = intval($stdClass->height);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->cAcl = new CAcl($stdClass->acl);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->tmpPathPrepend = validateString($stdClass->tmpPathPrepend);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->archive = new CArchive($stdClass->archive);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->imageExtensions = validateArray($stdClass->imageExtensions);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->imageExtensionsCaseSensitive = boolval($stdClass->imageExtensionsCaseSensitive);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->camerawriterFormatPosixMillis = boolval($stdClass->camerawriterFormatPosixMillis);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->maximumFilesystemFileArray = intval($stdClass->maximumFilesystemFileArray);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->logRawCameraUpload = boolval($stdClass->logRawCameraUpload);
    }

    /**
     * Returns the name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the file directory for images.
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Returns the refresh time for images.
     * @return integer
     */
    public function getRefresh()
    {
        return $this->refresh;
    }

    /**
     * Returns the number of latest images are ignored to get a viewing delay.
     * @return integer
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * Returns the image width.
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Returns the image height.
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Returns the top branding.
     * @return String
     */
    public function getTopTextBranding()
    {
        return $this->toptextbranding;
    }

    /**
     * Return the bottom branding.
     * @return String
     */
    public function getBottomTextBranding()
    {
        return $this->bottomtextbranding;
    }

    /**
     * Returns the ACL.
     * @return CAcl
     */
    public function getCAcl()
    {
        return $this->cAcl;
    }

    /**
     * Returns the prepend path for the tmp path creation.
     * @return String
     */
    public function getTmpPathPrepend()
    {
        return $this->tmpPathPrepend;
    }

    /**
     * Returns the archive configuration.
     * @return CArchive
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * Returns the flag to acitvate the raw log for a cameraupload request.
     * @return boolean
     */
    public function isLogRawCameraUpload()
    {
        return $this->logRawCameraUpload;
    }

    /**
     * Returns the array containing strings of file extension for a camera image.
     * @return String[]
     */
    public function getImageExtensions()
    {
        return $this->imageExtensions;
    }

    /**
     * Check if the image extension is checked in case sensitive mode.
     * @return boolean
     */
    public function isImageExtensionsCaseSensitive()
    {
        return $this->imageExtensionsCaseSensitive;
    }

    /**
     * Check if the POSIX millis format should used to write an image.
     * @return boolean
     */
    public function isCamerawriterFormatPosixMillis()
    {
        return $this->camerawriterFormatPosixMillis;
    }

    /**
     * Returns the maximum count for a file array from the filesystem.
     * @return integer
     */
    public function getMaximumFilesystemFileArray()
    {
        return $this->maximumFilesystemFileArray;
    }
}
