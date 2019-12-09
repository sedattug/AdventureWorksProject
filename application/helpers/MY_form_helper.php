<?php

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Form Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/helpers/form_helper.html
 */
// ------------------------------------------------------------------------

if (!function_exists('form_open')) {

    /**
     * Form Declaration
     *
     * Creates the opening portion of the form.
     *
     * @param	string	the URI segments of the form destination
     * @param	array	a key/value pair of attributes
     * @param	array	a key/value pair hidden data
     * @return	string
     */
    function form_open($action = '', $attributes = array(), $hidden = array()) {
        $CI = & get_instance();

        // If no action is provided then set to the current url
        if (!$action) {
            $action = $CI->config->site_url($CI->uri->uri_string());
        }
        // If an action is not a full URL then turn it into one
        elseif (strpos($action, '://') === FALSE) {
            $action = $CI->config->site_url($action);
        }

        $attributes = _attributes_to_string($attributes);

        if (stripos($attributes, 'method=') === FALSE) {
            $attributes .= ' method="post"';
        }

        if (stripos($attributes, 'accept-charset=') === FALSE) {
            $attributes .= ' accept-charset="' . strtolower(config_item('charset')) . '"';
        }

        $form = '<form action="' . $action . '"' . $attributes . ">\n";

        if (is_array($hidden)) {
            foreach ($hidden as $name => $value) {
                $form .= '<input type="hidden" name="' . $name . '" value="' . html_escape($value) . '" />' . "\n";
            }
        }

        // Add CSRF field if enabled, but leave it out for GET requests and requests to external websites
        if ($CI->config->item('csrf_protection') === TRUE && strpos($action, $CI->config->base_url()) !== FALSE && !stripos($form, 'method="get"')) {
            // Prepend/append random-length "white noise" around the CSRF
            // token input, as a form of protection against BREACH attacks
            if (FALSE !== ($noise = $CI->security->get_random_bytes(1))) {
                list(, $noise) = unpack('c', $noise);
            } else {
                $noise = mt_rand(-128, 127);
            }

            // Prepend if $noise has a negative value, append if positive, do nothing for zero
            $prepend = $append = '';
            if ($noise < 0) {
                $prepend = str_repeat(" ", abs($noise));
            } elseif ($noise > 0) {
                $append = str_repeat(" ", $noise);
            }

            $form .= sprintf(
                    '%s<input type="hidden" name="%s" value="%s"/>%s%s', $prepend, $CI->security->get_csrf_token_name(), $CI->security->get_csrf_hash(), $append, "\n"
            );
        }

        return $form;
    }

}
