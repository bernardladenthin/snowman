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
 * Configuration for an ACL list.
 */
class CAclList
{
    private $deny;
    private $allow;

    /**
     * Constructor.
     * @param \stdClass $stdClass Configuration object.
     */
    public function __construct($stdClass)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->deny = validateArray($stdClass->deny);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->allow = validateArray($stdClass->allow);
    }

    /**
     * Returns the deny list.
     * Higher rights priority as {@link $getAllow}.
     * @return String[]
     */
    public function getDeny()
    {
        return $this->deny;
    }

    /**
     * Returns the allow list.
     * Lower rights priority as {@link $getDeny}.
     * @return String[]
     */
    public function getAllow()
    {
        return $this->allow;
    }

}
