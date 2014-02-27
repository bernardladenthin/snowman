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
class CArchive
{
    private $dir;
    private $maxFiles;
    private $packageFormatZip;
    private $packageFormatZipName;
    private $packageFormatCustomExec;
    private $packageFormatCustomExecImageFormat;
    private $packageFormatCustomExecDateParameter;
    private $dirDate;
    private $imageUnlink;
    private $logFile;
    private $extensions;
    private $extensionsCaseSensitive;
    private $purgeTimeLimit;

    /**
     * Constructor.
     * @param \stdClass $stdClass Configuration object.
     */
    public function __construct($stdClass)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->dir = validateString($stdClass->dir);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->dirDate = validateString($stdClass->dirDate);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->maxFiles = intval($stdClass->maxFiles);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->packageFormatZip = boolval($stdClass->packageFormatZip);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->packageFormatZipName = validateString($stdClass->packageFormatZipName);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->packageFormatCustomExec = validateString($stdClass->packageFormatCustomExec);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->packageFormatCustomExecImageFormat = validateString($stdClass->packageFormatCustomExecImageFormat);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->packageFormatCustomExecDateParameter = validateString($stdClass->packageFormatCustomExecDateParameter);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->imageUnlink = boolval($stdClass->imageUnlink);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->logFile = validateString($stdClass->logFile);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->extensions = validateArray($stdClass->extensions);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->extensionsCaseSensitive = boolval($stdClass->extensionsCaseSensitive);
    }

    /**
     * Returns the dir for the archive directory.
     * @return string archive path
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Returns the maximum archive files for one archive.
     * @return integer maximum files
     */
    public function getMaxFiles()
    {
        return $this->maxFiles;
    }

    /**
     * Returns the flag to use a zip container for an archive.
     * @return boolean
     */
    public function isPackageFormatZip()
    {
        return $this->packageFormatZip;
    }

    /**
     * Returns the flag to use a custom date for the filename.
     * @return boolean
     */
    public function getPackageFormatZipName()
    {
        return $this->packageFormatZipName;
    }

    /**
     * Returns the custom execution to archive.
     * An empty string indicates no custom execution.
     * @return string
     */
    public function getPackageFormatCustomExec()
    {
        return $this->packageFormatCustomExec;
    }

    /**
     * Returns the custom format for moved files before trigger the custom execution.
     * @return string
     */
    public function getPackageFormatCustomExecImageFormat()
    {
        return $this->packageFormatCustomExecImageFormat;
    }

    /**
     * Returns the custom date for the output name of the custom execution result.
     * @return string
     */
    public function getPackageFormatCustomExecDateParameter()
    {
        return $this->packageFormatCustomExecDateParameter;
    }

    /**
     * Returns the archive dir date pattern.
     * @return string
     */
    public function getDirDate()
    {
        return $this->dirDate;
    }

    /**
     * Returns the flag to unlink (remove) images after archive.
     * @return boolean
     */
    public function isImageUnlink()
    {
        return $this->imageUnlink;
    }

    /**
     * Returns the path for the archive log file.
     * @return boolean
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * Returns the file extension for an archive file.
     * @return String[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Returns the flag file extension in case sensitive mode.
     * @return boolean
     */
    public function isExtensionsCaseSensitive()
    {
        return $this->extensionsCaseSensitive;
    }

    /**
     * Returns the time limit to remove outdated archives.
     * @return integer
     */
    public function getPurgeTimeLimit()
    {
        return $this->purgeTimeLimit;
    }

}
