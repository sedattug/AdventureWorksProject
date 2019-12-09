<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_DB_oci8_driver extends CI_DB_oci8_driver {

    function __construct($params) {
        parent::__construct($params);
        $this->query("alter session set nls_date_format='yyyy/mm/dd hh24:mi:ss'");
    }

    function db_to_localized_date($date, $time = FALSE) {

        if ($time === false) {
            $date = date("Y/m/d", strtotime($date));
            $date_hour_arr = array();
        } else {
            $date = date("Y/m/d H:i:s", strtotime($date));
        }
        $date_hour_arr = explode(' ', $date);



        if (count($date_hour_arr) === 1) {
            $date_arr = explode(DATE_SEPARATOR, $date_hour_arr[0]);

            $year = $date_arr[0];
            $month = $date_arr[1];
            $day = $date_arr[2];


// is_numeric kontrolü de olabilir
            if (strlen($year) !== 4 || strlen($month) !== 2 || strlen($day) !== 2) {
                throw new Exception('WRONG_DATE_DETAIL_FORMAT');
            } else {
                return $day . DATE_SEPARATOR . $month . DATE_SEPARATOR . $year;
            }
        } else if (count($date_hour_arr) === 2) {

            $date_arr = explode(DATE_SEPARATOR, $date_hour_arr[0]);
            $time_arr = explode(':', $date_hour_arr[1]);


            if (count($date_arr) !== 3 || count($time_arr) !== 3) {
                throw new Exception('WRONG_DATE_FORMAT');
            } else {

                $year = $date_arr[0];
                $month = $date_arr[1];
                $day = $date_arr[2];
                $hour = $time_arr[0];
                $minute = $time_arr[1];
                $second = substr($time_arr[2], 0, 2); // BURAYI AŞAĞIDAKİ KONTROLE DAHİL ETMEDİM
// is_numeric kontrolü de olabilir
                if (strlen($year) !== 4 || strlen($month) !== 2 || strlen($day) !== 2 || strlen($hour) !== 2 || strlen($minute) !== 2) {
                    throw new Exception('WRONG_DATE_DETAIL_FORMAT');
                } else {

                    if ($time === FALSE) {
                        return $day . DATE_SEPARATOR . $month . DATE_SEPARATOR . $year;
                    } else {
                        return $day . DATE_SEPARATOR . $month . DATE_SEPARATOR . $year . ' ' . $hour . ':' . $minute . ':' . $second;
                    }
                }
            }
        } else {
            throw new Exception('WRONG_DB_DATE_FORMAT');
        }
    }

    function localized_to_db_date($date_raw) {

        $date = str_replace("-", "/", $date_raw);
        $date_arr = explode(DATE_SEPARATOR, $date);

        if (count($date_arr) !== 3) {
            throw new Exception('WRONG_LOCALIZED_DATE_FORMAT');
        } else {

            $year = $date_arr[2];
            $month = $date_arr[1];
            $day = $date_arr[0];

            if (strlen($day) !== 2 || strlen($month) !== 2 || strlen($year) !== 4) {
                throw new Exception('WRONG_LOCALIZED_DATE_DETAIL_FORMAT');
            } else {
                return date(DATE_FORMAT, mktime(0, 0, 0, $month, $day, $year));
            }
        }
    }

    protected function _escape_identifiers($item) {
        if ($this->_escape_char == '') {
            return $item;
        }

        foreach ($this->_reserved_identifiers as $id) {
            if (strpos($item, '.' . $id) !== FALSE) {
                $str = $this->_escape_char . str_replace('.', $this->_escape_char . '.', $item);

//                // remove duplicates if the user already included the escape
//                $str = preg_replace('/[' . $this->_escape_char . ']+/', $this->_escape_char, $str);
//                $str = rtrim($str, '"');
//                $str = ltrim($str, '"');
//                $str = str_replace('"."', '.', $str);

                return $str;
            }
        }

        if (strpos($item, '.') !== FALSE) {
            $str = $this->_escape_char . str_replace('.', $this->_escape_char . '.' . $this->_escape_char, $item) . $this->_escape_char;
        } else {
            $str = $this->_escape_char . $item . $this->_escape_char;
        }

// remove duplicates if the user already included the escape
        $str = preg_replace('/[' . $this->_escape_char . ']+/', $this->_escape_char, $str);
//        $str = rtrim($str, '"');
//        $str = ltrim($str, '"');
//$str = str_replace('.', '.', $str);

        return $str;
    }

    function where_for_date_field($field, $date_value, $seperator = '') {

        $fields_array = explode(".", $field);

        $field_name_array = array();
        foreach ($fields_array as $field_name) {
            $field_name_array[] = $this->_escape_char . trim($field_name) . $this->_escape_char;
        }
        $date_format = 'yyyy/mm/dd hh24:mi:ss';
        if ((integer) strlen($date_value) === 10) {
            $date_format = 'yyyy/mm/dd';
        }
        $this->where(implode(".", $field_name_array) . " " . $seperator . " TO_DATE('" . $date_value . "', '" . $date_format . "')", null, false);
        return $this;
    }

    function set_date_field($date_field, $date_value) {
        if (is_null_empty_string($date_value) !== true && strlen($date_value) > 10) {
            return $this->set($this->_escape_char . $date_field . $this->_escape_char, "TO_DATE('" . $date_value . "', 'yyyy/mm/dd hh24:mi:ss')", false);
        } else if (is_null_empty_string($date_value) !== true && strlen($date_value) === 10) {
            return $this->set($this->_escape_char . $date_field . $this->_escape_char, "TO_DATE('" . $date_value . "', 'yyyy/mm/dd')", false);
        }
        return $this;
    }

    function last_insert_id($tablename, $key_id_name) {
        $max_name_char = 26;
        $tablename_char = strlen(trim($tablename) . "_" . trim($key_id_name));
        $seq_name_prefix = trim($tablename) . "_" . trim($key_id_name);

        if ((integer) $tablename_char < (integer) $max_name_char || (integer) $tablename_char === (integer) $max_name_char) {
//            $key_id_suffix = substr(trim($key_id_name), 0, ((integer) $max_name_char - (integer) $tablename_char));
            $sequence_name = $seq_name_prefix . '_SEQ';
        } else if ((integer) $tablename_char > (integer) $max_name_char) {
            $sequence_name = substr(trim($seq_name_prefix), 0, (integer) $max_name_char) . '_SEQ';
        }

        return $this->query('SELECT ' . $this->_escape_char . $sequence_name . $this->_escape_char . '.currval FROM DUAL')->row()->CURRVAL;
    }

    function select_blob($field_name) {

        $names = explode(".", $field_name);

        $escape_field_name_array = array();
        foreach ($names as $name) {
            $escape_field_name_array[] = $this->_escape_char . $name . $this->_escape_char;
        }

        $escape_field_name = implode(".", $escape_field_name_array);

        $this->select("TO_CHAR(" . $escape_field_name . ") as " . ((count($escape_field_name_array) > 1) ? $escape_field_name_array[1] : $escape_field_name_array[0]), false);

        return $this;
    }

    public function select_count($select = '', $alias = '', $distinct_status = false) {
        if (!is_string($select) OR $select === '') {
            $this->display_error('db_invalid_query');
        }

        $distinct_data = ($distinct_status === true) ? 'DISTINCT ' : '';


        $select_field = $this->protect_identifiers(trim($select));

        $select_array = explode(".", $select);

        if (count($select_array) === 2) {
            $select_field = $this->protect_identifiers(trim($select_array[0])) . "." . $this->protect_identifiers(trim($select_array[1]));
        }

        if ($alias === '' && count($select_array) === 2) {
            $alias = $select_array[1];
        } else if ($alias === '' && count($select_array) === 1) {
            $alias = $select_array[0];
        }

        $sql = 'COUNT(' . $distinct_data . $select_field . ') AS ' . $this->escape_identifiers(trim($alias));

        $this->qb_select[] = $sql;
        $this->qb_no_escape[] = NULL;

        if ($this->qb_caching === TRUE) {
            $this->qb_cache_select[] = $sql;
            $this->qb_cache_exists[] = 'select';
        }

        return $this;
    }

    public function where_clob($fieldname, $fieldvalue) {
        $this->where('dbms_lob.compare(' . $this->_escape_char . trim($fieldname) . $this->_escape_char . ',\'' . trim($fieldvalue) . '\' ) = 0');
        return $this;
    }

    public function or_where_clob($fieldname, $fieldvalue) {
        $this->or_where('dbms_lob.compare(' . $this->_escape_char . trim($fieldname) . $this->_escape_char . ',\'' . trim($fieldvalue) . '\' ) = 0');
        return $this;
    }

    function check_field_exist($tablename = null, $fieldname = null) {
        if (empty($tablename) || empty($fieldname)) {
            return null;
        }

        return $this->where('TABLE_NAME', (string) $tablename)->where('COLUMN_NAME', $fieldname)->count_all_results('SYS.USER_TAB_COLUMNS');
    }

    function get_dbtable_relations($tablename = null, $status = false, $exclude_tables = array()) {
        if (empty($tablename)) {
            return null;
        }

        if (is_array($exclude_tables) && count($exclude_tables) > 0) {
            $this->group_start();
            foreach ($exclude_tables as $exclude_table) {
                $this->or_where('a.TABLE_NAME <>', $exclude_table);
            }
            $this->group_end();
        }

        $this->group_start();
        if ($status === false) {
            $this->or_where('a.TABLE_NAME', (string) $tablename);
        }
        $this->or_where('c_pk.TABLE_NAME', (string) $tablename);
        $this->group_end();

// $q = $this->select('fk.name fk_name, tp.name parent_table, cp.name parent_name, cp.column_id, tr.name refrence_table, cr.name refrence_name, cr.column_id')        

        $q = $this->select('a.CONSTRAINT_NAME as fk_name, a.TABLE_NAME as parent_table, a.COLUMN_NAME as parent_name, c_pk.TABLE_NAME as refrence_table')
                        ->from('ALL_CONS_COLUMNS a')
                        ->join('ALL_CONSTRAINTS c', 'a.OWNER = c.OWNER AND a.CONSTRAINT_NAME = c.CONSTRAINT_NAME')
                        ->join('ALL_CONSTRAINTS c_pk', 'c.R_OWNER = c_pk.OWNER AND c.R_CONSTRAINT_NAME = c_pk.CONSTRAINT_NAME')
                        ->order_by('a.CONSTRAINT_NAME')->get();


        return ($q->num_rows() > 0) ? $q->result_array() : null;
    }

    public function update_clob_marcdata(array $fields, array $ids) {

        if (!$this->conn_id) {
            log_message("error", "Oracle Bağlantısı yok.");
        }

        $fields_list = array();
        foreach ($fields as $key => $value) {
            $fields_list[] = $this->_escape_identifiers($key) . " = :" . trim($key);
        }

        $id_list = array();
        foreach ($ids as $key => $value) {
            $id_list[] = $this->_escape_identifiers($key) . " = " . $value;
        }

        $sql = 'UPDATE ' . $this->_escape_identifiers("biblios") . ' SET ' . implode(", ", $fields_list) . ' WHERE ' . implode(" AND ", $id_list);
        $stmt = oci_parse($this->conn_id, $sql);

        oci_bind_by_name($stmt, ':marcxml', $fields["marcxml"]);
        oci_bind_by_name($stmt, ':marc', $fields["marc"]);


        if (oci_execute($stmt, OCI_DEFAULT) === false) {
            log_message('error', 'clob alana veri yazılamadı. ' . __CLASS__ . " - " . __FUNCTION__);
            $this->trans_rollback();
            return false;
        } else {
            oci_commit($this->conn_id);
            oci_free_statement($stmt);
        }

        return $this;
    }

    public function update_session_data($ci_id, $ci_timestamp, $ci_data) {

        $sql = 'UPDATE ' . $this->_escape_identifiers(config_item('sess_table_name')) . ' SET ' . $this->_escape_identifiers('data') . ' = :session_data, ' .
                $this->_escape_identifiers('timestamp') . ' = :timestamp_data ' . ' WHERE ' . $this->_escape_identifiers('id') . ' = \'' . $ci_id . '\'';

        $stmt = oci_parse($this->conn_id, $sql);

        oci_bind_by_name($stmt, ':timestamp_data', $ci_timestamp);
        oci_bind_by_name($stmt, ':session_data', $ci_data);


        if (oci_execute($stmt, OCI_DEFAULT) === false) {
            log_message('error', 'clob alana veri yazılamadı. ' . __CLASS__ . " - " . __FUNCTION__);
            $this->trans_rollback();
            return false;
        } else {
            oci_commit($this->conn_id);
            oci_free_statement($stmt);
        }

        return $this;
    }

    public function insert_session_data(array $insert_data) {

        $fields_list = array();
        foreach ($insert_data as $key => $value) {
            $fields_list[] = $this->_escape_identifiers($key);
        }

        $data = $insert_data["data"];


        $sql = 'INSERT INTO ' . $this->_escape_identifiers(config_item('sess_table_name')) . ' (' . implode(", ", $fields_list) . ' ) VALUES (:id_data, :ip_address_data, :timestamp_data, EMPTY_CLOB()) RETURNING ' . $this->_escape_identifiers("data") . ' INTO :session_data';

        $stmt = oci_parse($this->conn_id, $sql);
        $clob = oci_new_descriptor($this->conn_id, OCI_D_LOB);
        oci_bind_by_name($stmt, ':id_data', $insert_data["id"]);
        oci_bind_by_name($stmt, ':ip_address_data', $insert_data["ip_address"]);
        oci_bind_by_name($stmt, ':timestamp_data', $insert_data["timestamp"]);
        oci_bind_by_name($stmt, ':session_data', $clob, -1, OCI_B_CLOB);


        if (oci_execute($stmt, OCI_DEFAULT) === false) {
            log_message('error', 'session clob alana veri yazılamadı. ' . __CLASS__ . " - " . __FUNCTION__);
            $this->trans_rollback();
            return false;
        } else {
            $clob->save($data);
            oci_commit($this->conn_id);
            oci_free_statement($stmt);
        }

        return $this;
    }

    public function update_acs_search_data($lang, $field, $tags) {

        $sql = 'UPDATE ' . $this->_escape_identifiers("solr_fields_from_acs") . ' SET ' . $this->_escape_identifiers("tags") . ' = :tags_data  WHERE ' . $this->_escape_identifiers("lang") . ' = \'' . $lang . '\' AND ' . $this->_escape_identifiers("field") . ' = \'' . $field . '\'';

        $stmt = oci_parse($this->conn_id, $sql);

        oci_bind_by_name($stmt, ':tags_data', $tags);

        if (oci_execute($stmt, OCI_DEFAULT) === false) {
            log_message('error', 'clob alana veri yazılamadı. ' . __CLASS__ . " - " . __FUNCTION__);
            $this->trans_rollback();
            return false;
        } else {
            oci_commit($this->conn_id);
            oci_free_statement($stmt);
        }

        return $this;
    }

    public function update_draft_marc_data($marcxml, $draft_id, $patron_id) {

        $sql = 'UPDATE ' . $this->_escape_identifiers("biblios_draft")
                . ' SET ' . $this->_escape_identifiers("marcxml") . ' = :marcxml  WHERE ' . $this->_escape_identifiers("draft_id") . ' = ' . (integer) $draft_id . ' AND ' . $this->_escape_identifiers("patron_id") . ' = ' . (integer) $patron_id;

        $stmt = oci_parse($this->conn_id, $sql);

        oci_bind_by_name($stmt, ':marcxml', $marcxml);

        if (oci_execute($stmt, OCI_DEFAULT) === false) {
            log_message('error', 'clob alana veri yazılamadı. ' . __CLASS__ . " - " . __FUNCTION__);
            $this->trans_rollback();
            return false;
        } else {
            oci_commit($this->conn_id);
            oci_free_statement($stmt);
        }

        return $this;
    }

    public function update_import_marc_data($marc, $marcxml, $import_id) {

        if (!$this->conn_id) {
            log_message("error", "Oracle Bağlantısı yok.");
        }

        $fields_list = array();
        $fields_list[] = $this->_escape_identifiers("marc") . " = :" . trim("marc");
        $fields_list[] = $this->_escape_identifiers("marcxml") . " = :" . trim("marcxml");

        $id_list = array();
        $id_list[] = $this->_escape_identifiers("import_id") . " = " . (integer) $import_id;

        $sql = 'UPDATE ' . $this->_escape_identifiers("biblio_import_marc") . ' SET ' . implode(", ", $fields_list) . ' WHERE ' . implode(" AND ", $id_list);
        $stmt = oci_parse($this->conn_id, $sql);

        oci_bind_by_name($stmt, ':marc', $marc);
        oci_bind_by_name($stmt, ':marcxml', $marcxml);

        if (oci_execute($stmt, OCI_DEFAULT) === false) {
            log_message('error', 'clob alana veri yazılamadı. ' . __CLASS__ . " - " . __FUNCTION__);
            $this->trans_rollback();
            return false;
        } else {
            oci_commit($this->conn_id);
            oci_free_statement($stmt);
        }

        return $this;
    }

    public function update_auth_marc_data($marc, $marcxml, $authid) {

        if (!$this->conn_id) {
            log_message("error", "Oracle Bağlantısı yok.");
        }

        $fields_list = array();
        $fields_list[] = $this->_escape_identifiers("marcxml") . " = :" . trim("marcxml");
        $fields_list[] = $this->_escape_identifiers("marc") . " = :" . trim("marc");

        $id_list = array();
        $id_list[] = $this->_escape_identifiers("authid") . " = " . (integer) $authid;

        $sql = 'UPDATE ' . $this->_escape_identifiers("auth_header") . ' SET ' . implode(", ", $fields_list) . ' WHERE ' . implode(" AND ", $id_list);
        $stmt = oci_parse($this->conn_id, $sql);

        oci_bind_by_name($stmt, ':marcxml', $marcxml);
        oci_bind_by_name($stmt, ':marc', $marc);

        if (oci_execute($stmt, OCI_DEFAULT) === false) {
            log_message('error', 'clob alana veri yazılamadı. ' . __CLASS__ . " - " . __FUNCTION__);
            $this->trans_rollback();
            return false;
        } else {
            oci_commit($this->conn_id);
            oci_free_statement($stmt);
        }

        return $this;
    }

    /**
     * Internal LIKE
     *
     * @used-by	like()
     * @used-by	or_like()
     * @used-by	not_like()
     * @used-by	or_not_like()
     *
     * @param	mixed	$field
     * @param	string	$match
     * @param	string	$type
     * @param	string	$side
     * @param	string	$not
     * @param	bool	$escape
     * @return	CI_DB_query_builder
     */
    protected function _like($field, $match = '', $type = 'AND ', $side_raw = 'both', $not = '', $escape = NULL) {
        if (!is_array($field)) {
            $field = array($field => $match);
        }

        is_bool($escape) OR $escape = $this->_protect_identifiers;
        // lowercase $side in case somebody writes e.g. 'BEFORE' instead of 'before' (doh)
        $side = strtolower($side_raw);

        foreach ($field as $k => $v) {

            $k = $this->_escape_identifiers($k);

            $prefix = (count($this->qb_where) === 0 && count($this->qb_cache_where) === 0) ? $this->_group_get_type('') : $this->_group_get_type($type);

            if ($escape === TRUE) {
                $v = $this->escape_like_str($v);
            }

            switch ($side) {
                case 'none':
                    $v = "'{$v}'";
                    break;
                case 'before':
                    $v = "%'{$v}'";
                    break;
                case 'after':
                    $v = "'{$v}%'";
                    break;
                case 'both':
                default:
                    $v = "'%{$v}%'";
                    break;
            }

            // some platforms require an escape sequence definition for LIKE wildcards
            if ($escape === TRUE && $this->_like_escape_str !== '') {
                $v .= sprintf($this->_like_escape_str, $this->_like_escape_chr);
            }

            $qb_where = array('condition' => "{$prefix} {$k} {$not} LIKE", 'value' => $v, 'escape' => $escape);

            $this->qb_where[] = $qb_where;
            if ($this->qb_caching === TRUE) {
                $this->qb_cache_where[] = $qb_where;
                $this->qb_cache_exists[] = 'where';
            }
        }

        return $this;
    }

}
