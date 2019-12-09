<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}


if (!function_exists('is_null_empty_string')) {

    function is_null_empty_string($string) {
        return ($string === NULL || trim($string) === '');
    }

}


if (!function_exists('dump')) {

    function dump($var, $label = 'Dump', $echo = TRUE) {

        ob_start();
        var_dump($var);
        $output = ob_get_clean();

        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        $output = '<pre style="background: #FFFEEF; color: #000; border: 1px dotted #000; padding: 10px; margin: 10px 0; text-align: left;">' . $label . ' => ' . $output . '</pre>';

        if ($echo == TRUE) {
            echo $output;
        } else {
            return $output;
        }
    }

}


if (!function_exists('dump_exit')) {

    function dump_exit($var, $label = 'Dump', $echo = TRUE) {
        dump($var, $label, $echo);
        exit;
    }

}


if (!function_exists('array_column')) {

    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null) {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();

        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }

        if (!is_array($params[0])) {
            trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING);
            return null;
        }

        if (!is_int($params[1]) && !is_float($params[1]) && !is_string($params[1]) && $params[1] !== null && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (isset($params[2]) && !is_int($params[2]) && !is_float($params[2]) && !is_string($params[2]) && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;

        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }

        $resultArray = array();

        foreach ($paramsInput as $row) {

            $key = $value = null;
            $keySet = $valueSet = false;

            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }

            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }

            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }

        return $resultArray;
    }

}

if (!function_exists('librid_server_variables')) {

    function librid_server_variables($indis = '') {

        if (!empty($indis)) {
            return filter_input(INPUT_SERVER, $indis);
        }

        $_LIBRID_SERVER = array();
        foreach (array_keys($_SERVER) as $b) {
            $_LIBRID_SERVER[$b] = filter_input(INPUT_SERVER, $b);
        }
        return $_LIBRID_SERVER;
    }

}

if (!function_exists('convert_clob_to_string')) {

    function convert_clob_to_string($data) {

        $ci = & get_instance();

        if (is_object($data) && (string) $ci->db->dbdriver === 'oci8') {
            $size = @$data->size();
            return @$data->read($size);
        }

        return $data;
    }

}

/*
 * PRODUCT CLASS
 * H = High, M = Medium, L = Low
 */
if (!function_exists('product_class')) {

    function product_class($char) {
        switch (trim($char)) {
            case 'H':
                echo 'High';
                break;

            case 'M':
                echo 'Medium';
                break;

            case 'L':
                echo 'Low';
                break;
            default:
                echo 'Unknown Class';
                break;
        }
    }

}

/*
 * PRODUCT LINE
 * R = Road, M = Mountain, T = Touring, S = Standard
 */
if (!function_exists('product_line')) {

    function product_line($char) {
        switch (trim($char)) {
            case 'R':
                echo 'Road';
                break;

            case 'M':
                echo 'Mountain';
                break;

            case 'T':
                echo 'Touring';
                break;

            case 'S':
                echo 'Standard';
                break;
            default:
                echo 'Unknown Line';
                break;
        }
    }

}

/*
 * PRODUCT STYLE
 * W = Womens, M = Mens, U = Universal
 */
if (!function_exists('product_style')) {

    function product_style($char) {
        switch (trim($char)) {
            case 'W':
                echo 'Womens';
                break;

            case 'M':
                echo 'Mens';
                break;

            case 'U':
                echo 'Universal';
                break;

            default:
                echo 'Unknown Style';
                break;
        }
    }

}

/*
 * PRODUCTREVIEW COMMENT SUMMARY
 */
if (!function_exists('comment_summary')) {

    function comment_summary($string) {

        return (strlen($string) > 150) ? substr($string, 0, 150) . ' ...' : $string;
    }

}

/*
 * IS VALID EMAIL
 */
if (!function_exists('is_valid_email')) {

    function is_valid_email($email) {

        return (preg_match("/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/", htmlspecialchars(stripslashes(trim($email))))) ? true : false;
    }

}

/*
 * RETURN PREVIEW STATUS TEXT
 */
if (!function_exists('preview_status_text')) {

    function preview_status_text($val) {
        switch (trim($val)) {
            case 0:
                echo '<div style="font-size:small;" class="alert alert-danger"><strong>Rejected!</strong> Your review contains inappropriate content.</div>';
                break;

            case 1:
                echo '<div style="font-size:small;" class="alert alert-success"><strong>Approved!</strong> Your review is approved.</div>';
                break;

            case 2:
                echo '<div style="font-size:small;" class="alert alert-info"><strong>Pending!</strong> Your review is pending approval. You will be notified by email when your review is approved.</div>';
                break;

            default:
                echo 'Unknown Status';
                break;
        }
    }

}

/*
 * RETURN COMMENT BAD WORD STATUS
 */
if (!function_exists('is_contains_bad_word')) {

    function is_contains_bad_word($comment) {
        $badWords = BAD_WORDS;
        $string = $comment;

        $matches = array();
        $matchFound = preg_match_all(
                "/\b(" . implode($badWords, "|") . ")\b/i", $string, $matches
        );

        if ($matchFound) {
            return true;
        }
        return false;
    }

}