<?php

defined('BASEPATH') || exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Global_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function limit_offset_query_parser($total_rows = 0, $limit = GRAB_DATA_MAX_COUNT) {

        $data["total_rows"] = $total_rows;
        $data["limit"] = $limit;
        $data["remaining_rows"] = $data["total_rows"] % $data["limit"];
        $data["loop_count"] = (($data["total_rows"] - $data["remaining_rows"]) / $data["limit"]) + (($data["remaining_rows"] > 0) ? 1 : 0);


        $data["loop_info"] = [];
        for ($x = 1; $x <= $data["loop_count"]; $x++) {
            $y = $x - 1;
            $offset = $y * $data["limit"];
            $limit = ($x === $data["loop_count"] && $data["remaining_rows"] > 0) ? $data["remaining_rows"] : $data["limit"];

            $temp_data = [];
            $temp_data["offset"] = $offset;
            $temp_data["limit"] = $limit;
            $data["loop_info"][] = $temp_data;
        }

        return $data;
    }

    public function send_mail($data, $objResponse = null) {

        $this->load->library('email');
        $this->email->clear();
        $config['protocol'] = 'smtp';

        $config['smtp_host'] = EMAIL_SMTP_HOST;
        $config['smtp_port'] = EMAIL_SMTP_PORT;
        $config['smtp_user'] = EMAIL_SMTP_USER;
        $config['smtp_pass'] = EMAIL_SMTP_PASSWORD;
        $config['useragent'] = 'CodeIgniter';
        $config['charset'] = 'utf-8';
        $config['mailtype'] = isset($data["mailtype"]) ? $data["mailtype"] : 'html';

        if ((integer) EMAIL_SMTP_CRYPTO === 1) {
            $config['smtp_crypto'] = 'ssl';
        }

        if ((integer) EMAIL_SMTP_CRYPTO === 2) {
            $config['smtp_crypto'] = 'tls';
        }


        $this->email->initialize($config);
        $this->email->set_crlf("\r\n");
        $this->email->set_newline("\r\n");
        $this->email->from(EMAIL_SENDER, $data['from_name']);
        $this->email->to($data['to_mail']);

        if (isset($data['cc_mail'])) {
            $this->email->cc($data['cc_mail']);
        }
        if (isset($data['bcc_mail'])) {
            $this->email->cc($data['bcc_mail']);
        }

        $this->email->subject($data['subject']);
        $this->email->message($data['message']);


        if ($this->email->send()) {
            return true;
        } else {
            log_message('error', nl2br(print_r($this->email->print_debugger(), true)));
            return false;
        }
    }

}
