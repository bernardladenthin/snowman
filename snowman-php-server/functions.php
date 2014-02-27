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
 * Functions file.
 */

if( ! function_exists('boolval'))
{
    /**
     * Get the boolean value of a variable
     *
     * @param mixed $var The scalar value being converted to a boolean.
     * @return boolean The boolean value of var.
     */
    function boolval($var)
    {
        return !! $var;
    }
}

/**
 * Remove the files "." and ".."
 * @param array $entries an array of strings
 */
function removeDotFiles($entries)
{
    return array_diff($entries, array('.', '..'));
}

/**
 * Return true if the filename equals "." or ".."
 * @param boolean true if the file is an dotfile
 */
function isDotFile($filename)
{
    return ($filename != "." && $filename != "..") ? false : true;
}

/**
 * Return true if the needle is in the array. This function is not case sensitive.
 * @param boolean true if the needle is in the haystack.
 */
function in_arrayi($needle, $haystack)
{
    return in_array(strtolower($needle), array_change_key_case($haystack));
}

/**
 * Return true if the array contains an array.
 * @param boolean true if the array contains an array.
 */
function contains_array($array)
{
    foreach ($array as $value) {
        if (is_array($value)) {
            return true;
        }
    }
    return false;
}

/**
 * Create the dir recursive if not exist.
 * @param string $path the directory path
 */
function nis_dir_mkdir($path)
{
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

/**
 * Chmods files and folders with different permissions.
 * This is an all-PHP alternative to using: \n
 * <i>exec("find ".$path." -type f -exec chmod 644 {} \;");</i> and
 * <i>exec("find ".$path." -type d -exec chmod 755 {} \;");</i>
 * The permission levels has to be entered in octal format, which
 * normally means adding a zero ("0") in front of the permission level.
 *
 * @author Jeppe Toustrup (tenzer at tenzer dot dk)
 * @author Bernard Ladenthin (bernard.ladenthin@gmail.com)
 * @link http://php.net/chmod More info about chmod on php.net
 * @param String $path An either relative or absolute path to a file or directory
 * which should be processed.
 * @param integer $filePerm The permissions any found files should get. In Octal
 * @param integer $dirPerm The permissions any found folder should get. In Octal
 * @return boolean returns true if the path is found, otherwise false
 **/
function recursiveChmod($path, $filePerm = 0644, $dirPerm = 0755)
{
    //Check if the path exists
    if (!file_exists($path)) {
        return false;
    }
    //See whether this is a file
    if (is_file($path)) {
        //Chmod the file with our given filepermissions
        chmod($path, $filePerm);
        //If this is a directory...
    } elseif (is_dir($path)) {
        //Then get an array of the contents
        $entries = scandir($path);
        //Remove "." and ".." from the list
        $entries = removeDotFiles($entries);
        //Parse every result...
        foreach ($entries as $entry) {
            //And call this function again recursively,
            //with the same permissions
            recursiveChmod($path . "/" . $entry, $filePerm, $dirPerm);
        }
        //When we are done with the contents of the directory,
        //we chmod the directory itself
        chmod($path, $dirPerm);
    }
    //Everything seemed to work out well, return true
    return true;
}

function millitime()
{
    $milli = 1000;
    return microtime(true) * $milli;
}

function getPosixFromMillitime($millitime)
{
    $milli = 1000;
    return floor($millitime / $milli);
}

function getMillisFromMillitime($millitime)
{
    return substr(floor($millitime), -3);
}

function checkPHPGDExtension()
{
    if (extension_loaded('gd') && function_exists('imagecreatetruecolor')) {
        return true;
    }
    return false;
}

function makeDownload($file, $dir, $type)
{
    header('Access-Control-Allow-Origin: *');
    header('Set-Cookie: fileDownload=true; path=/');
    header("Content-Type: $type");
    header("Content-Disposition: attachment; filename=\"$file\"");
    readfile($dir . $file);
}

function isLocalIp($ip)
{
    if ($ip == '::1' || $ip == "127.0.0.1") {
        return true;
    }
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
        return true;
    }
    return false;
}

/**
 * Remove empty subfolders
 * from: http://stackoverflow.com/questions/1833518/remove-empty-subfolders-with-php
 * @param String $path the path
 */
function removeEmptySubfolders($path)
{

    if (substr($path, -1) != DIRECTORY_SEPARATOR) {
        $path .= DIRECTORY_SEPARATOR;
    }
    $d2 = array('.', '..');
    $dirs = array_diff(glob($path . '*', GLOB_ONLYDIR), $d2);
    foreach ($dirs as $d) {
        removeEmptySubfolders($d);
    }

    if (count(array_diff(glob($path . '*'), $d2)) === 0) {
        $checkEmpSubDir = explode(DIRECTORY_SEPARATOR, $path);
        for ($i = count($checkEmpSubDir) - 1; $i > 0; $i--) {
            $path = substr(str_replace($checkEmpSubDir[$i], "", $path), 0, -1);

            if (($files = @scandir($path)) && count($files) <= 2) {
                rmdir($path);
            }
        }
    }
}

function validateString($obj) {
    if (is_string($obj)) {
        return $obj;
    }
    throw new RuntimeException("Not a string.");
}

function validateArray($obj) {
    if (is_array($obj)) {
        return $obj;
    }
    throw new RuntimeException("Not an array.");
}
