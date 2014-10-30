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
 * Configuration for an user.
 */
class CUser
{
    private $name;
    private $password;
    private $groups;
    private $passwordHashAlgorithm;
    private $loginDisabled;

    /**
     * Constructor.
     * @param \stdClass $stdClass Configuration object.
     */
    public function __construct($stdClass)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->name = validateString($stdClass->name);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->password = validateString($stdClass->password);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->passwordHashAlgorithm = validateString($stdClass->passwordHashAlgorithm);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->groups = validateArray($stdClass->groups);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->loginDisabled = boolval($stdClass->loginDisabled);
    }

    /**
     * Returns the name.
     * @return string
     */
    public final function getName()
    {
        return $this->name;
    }

    /**
     * Returns the password.
     * @return string
     */
    public final function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the groups.
     * @return array
     */
    public final function getGroups()
    {
        return $this->groups;
    }

    /**
     * Returns the password hash algorithm.
     * @link http://www.php.net/manual/de/function.hash.php
     * @return string Possible values: plain, sha1 and md5.
     */
    public final function getPasswordHashAlgorithm()
    {
        return $this->passwordHashAlgorithm;
    }

    /**
     * Returns the login disabled flag.
     * @return boolean
     */
    public final function isLoginDisabled()
    {
        $this->loginDisabled;
    }

}
