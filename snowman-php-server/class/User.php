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
 * Class for a user.
*/
class User {

	/**
	 * Name of the user.
	 * @var string
	 */
	private $name;

	/**
	 * Password of the user.
	 * @var string
	 */
	private $password;

	/**
	 * The password hash algorithm.
	 * Possible values: plain, sha1 and md5.
	 * @link http://www.php.net/manual/de/function.hash.php
	 * @var string
	 */
	private $passwordHashAlgorithm;

	/**
	 * Groups of the user
	 * @var array
	 */
	private $groups;

	/**
	 * User is disabled.
	 * @var boolean
	 */
	private $loginDisabled;

	/**
	 * Construct a user from stdClass object.
	 * @link name
	 * @link password
	 * @link passwordHashAlgorithm
	 * @link groups
	 * @link loginDisabled
	 * @param StdClass $user
	 */
	public function user(
		$stdClass
	) {
		$this->name = $stdClass->name;
		$this->password = $stdClass->password;
		$this->passwordHashAlgorithm = $stdClass->passwordHashAlgorithm;
		if(!is_array($stdClass->groups)) {
			printf('Warning: $groups should be an array.');
		}
		$this->groups = $stdClass->groups;
		$this->loginDisabled = $stdClass->loginDisabled;
	}

	/**
	 * Getter for <code>$name</code>.
	 * @return string
	 */
	public final function getName() {
		return $this->name;
	}

	/**
	 * Getter for <code>$password</code>.
	 * @link $password
	 * @return string
	 */
	public final function getPassword() {
		return $this->password;
	}

	/**
	 * Getter for <code>$groups</code>.
	 * @link $groups
	 * @return array
	 */
	public final function getGroups() {
		return $this->groups;
	}

	/**
	 * Getter for <code>$passwordHashAlgorithm</code>.
	 * @link $passwordHashAlgorithm
	 * @return string
	 */
	public final function getPasswordHashAlgorithm() {
		return $this->passwordHashAlgorithm;
	}

	/**
	 * Getter for <code>$loginDisabled</code>.
	 * @return boolean
	 */
	public final function getLoginDisabled() {
		$this->loginDisabled;
	}

	/**
	 * Check for correct login data and user is not disabled.
	 * @param string $name name from the user
	 * @param string $password password from the user (plain text)
	 * @return boolean true if login tupel are correct, otherwise false.
	 */
	public final function isLoginOK($name, $password) {
		if($this->getPasswordHashAlgorithm()) {
			$password = hash($this->getPasswordHashAlgorithm(), $password);
		}

		if(
			!	$this->getLoginDisabled()
			&&	$this->getName() == $name
			&&	$this->getPassword() == $password
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
	public final function isInGroups($groups) {
		foreach($groups as $group) {
			if(in_array($group, $this->getGroups())) {
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
	public final function isInUsers($users) {
		foreach($users as $user) {
			if($user == $this->getName()) {
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
	 * @return mixed return a user object if success, otherwise false
	 */
	public final static function getObjByNameAndPassword(
		$users, $name, $password
	) {
		foreach($users as $user) {
			if($user->isLoginOK($name, $password)) {
				return $user;
			}
		}
		return false;
	}

}

