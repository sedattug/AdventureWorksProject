<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_DB_oci8_result extends CI_DB_oci8_result {

    function __construct($params) {
        parent::__construct($params);
        log_message('debug', 'Extended DB driver class instantiated!');
    }

    // --------------------------------------------------------------------

    /**
     * Result - associative array
     *
     * Returns the result set as an array
     *
     * @access  protected
     * @return  array
     */
    function _fetch_assoc() {
        $id = ($this->curs_id) ? $this->curs_id : $this->stmt_id;
        return @oci_fetch_assoc($id);
    }

}
