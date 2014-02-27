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
 * Class for an AJAX request.
 */
class Ajax
{
    /**
     * The snowman instance.
     * @link snowman
     * @var Snowman snowman
     */
    private $snowman;

    /**
     * JSON request from client.
     */
    private $JSONRequest;

    /**
     * JSON response for client.
     */
    private $snowmanresponse;

    /**
     * Constructor
     * @param Snowman $snowman
     * @param \stdClass $JSONRequest The request object.
     */
    public function __construct($snowman, $JSONRequest)
    {
        $this->snowman = $snowman;
        $this->JSONRequest = $JSONRequest;
        $this->snowmanresponse = new \stdClass();

        /** @noinspection PhpUndefinedFieldInspection */
        if (isset($this->JSONRequest->login)) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->requestNodeLogin($this->JSONRequest->login);
        }

        /** @noinspection PhpUndefinedFieldInspection */
        if (isset($this->JSONRequest->logout)) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->requestNodeLogout($this->JSONRequest->logout);
        }

        /** @noinspection PhpUndefinedFieldInspection */
        if (isset($this->JSONRequest->serverinformation)) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->requestNodeServerinformation(
                $this->JSONRequest->serverinformation
            );
        }

        /** @noinspection PhpUndefinedFieldInspection */
        if (isset($this->JSONRequest->camerainformation)) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->requestNodeCamerainformation(
                $this->JSONRequest->camerainformation
            );
        }

        /** @noinspection PhpUndefinedFieldInspection */
        if (isset($this->JSONRequest->command)) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->requestNodeCommand($this->JSONRequest->command);
        }

        /** @noinspection PhpUndefinedFieldInspection */
        if (isset($this->JSONRequest->getArchiveListing)) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->requestNodeGetArchiveListing($this->JSONRequest->getArchiveListing);
        }
    }

    /**
     * Parse login node.
     * @param \stdClass $node
     * @return void
     */
    private final function requestNodeLogin($node)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        if ($this->snowman->isLoginOK($node->username, $node->password)) {
            $this->addResponseLoginTrue();
        } else {
            $this->addResponseLoginFalse();
        }
    }

    /**
     * Add success login status to response.
     * @return void
     */
    private final function addResponseLoginTrue()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->snowmanresponse->login = new \stdClass();
        /** @noinspection PhpUndefinedFieldInspection */
        $this->snowmanresponse->login->status = true;
        /** @noinspection PhpUndefinedFieldInspection */
        $this->snowmanresponse->login->sessionid = session_id();

        /** @noinspection PhpUndefinedFieldInspection */
        $this->addResponseUsername($this->snowmanresponse->login);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->addResponseUsergroups($this->snowmanresponse->login);
    }

    /**
     * Add username to node.
     * @param \stdClass $parent
     * @return void
     */
    private final function addResponseUsername($parent)
    {
        $name = $this->snowman->getLoginUser()->getCUser()->getName();
        $parent->username = $name;
    }

    /**
     * Add usergroups to node.
     * @param \stdClass $parent
     * @return void
     */
    private final function addResponseUsergroups($parent)
    {
        $groups = $this->snowman->getLoginUser()->getCUser()->getGroups();
        $parent->groups = array();
        foreach ($groups as $group) {
            $parent->groups[] = $group;
        }
    }

    /**
     * Add false status to response.
     * @return void
     */
    private final function addResponseLoginFalse()
    {
        $this->snowmanresponse->login = new \stdClass();
        /** @noinspection PhpUndefinedFieldInspection */
        $this->snowmanresponse->login->status = false;
    }

    /**
     * Parse logout node.
     * @param \stdClass $parent
     * @return void
     */
    private final function requestNodeLogout($parent)
    {
        global $isloginok;
        $this->addResponseLogoutTrue();
        $this->snowman->session_destroy();
        $isloginok = false;
    }

    /**
     * Add success logout status to response.
     * @return void
     */
    private final function addResponseLogoutTrue()
    {
        $this->snowmanresponse->logout = new \stdClass();
        /** @noinspection PhpUndefinedFieldInspection */
        $this->snowmanresponse->logout->status = true;
    }

    /**
     * Parse serverinformation node.
     * @param \stdClass $node
     * @return void
     */
    private final function requestNodeServerinformation($node)
    {
        $this->addResponseServerinformation();
    }

    /**
     * Add serverinformation to response.
     * @return void
     */
    private final function addResponseServerinformation()
    {
        $this->snowmanresponse->serverinformation = new \stdClass();

        /** @noinspection PhpUndefinedFieldInspection */
        $this->snowmanresponse->serverinformation->ajaxApiUrl =
            $this->snowman->getCSnowman()->getAjaxApiUrl();

        /** @noinspection PhpUndefinedFieldInspection */
        $this->snowmanresponse->serverinformation->imprintUrl =
            $this->snowman->getCSnowman()->getImprintUrl();

        /** @noinspection PhpUndefinedFieldInspection */
        $this->snowmanresponse->serverinformation->liveviewUrl =
            $this->snowman->getCSnowman()->getLiveViewUrl();

        /** @noinspection PhpUndefinedFieldInspection */
        $this->snowmanresponse->serverinformation->downloadarchiveurl =
            $this->snowman->getCSnowman()->getDownloadArchiveUrl();

        /** @noinspection PhpUndefinedFieldInspection */
        $this->snowmanresponse->serverinformation->version =
            $this->snowman->getCSnowman()->getVersion();

        /** @noinspection PhpUndefinedFieldInspection */
        $this->snowmanresponse->serverinformation->versionDate =
            $this->snowman->getCSnowman()->getVersionDate();

        /** @noinspection PhpUndefinedFieldInspection */
        $this->snowmanresponse->serverinformation->owner =
            $this->snowman->getCSnowman()->getOwner();
    }

    /**
     * Parse camerainformation node.
     * @param \stdClass $node
     * @return void
     */
    private final function requestNodeCamerainformation($node)
    {
        $this->addResponseCamerainformation();
    }

    /**
     * Add camerainformation to response.
     * @return void
     */
    private final function addResponseCamerainformation()
    {
        $accessgrantedcameras = camera::getAccessableCamerasByUser(
            $this->snowman->getCameras(),
            $this->snowman->getLoginUser()
        );

        /** @noinspection PhpUndefinedFieldInspection */
        $this->snowmanresponse->camerainformation = new \stdClass();
        /** @noinspection PhpUndefinedFieldInspection */
        $this->snowmanresponse->camerainformation->cameras = array();

        foreach ($accessgrantedcameras as $camera) {
            /** @var Camera $camera*/
            /** @noinspection PhpUndefinedFieldInspection */
            $currentcamera =
                $this->snowmanresponse->camerainformation->cameras[] =
                new \stdClass;

            $currentcamera->url = urlencode($camera->getCCamera()->getName());
            $currentcamera->name = $camera->getCCamera()->getName();
            $currentcamera->refresh = $camera->getCCamera()->getRefresh();
        }
    }

    /**
     * Parse command node.
     * @param \stdClass $node
     * @return void
     */
    private final function requestNodeCommand($node)
    {
        global $isloginok;
        $this->snowmanresponse->command = new \stdClass();

        if (isset($node->createarchive)) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->snowmanresponse->command->createarchive = new \stdClass();
            /** @noinspection PhpUndefinedFieldInspection */
            $this->snowmanresponse->command->createarchive->log =
                $this->snowman->createArchive();
            /** @noinspection PhpUndefinedFieldInspection */
            $this->snowmanresponse->command->createarchive->success = true;
        }

        if (isset($node->refreshchmod)) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->snowmanresponse->command->refreshchmod = new \stdClass();
            if ($isloginok) {
                $this->snowman->refreshChmod();
                /** @noinspection PhpUndefinedFieldInspection */
                $this->snowmanresponse->command->refreshchmod->success = true;
            } else {
                /** @noinspection PhpUndefinedFieldInspection */
                $this->snowmanresponse->command->refreshchmod->success = false;
            }
        }

    }

    /**
     * Parse getArchiveListing node.
     * @param \stdClass $node
     * @return void
     */
    private final function requestNodeGetArchiveListing($node)
    {
        global $isloginok;
        $this->snowmanresponse->archiveListing = new \stdClass();

        if ($isloginok) {
            $this->snowmanresponse->archiveListing = array();

            $accessgrantedcameras = camera::getAccessableCamerasByUser(
                $this->snowman->getCameras(),
                $this->snowman->getLoginUser()
            );

            foreach ($accessgrantedcameras as $camera) {
                /** @var Camera $camera*/
                $archiveListing = $camera->getArchiveListing();

                /** @noinspection PhpUndefinedFieldInspection */
                $this->snowmanresponse->archiveListing[$camera->getCCamera()->getName()] =
                    $archiveListing;
            }

        } else {
            $this->snowmanresponse->archiveListing = false;
        }

    }

    /**
     * Returns the snowman response.
     * @link $snowmanresponse
     * @return \stdClass
     */
    public final function getSnowmanResponse()
    {
        return $this->snowmanresponse;
    }
}

