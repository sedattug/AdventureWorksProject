<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Global_library {

    private $ci;

    public function __construct() {
        $this->ci = &get_instance();
        $this->ci->load->library('main_page_library');
        $this->_init();
    }

    private function _init() {
        $this->ci->load->model('global_model');
    }


    public function page_data($css = null, $js = null, $title = null, $description = null) {
        /*
         * HEADER DATA
         */

        $data['headerdata'] = $this->create_page_specific_css_links($css);
        $data['headerdata']['site_title'] = $title;
        $data['headerdata']['favicon_url'] = null;
        $data['headerdata']['site_description'] = $description;
        $data['headerdata'] += $this->ci->main_page_library->main_page_default_css();
        $data['headerdata'] += $this->ci->main_page_library->main_page_default_header_js();

        /*
         * CONTENT SHARED DATA
         */

        $data['data'] = $this->lang_tags();
        /*
         * FOOTER DATA
         */

        $data['footerdata'] = $this->ci->main_page_library->main_page_default_js();
        $data['footerdata'] += $this->create_page_specific_js_links($js);
        return $data;
    }

    private function create_page_specific_css_links($css = null) {
        $data = array('css' => array());
        if (!is_array($css) && count($css) <= 0) {
            return $data;
        }
        foreach ($css as $css_name) {
            $data[]['css_link'] = '<link rel="stylesheet" href="' . base_url('template/css/page_specific_css/' . $css_name . '.css') . '">';
        }
        return $data;
    }

    private function create_page_specific_js_links($js = null) {
        $data = array('js' => array());
        if (!is_array($js) && count($js) <= 0) {
            return $data;
        }

        foreach ($js as $js_name) {
            $data[]['js_link'] = '<script src="' . base_url('template/js/page_specific_js/' . $js_name . '.js') . '">';
        }

        return $data;
    }

    public function tag_description($text, $lang_id = false) {

        if (empty($text)) {
            return $text;
        }

        if ($lang_id !== false) {
            $this->ci->lang->load($lang_id, $lang_id);
        }

        $desc = lang(strtoupper(convert_clob_to_string($text)));

        if (empty($desc)) {
            $tag_text = rtrim(str_replace("__", "_", (string) mb_substr($this->clear_invalid_chars($text), 0, 100)), "_");
            $desc = lang($tag_text);
        }

        return (empty($desc)) ? $text : $desc;
    }

    public function clear_invalid_chars($text, $parser = '_') {
        $turkish = array("Ğ", "Ü", "Ş", "İ", "Ö", "Ç", "ğ", "ü", "ş", "ı", "ö", "ç", "/", "(", ")", "[", "]", ",", "-", ".");
        $english = array("G", "U", "S", "I", "O", "C", "G", "U", "S", "I", "O", "C", " ", " ", " ", " ", " ", " ", " ", "");
        $new_text = trim(preg_replace("/[^A-Za-z0-9_ ]/", '', str_replace($turkish, $english, trim($text))));
        return rtrim(str_replace("__", "_", strtoupper(preg_replace('!\s+!', $parser, $new_text))), "_"); // str_replace(" ", "_", $new_text);
    }

    public function system_curl($url, $postdata = false, $post_status = 1, $conn_timeout = 5, $timeout = 20, $headerdata = false) {
        $curl = curl_init();

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
            CURLOPT_CONNECTTIMEOUT => $conn_timeout,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Default cURL Request'
        );
        
        if ($postdata !== false) {
            $options[CURLOPT_POST] = $post_status;
            $options[CURLOPT_POSTFIELDS] = $postdata;
        }
        
        if ($headerdata !== false) {
            $options[CURLOPT_HTTPHEADER] = $headerdata;
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

    public function lang_tags() {
        return array(
            'PLEASE_WAIT' => $this->ci->global_library->tag_description('PLEASE_WAIT'),
        );
    }
}
