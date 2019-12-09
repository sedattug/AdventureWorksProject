<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions {

    function show_error($heading, $message, $template = 'error_general', $status_code = 500) {
        if ((!isset($template)) || ($template == 'error_general')) {
            if (file_exists(APPPATH . 'errors/error_' . $status_code . '.php')) {
                $template = 'error_' . $status_code;
            }
        }

        if (!isset($status_code)) {
            $status_code = 500;
        }

        set_status_header($status_code);

        $message = '<p>' . implode('</p><p>', (!is_array($message)) ? array($message) : $message) . '</p>';
        $heading = '<p>' . implode('</p><p>', (!is_array($heading)) ? array($heading) : $heading) . '</p>';

        if (ob_get_level() > $this->ob_level + 1) {
            ob_end_flush();
        }
        ob_start();

        include(APPPATH . 'errors/' . $template . '.php');
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

}
