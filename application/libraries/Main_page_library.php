<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Main_page_library {

    private $ci;

    public function __construct() {
        $this->ci = &get_instance();
    }

    public function main_page_default_css() {
        $data['page_default_css_array'] = array();

        $css_file_names = array(
            'google_fonts',
            'shop-homepage/bootstrap/bootstrap.min',
            'shop-homepage/shop-homepage',
            'loadmask/jquery.loadmask',
            'notification/toastr.min',
            'font-awesome.min',
            'custom'
        );
        foreach ($css_file_names as $css_file_name) {
            $data['page_default_css_array'][]['page_default_css'] = '<link rel="stylesheet" type="text/css" media="screen" href="' . base_url('template/css/main/' . $css_file_name . '.css') . '">';
        }

        return $data;
    }

    public function main_page_default_js() {
        $data['page_default_js_array'] = array();
        $js_file_names = array(
            'custom'
        );
        foreach ($js_file_names as $js_file_name) {
            $data['page_default_js_array'][]['page_default_js'] = '<script src="' . base_url('template/js/main/' . $js_file_name . '.js') . '"></script>';
        }

        return $data;
    }

    public function main_page_default_header_js() {
        $data['page_default_header_js_array'] = array();

        $js_file_names = array(
            'shop-homepage/jquery/jquery.min',
            'shop-homepage/bootstrap/bootstrap.bundle.min',
            'loadmask/jquery.loadmask',
            'bootstrap/bootstrap.min',
            'notification/toastr.min',
            'input-mask/inputmask',
            'input-mask/inputmask.extensions',
            'input-mask/jquery.inputmask',
            'input-mask/jquery-ensure-max-length.min',
            'common'
        );
        foreach ($js_file_names as $js_file_name) {
            $data['page_default_header_js_array'][]['page_default_header_js'] = '<script src="' . base_url('template/js/main/' . $js_file_name . '.js') . '"></script>';
        }

        return $data;
    }

}
