<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('BASEPATH') || exit('No direct script access allowed');

class Product_detail extends CI_Controller {

    public function __construct() {
        parent::__construct();
        error_reporting(0);
        $this->load->model('products/product_detail_model');
        $this->load->model('products/products_model');
        $this->load->library('xajax/xajax');
        $this->xajax->configure('javascript URI', base_url());
    }

    public function detail($productid = null) {

        if (is_null_empty_string($productid) !== false) {
            redirect(base_url('/products'));
            exit;
        }

        $this->xajax->register(XAJAX_FUNCTION, array('xajaxLeaveReview', &$this, 'xajaxLeaveReview'));
        $this->xajax->register(XAJAX_FUNCTION, array('xajaxRefreshCaptcha', &$this, 'xajaxRefreshCaptcha'));
        $this->xajax->configure('debug', false);
        $this->xajax->processRequest();

        $css = $js = [];
        $title = constant("SITE_TITLE_" . strtoupper(($this->session->has_userdata('current_lang_id') && !empty($this->session->userdata('current_lang_id')) ? $this->session->userdata('current_lang_id') : 'tr')));
        $description = null;
        $data = $this->global_library->page_data($css, $js, $title, $description);
        $data['main_content_page'] = 'template1/products/detail_view';
        $data['data'] = array();
        $data['data']['captchaImg'] = $this->product_detail_model->create_captcha();
        $data['data']['productinfo'] = $this->product_detail_model->get_product($productid);
        $data['data']['productreviews'] = $this->product_detail_model->get_productreviews($productid);

        if (is_null_empty_string($data['data']['productinfo']) !== false) {
            redirect(base_url('/products'));
            exit;
        }

        $data['data']['productcategories'] = $this->products_model->get_categories();
        $this->parser->parse('template1/includes/template1', $data);
    }

    public function xajaxLeaveReview($formData) {
        $objResponse = new xajaxResponse();

        $objResponse->script("$('.form-control').css({'border':'1px solid #ced4da', 'background-color':'#FFFFFF'})");

        $required_columns = array('email', 'name', 'review', 'captcha');

        $errors = [];

        foreach ($required_columns as $required_column) {
            if (is_null_empty_string($formData[$required_column]) === true) {
                $errors[] = ["column" => $required_column, "message" => ucfirst($required_column) . " field is required."];
            }
        }

        if (count($errors) > 0) {
            //$objResponse->script("console.log('" . json_encode($errors) . "')");

            foreach ($errors as $error) {
                $objResponse->script("toastr.error('" . $this->global_library->tag_description($error['message']) . "')");
                $objResponse->script("$('#" . $error['column'] . "').css({'border':'1px solid red', 'background-color':'#ff00001f'}).focus()");
                $objResponse->script("unmask_div();");
                return $objResponse;
            }
        }

        $captcha_info = $formData["captcha"];

        if ((string) $captcha_info !== (string) $this->session->userdata('valuecaptchaCode')):
            if ((string) trim($captcha_info) !== (string) $this->session->userdata('valuecaptchaCode')) {
                $objResponse->script("toastr.error('The confirmation code did not match, refresh the code, or try typing again.')");
                $objResponse->script("$('#captcha').css({'border':'1px solid red', 'background-color':'#ff00001f'}).focus()");
                $objResponse->script("unmask_div();");
                return $objResponse;
            }
        endif;

        $process_leave_review = $this->product_detail_model->process_leave_review($formData);
        //log_message("error", "API Response : " . print_r($process_leave_review, true));

        if ($process_leave_review["success"] === true) {
            $objResponse->script("toastr.success('Everything is okay.')");
            $objResponse->script("unmask_div();");
            $objResponse->script("window.location.href = '" . current_url() . "';");
            return $objResponse;
        } else {
            $objResponse->script("toastr.error('An error occurred. (Code : 3)')");
            $objResponse->script("unmask_div();");
            return $objResponse;
        }
    }

    public function xajaxRefreshCaptcha() {

        $objResponse = new xajaxResponse();
        $refreshed_captcha = $this->product_detail_model->create_captcha();
        $objResponse->script("$('#image_captcha').html('" . $refreshed_captcha . "')");
        return $objResponse;
    }

}
