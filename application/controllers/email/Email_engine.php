<?php

defined('BASEPATH') ||     exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Email_engine extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {

        $emails_count = $this->db->where('status', 0)->where('to_mail<>', null)
                ->where('trying_count <=', 9)
                ->count_all_results('production.email_que');

        $loops = $this->global_model->limit_offset_query_parser($emails_count, 10);


        foreach ($loops["loop_info"] as $loop) {

            $q = $this->db->where('status', 0)->where('to_mail<>', null)
                    ->where('trying_count <=', 9)
                    ->limit($loop["limit"], $loop["offset"])
                    ->get('production.email_que');


            if ($q->num_rows() > 0) {

                foreach ($q->result_array() as $row) {
                    $data = [];
                    $data['from_mail'] = (!empty($row["from_mail"])) ? convert_clob_to_string($row["from_mail"]) : EMAIL_SENDER;
                    $data['from_name'] = (!empty($row["from_name"])) ? convert_clob_to_string($row["from_name"]) : EMAIL_SENDER;
                    $data['subject'] = convert_clob_to_string($row["subject"]);
                    $data['to_mail'] = convert_clob_to_string($row["to_mail"]);
                    $data['message'] = convert_clob_to_string($row["message"]);
                    $email_que_id = $row["email_que_id"];
                    $productreviewid = $row["productreviewid"];
                    $trying_count = $row["trying_count"] + 1;
                    if ($this->global_model->send_mail($data)) {
                        $this->update_mail_que($trying_count, 1, $email_que_id);
                        $this->update_review_status($productreviewid);
                    } else {
                        $this->update_mail_que($trying_count, 0, $email_que_id);
                    }
                    usleep(200);
                }
            }
        }
    }

    private function update_mail_que($trying_count, $status, $email_que_id) {
        $update_data = array(
            "trying_count" => $trying_count,
            "status" => $status
        );
        
        $this->db->set_date_field('last_try_date', CURRENT_DATE);
        $this->db->where('email_que_id', (integer) $email_que_id)->update("production.email_que", $update_data);
    }
    
    private function update_review_status($productreviewid) {
        $this->db->set('reviewstatus', PREVIEW_STATUS::APPROVED)->where('productreviewid', (integer) $productreviewid)
                ->update('production.productreview');
    }

}
