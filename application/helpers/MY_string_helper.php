<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}


// ------------------------------------------------------------------------

/**
 * Strip Slashes
 *
 * Removes slashes contained in a string or in an array
 *
 * @access	public
 * @param	mixed	string or array
 * @return	mixed	string or array
 */
if (!function_exists('strip_slashes')) {

    function strip_slashes($str) {

        if (is_array($str)) {
            foreach ($str as $key => $val) {
                $str[$key] = strip_slashes($val);
            }
        } else if (is_object($str)) {
            $size = $str->size();
            return $str->read($size);
        } else {
            $str = stripslashes($str);
        }

        return $str;
    }

}
