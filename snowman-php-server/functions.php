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

/**
 * from: http://mobiforge.com/design-development/content-delivery-mobile-devices
 * (2014-06-27 00:30) and modified
 * @param type $file the file to download
 */
function rangeDownload($file) {

  $fp = @fopen($file, 'rb');

  $size   = filesize($file); // File size
  $length = $size;           // Content length
  $start  = 0;               // Start byte
  $end    = $size - 1;       // End byte
  // Now that we've gotten so far without errors we send the accept range header
  /* At the moment we only support single ranges.
   * Multiple ranges requires some more work to ensure it works correctly
   * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
   *
   * Multirange support annouces itself with:
   * header('Accept-Ranges: bytes');
   *
   * Multirange content must be sent with multipart/byteranges mediatype,
   * (mediatype = mimetype)
   * as well as a boundry header to indicate the various chunks of data.
   */
  header("Accept-Ranges: 0-$length");
  // header('Accept-Ranges: bytes');
  // multipart/byteranges
  // http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
  if (isset($_SERVER['HTTP_RANGE'])) {

    $c_start = $start;
    $c_end   = $end;
    // Extract the range string
    list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
    // Make sure the client hasn't sent us a multibyte range
    if (strpos($range, ',') !== false) {

      // (?) Shoud this be issued here, or should the first
      // range be used? Or should the header be ignored and
      // we output the whole content?
      header('HTTP/1.1 416 Requested Range Not Satisfiable');
      header("Content-Range: bytes $start-$end/$size");
      // (?) Echo some info to the client?
      exit;
    }
    // If the range starts with an '-' we start from the beginning
    // If not, we forward the file pointer
    // And make sure to get the end byte if spesified
    if ($range{0} == '-') {

      // The n-number of the last bytes is requested
      $c_start = $size - substr($range, 1);
    }
    else {

      $range  = explode('-', $range);
      $c_start = $range[0];
      $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
    }
    /* Check the range and make sure it's treated according to the specs.
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     */
    // End bytes can not be larger than $end.
    $c_end = ($c_end > $end) ? $end : $c_end;
    // Validate the requested range and return an error if it's not correct.
    if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {

      header('HTTP/1.1 416 Requested Range Not Satisfiable');
      header("Content-Range: bytes $start-$end/$size");
      // (?) Echo some info to the client?
      exit;
    }
    $start  = $c_start;
    $end    = $c_end;
    $length = $end - $start + 1; // Calculate new content length
    fseek($fp, $start);
    header('HTTP/1.1 206 Partial Content');
  }
  // Notify the client the byte range we'll be outputting
  header("Content-Range: bytes $start-$end/$size");
  header("Content-Length: $length");

  // Start buffered download
  $buffer = 1024 * 8;
  while(!feof($fp) && ($p = ftell($fp)) <= $end) {

    if ($p + $buffer > $end) {

      // In case we're only outputtin a chunk, make sure we don't
      // read past the length
      $buffer = $end - $p + 1;
    }
    set_time_limit(0); // Reset time limit for big files
    echo fread($fp, $buffer);
    flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
  }

  fclose($fp);

}

