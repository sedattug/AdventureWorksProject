<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

defined('BASEPATH') || exit('No direct script access allowed');

class Product_detail_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('global_library');
        $this->load->helper('captcha');
        $this->_init();
    }

    protected function _init() {
        $this->_add_review_path = ADD_REVIEW_WEBSERVICE;
    }

    public function get_product($productid) {
        if (is_null_empty_string($productid) === true) {
            return false;
        }

        $q = $this->db->select('product.productid, product.name as productname, product.productnumber, product.color, product.listprice, '
                        . 'product.productline, product.class, product.style, '
                        . 'productsubcategory.productsubcategoryid as productsubcategoryid, productsubcategory.name as productsubcategoryname, '
                        . 'productcategory.productcategoryid as productcategoryid, productcategory.name as productcategoryname')
                ->where('productline<>', null)->where('class<>', null)->where('style<>', null)
                ->where('product.productid', (integer) $productid)
                ->join('production.productsubcategory', 'production.productsubcategory.productsubcategoryid = production.product.productsubcategoryid')
                ->join('production.productcategory', 'production.productcategory.productcategoryid = production.productsubcategory.productcategoryid')
                ->order_by('product.class')
                ->get('production.product');

        if ($q->num_rows() > 0) {
            return $q->row();
        } else {
            return false;
        }
    }

    public function get_productreviews($productid) {
        if (is_null_empty_string($productid) === true) {
            return false;
        }

        $q = $this->db->where('productid', (integer) $productid)
                ->get('production.productreview');

        if ($q->num_rows() > 0) {
            return $q->result();
        } else {
            return false;
        }
    }

    public function process_leave_review($formData) {

        $curl_url = base_url() . $this->_add_review_path;

        $postdata = json_encode($formData);

        $headerdata = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($postdata));

        $result = $this->global_library->system_curl($curl_url, $postdata, 1, 5, 20, $headerdata);

        if (isset($result['success'])) {
            return $result;
        }
    }

    public function create_captcha() {

        $config = array(
            'img_url' => base_url() . 'template/img/image_for_captcha/',
            'img_path' => 'template/img/image_for_captcha/',
            'word_length' => 5,
            'img_width' => '100',
            'img_height' => 35,
            'expiration' => 7200,
            'font_size' => 12,
            'font_path' => base_url() . 'template/css/fonts/Comfortaa/Comfortaa-Regular.ttf',
            // White background and border, black text and red grid
            'colors' => array(
                'background' => array(255, 255, 255),
                'border' => array(255, 255, 255),
                'text' => array(0, 0, 0),
                'grid' => array(233, 233, 233)
            ),
            'pool' => '0123456789ABCDEFGHIJKLMNPQRSTUVWXYZ'
        );
        $captcha = create_captcha($config);
        $this->session->unset_userdata('valuecaptchaCode');
        $this->session->set_userdata('valuecaptchaCode', $captcha['word']);

        return $captcha['image'];
    }

}
