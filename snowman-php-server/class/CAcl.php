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
 * Configuration for an ACL.
 */
class CAcl
{
    private $users;
    private $groups;

    /**
     * Constructor.
     * @param \stdClass $stdClass Configuration object.
     */
    public function __construct($stdClass)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->users = new CAclList($stdClass->users);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->groups = new CAclList($stdClass->groups);
    }

    /**
     * Returns the users.
     * Higher rights priority as {@link $getGroups}.
     * @return CAclList
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Returns the groups.
     * Lower rights priority as {@link $getUsers}.
     * @return CAclList
     */
    public function getGroups()
    {
        return $this->groups;
    }

}
