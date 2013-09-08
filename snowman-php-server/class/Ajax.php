<?php
/**
 * snowman-php-server - PHP script to run a snowman server.
 * http://code.google.com/p/snowman/
 *
 * Copyright (C) 2013 Bernard Ladenthin <bernard@ladenthin.net>
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
 * Class for AJAX request
*/
class Ajax {
	/**
	 * The snowman instance.
	 * @link snowman
	 * @var snowman
	 */
	private $snowman;

	/**
	 * JSON request from client.
	 * @var StdClass
	 */
	private $JSONResquest;

	/**
	 * JSON response for client.
	 * @var StdClass
	 */
	private $snowmanresponse;

	/**
	 * Constructor
	 * {@link JSONResquest}
	 * @param StdClass $JSONResquest
	 * @return void
	 */
	public function ajax(
		$snowman,
		$JSONResquest
	) {
		$this->snowman = $snowman;
		$this->JSONResquest = $JSONResquest;
		$this->snowmanresponse = new StdClass();
	}

	/**
	 * Getter for <code>$snowmanresponse</code<
	 * @link $snowmanresponse
	 * @return string
	 */
	public final function getSnowmanResponse() {
		return $this->snowmanresponse;
	}

	/**
	 * Parse request and generate response.
	 * @link JSONResquest
	 * @link JSONResponse
	 * @return void
	 */
	public final function parseRequestGenerateResponse() {

		if(isset($this->JSONResquest->login)) {
			$this->requestNodeLogin($this->JSONResquest->login);
		}

		if(isset($this->JSONResquest->logout)) {
			$this->requestNodeLogout($this->JSONResquest->logout);
		}

		if(isset($this->JSONResquest->serverinformation)) {
			$this->requestNodeServerinformation(
				$this->JSONResquest->serverinformation
			);
		}

		if(isset($this->JSONResquest->camerainformation)) {
			$this->requestNodeCamerainformation(
				$this->JSONResquest->camerainformation
			);
		}

		if(isset($this->JSONResquest->command)) {
			$this->requestNodeCommand($this->JSONResquest->command);
		}

		if(isset($this->JSONResquest->getArchiveListing)) {
			$this->requestNodeGetArchiveListing($this->JSONResquest->getArchiveListing);
		}
	}

	/**
	 * Parse login node.
	 * @return void
	 */
	private final function requestNodeLogin($node) {
		if($this->snowman->isLoginOK($node->username, $node->password)) {
			$this->addResponseLoginTrue();
		} else {
			$this->addResponseLoginFalse();
		}
	}

	/**
	 * Parse serverinformation node.
	 * @return void
	 */
	private final function requestNodeServerinformation($node) {
		$this->addResponseServerinformation();
	}

	/**
	 * Parse camerainformation node.
	 * @return void
	 */
	private final function requestNodeCamerainformation($node) {
		$this->addResponseCamerainformation();
	}

	/**
	 * Parse command node.
	 * @return void
	 */
	private final function requestNodeCommand($node) {
		global $isloginok;
		$this->snowmanresponse->command = new StdClass();

		if(isset($node->createarchive)) {
			$this->snowmanresponse->command->createarchive = new StdClass();
			$this->snowmanresponse->command->createarchive->log =
				$this->snowman->createArchive();
			$this->snowmanresponse->command->createarchive->success = true;
		}

		if(isset($node->refreshchmod)) {
			$this->snowmanresponse->command->refreshchmod = new StdClass();
			if($isloginok) {
				$this->snowman->refreshChmod();
				$this->snowmanresponse->command->refreshchmod->success = true;
			} else {
				$this->snowmanresponse->command->refreshchmod->success = false;
			}
		}

	}

	/**
	 * Parse getArchiveListing node.
	 * @return void
	 */
	private final function requestNodeGetArchiveListing($node) {
		global $isloginok;
		$this->snowmanresponse->archiveListing = new StdClass();

		if($isloginok) {
			$this->snowmanresponse->archiveListing = array();

			$accessgrantedcameras = camera::getAccessableCamerasByUser(
				$this->snowman->getCameras(),
				$this->snowman->getLoginUser()
			);

			foreach($accessgrantedcameras as $camera) {
				$archiveListing = $camera->getArchiveListing();
				//array_multisort($archiveListing);

				$this->snowmanresponse->archiveListing[$camera->getName()] =
					$archiveListing;
			}

		} else {
			$this->snowmanresponse->archiveListing = false;
		}

	}

	/**
	 * Parse logout node.
	 * @return void
	 */
	private final function requestNodeLogout($parent) {
		global $isloginok;
		$this->addResponseLogoutTrue();
		$this->snowman->session_destroy();
		$isloginok = false;
	}

	/**
	 * Add success login status to response.
	 * @return void
	 */
	private final function addResponseLoginTrue() {
		$this->snowmanresponse->login = new StdClass();
		$this->snowmanresponse->login->status = true;
		$this->snowmanresponse->login->sessionid = session_id();

		$this->addResponseUsername($this->snowmanresponse->login);
		$this->addResponseUsergroups($this->snowmanresponse->login);
	}

	/**
	 * Add success logout status to response.
	 * @return void
	 */
	private final function addResponseLogoutTrue() {
		$this->snowmanresponse->logout = new StdClass();
		$this->snowmanresponse->logout->status = true;
	}

	/**
	 * Add username to node.
	 * @return void
	 */
	private final function addResponseUsername($parent) {
		$name = $this->snowman->getLoginUser()->getName();
		$parent->username = $name;
	}

	/**
	 * Add usergroups to node.
	 * @return void
	 */
	private final function addResponseUsergroups($parent) {
		$groups = $this->snowman->getLoginUser()->getGroups();
		$parent->groups = array();
		foreach($groups as $group) {
			$parent->groups[] = $group;
		}
	}

	/**
	 * Add false status to response.
	 * @return void
	 */
	private final function addResponseLoginFalse() {
		$this->snowmanresponse->login = new StdClass();
		$this->snowmanresponse->login->status = false;
	}

	/**
	 * Add camerainformation to response.
	 * @return void
	 */
	private final function addResponseCamerainformation() {
		$accessgrantedcameras = camera::getAccessableCamerasByUser(
			$this->snowman->getCameras(),
			$this->snowman->getLoginUser()
		);

		$this->snowmanresponse->camerainformation = new StdClass();
		$this->snowmanresponse->camerainformation->cameras = array();

		foreach($accessgrantedcameras as $camera) {
			$currentcamera =
				$this->snowmanresponse->camerainformation->cameras[] =
					new StdClass;

			$currentcamera->url = urlencode($camera->getName());
			$currentcamera->name = $camera->getName();
			$currentcamera->refresh = $camera->getRefresh();
		}
	}

	/**
	 * Add serverinformation to response.
	 * @return void
	 */
	private final function addResponseServerinformation() {
		$this->snowmanresponse->serverinformation = new StdClass();

		$this->snowmanresponse->serverinformation->ajaxApiUrl =
			$this->snowman->getAjaxApiUrl();

		$this->snowmanresponse->serverinformation->imprintUrl =
			$this->snowman->getImprintUrl();

		$this->snowmanresponse->serverinformation->liveviewUrl =
			$this->snowman->getLiveviewUrl();

		$this->snowmanresponse->serverinformation->version =
			$this->snowman->getVersion();

		$this->snowmanresponse->serverinformation->versionDate =
			$this->snowman->getVersionDate();

		$this->snowmanresponse->serverinformation->owner =
			$this->snowman->getOwner();
	}
}

