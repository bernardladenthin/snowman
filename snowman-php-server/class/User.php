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

namespace net\ladenthin\snowman\phpserver;

/**
 * Class for an user.
 */
class User
{
    private $cUser;
    /**
     * Constructor.
     * @param \stdClass $stdClass Configuration object.
     */
    public function __construct($stdClass)
    {
        $this->cUser = new CUser($stdClass);
    }

    /**
     * Returns the configuration.
     * @return CUser
     */
    public function getCUser() {
        return $this->cUser;
    }

    /**
     * Check for correct login data and user is not disabled.
     * @param string $name name from the user
     * @param string $password password from the user (plain text)
     * @return boolean true if login tupel are correct, otherwise false.
     */
    public final function isLoginOK($name, $password)
    {
        if ($this->getCUser()->getPasswordHashAlgorithm()) {
            $password = hash($this->getCUser()->getPasswordHashAlgorithm(), $password);
        }

        if (
            !$this->getCUser()->isLoginDisabled()
            && $this->getCUser()->getName() == $name
            && $this->getCUser()->getPassword() == $password
        ) {
            return true;
        }
        return false;
    }

    /**
     * Check if the user is in a single group.
     * @param array $groups array of strings with group names.
     * @return boolean true if the user is in the group array, otherwise false.
     */
    public final function isInGroups($groups)
    {
        foreach ($groups as $group) {
            if (in_array($group, $this->getCUser()->getGroups())) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the user is in array.
     * @param array $users array of strings with group names.
     * @return boolean true if the user is in the group array, otherwise false.
     */
    public final function isInUsers($users)
    {
        foreach ($users as $user) {
            if ($user == $this->getCUser()->getName()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return an user object if valid user found.
     * @param array $users
     * @param string $name
     * @param string $password
     * @return User return a user object if success, otherwise null
     */
    public final static function getObjByNameAndPassword(
        $users, $name, $password
    )
    {
        foreach ($users as $user) {
            /** @var User $user */
            if ($user->isLoginOK($name, $password)) {
                return $user;
            }
        }
        return null;
    }

}

