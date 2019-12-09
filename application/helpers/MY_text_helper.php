<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('highlight_phrase'))
{
    function highlight_phrase($str, $phrase, $tag_open = '<strong>', $tag_close = '</strong>')
    {
        if ($str == '')
        {
            return '';
        }

        if ($phrase != '')
        {
            $str = preg_replace('/\s+/', ' ', $str);
            $phrase = preg_replace('/\s+/', ' ', $phrase);

            return preg_replace('/(' . preg_quote($phrase, '/') . ')/i', $tag_open . "\\1" . $tag_close, $str);
        }

        return $str;
    }
}