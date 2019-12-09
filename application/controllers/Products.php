<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('BASEPATH') || exit('No direct script access allowed');

class Products extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('products/products_model');
    }

    public function index() {
        $css = $js = [];
        $title = constant("SITE_TITLE_" . strtoupper(($this->session->has_userdata('current_lang_id') && !empty($this->session->userdata('current_lang_id')) ? $this->session->userdata('current_lang_id') : 'tr')));
        $description = null;
        $data = $this->global_library->page_data($css, $js, $title, $description);
        $data['main_content_page'] = 'template1/products/index_view';
        $data['data'] = array();
        //$data['data']['captchaImg'] = $this->products_model->create_captcha();
        $data['data']['products'] = $this->products_model->get_products();
        $data['data']['productcategories'] = $this->products_model->get_categories();
        //$data['data']['productsubcategories'] = $this->products_model->get_subcategories();
        $this->parser->parse('template1/includes/template1', $data);
    }
    
}
