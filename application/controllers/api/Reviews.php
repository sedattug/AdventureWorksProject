<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require APPPATH . 'libraries/webservice/REST_Controller.php';

class Reviews extends REST_Controller {

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Puts the product review into the database
     *
     * @return Response
     */
    public function add_review_post() {

        $post = $this->post();

        $post_arr = ['name', 'email', 'productid', 'review'];

        $errors = [];

        foreach ($post_arr as $key => $value):
            if (!isset($post[$value]) || is_null_empty_string($post[$value]) !== false) {
                $errors[] = $key;
            }
        endforeach;

        if (!is_valid_email($post['email'])) {
            $errors[] = 'INVALID_EMAIL';
        }

        if (count($errors) > 0):
            log_message("error", print_r($errors, true));
            $data['success'] = false;
            $data['reviewID'] = null;
            $this->response($data, 200);
            return;
        endif;

        $review_data = [
            'productid' => (integer) $post['productid'],
            'emailaddress' => (string) $post['email'],
            'reviewername' => (string) $post['name'],
            'comments' => (string) htmlspecialchars($post['review']),
            'rating' => 3 // TODO: star icons will be added for voting later bla bla bla.
        ];

        $review_data['reviewstatus'] = PREVIEW_STATUS::PENDING;

        if (is_contains_bad_word($review_data['comments']) === true) {
            $review_data['reviewstatus'] = PREVIEW_STATUS::REJECTED;
        }

        $insertstatus = $this->db->set_date_field('reviewdate', CURRENT_DATE)
                ->set_date_field('modifieddate', CURRENT_DATE)
                ->insert('production.productreview', $review_data);

        $reviewID = $this->db->insert_id();

        if ($insertstatus === true) {

            if ($review_data['reviewstatus'] !== PREVIEW_STATUS::REJECTED) {
                $customer_name = constant("SITE_TITLE_TR");
                $mail_subject = 'About Your Review.';
                $message = '-';
                
                $product_detail = $this->db->select('product.productid,product.productnumber,productreview.reviewdate')
                        ->where('productreviewid',(integer) $reviewID)
                        ->join('production.product', 'production.product.productid = production.productreview.productid')
                        ->get('production.productreview');
                
                if($product_detail->num_rows() > 0) {
                    $message  = 'Your review has been approved and published.'; 
                    $message .= '<ul><li>Product Number : ' . $product_detail->row()->productnumber . '</li>'
                            . '<li>Posted on ' . $product_detail->row()->reviewdate . '</li>';
                    $message .= '<p><a target="_blank" href="'. base_url('product_detail/detail/') . $product_detail->row()->productid .'">Click</a> for details.</p>';
                }
                
                $email_que_data = array(
                    'productreviewid' => $reviewID,
                    'message' => $message,
                    'to_mail' => $review_data['emailaddress'],
                    'from_mail' => EMAIL_SENDER,
                    'from_name' => $customer_name,
                    'subject' => $mail_subject,
                    'status' => 0
                );

                $this->db->insert("production.email_que", $email_que_data);
            }

            $data['success'] = true;
            $data['reviewID'] = ($reviewID > 0) ? $reviewID : null;
            $this->response($data, 200);
            return;
        }

        //log_message("error", "An error occurred during the insert. Last query : " . $this->db->last_query());
        $data['success'] = false;
        $data['reviewID'] = null;
        $this->response($data, 200);
        return;
    }

}
