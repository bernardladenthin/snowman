<?php
/**
 * snowman-php-server - PHP script to run a snowman server.
 * https://github.com/bernardladenthin/snowman
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
 * Main file to download an archive file.
 */
$disable_ob_gzhandler = true;
require_once('globalconfig.php');

/**
 * File for Ajax interaction
 */
try {

    $downloadarchive = new \net\ladenthin\snowman\phpserver\Downloadarchive($snowman, JSON_decode($json));

    if ($downloadarchive->isSafeArchiveRequest()) {
        $downloadarchive->sendArchive();
    } else {
        echo "error";
    }

    ob_flush();

} catch (Exception $e) {
    echo 'Exception: ', $e->getMessage(), "\n";
    exit;
}

