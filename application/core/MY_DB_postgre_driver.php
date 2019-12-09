<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_DB_postgre_driver extends CI_DB_postgre_driver {

    function db_pconnect() {
        return $this->db_connect(TRUE);
    }

    function db_to_localized_date($date, $time = FALSE) {

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

    function localized_to_db_date($date) {

        $date_arr = explode(DATE_SEPARATOR, $date);

        if (count($date_arr) !== 3) {
            throw new Exception('WRONG_LOCALIZED_DATE_FORMAT');
        } else {

            $year = $date_arr[2];
            $month = $date_arr[1];
            $day = $date_arr[0];

            if (strlen($day) !== 2 || strlen($month) !== 2 || strlen($year) !== 4) {
                log_message('error', 'Gün : ' . $day . ' Ay: ' . $month . ' Yıl : ' . $year);
                throw new Exception('WRONG_LOCALIZED_DATE_DETAIL_FORMAT');
            } else {
                return date(DATE_FORMAT, mktime(0, 0, 0, $month, $day, $year));
            }
        }
    }

    function get_primary_key($tablename = null) {
        if (empty($tablename)) {
            return false;
        }

        $sql = "SELECT a.attname column_name FROM pg_index i JOIN   pg_attribute a ON a.attrelid = i.indrelid  AND a.attnum = ANY(i.indkey) WHERE  i.indrelid = ?::regclass AND i.indisprimary";

        $q = $this->query($sql);
        if ($q->num_rows() > 0) {
            return $q->row()->column_name;
        }
        return false;
    }

    function where_for_date_field($field, $date_value, $statement = '') {
        return $this->where($field . $statement, $date_value);
    }

    function or_where_for_date_field($field, $date_value, $statement = '') {
        return $this->or_where($field . $statement, $date_value);
    }

    function set_date_field($date_field, $date_value, $statement = '') {
        return $this->set($date_field . $statement, $date_value);
    }

    function last_insert_id($tablename, $table_id_name) {
        if (defined('POSTGRESQL_VERSION') && POSTGRESQL_VERSION === '9') {
            return $this->insert_id();
        } else if (defined('POSTGRESQL_VERSION') && POSTGRESQL_VERSION === '10') {
            return $this->query("SELECT CURRVAL(pg_get_serial_sequence('" . $tablename . "', '" . $table_id_name . "')) as insert_id")->row()->insert_id;
        } else {
            log_message('error', 'POSTGRESQL_VERSION değeri 9 ya da 10 olarak library_config içinde tanımlanmamış');
            die('POSTGRESQL_VERSION değeri 9 ya da 10 olarak library_config içinde tanımlanmamış');
        }
    }

    function select_blob($field_name) {
        return $this->select($field_name);
    }

    function get_dbtable_relations($tablename = null, $status = false, $exclude_tables = array()) {
        if (empty($tablename)) {
            return null;
        }

        if (is_array($exclude_tables) && count($exclude_tables) > 0) {
            $this->group_start();
            foreach ($exclude_tables as $exclude_table) {
                $this->or_where('tc.table_name <>', $exclude_table);
            }
            $this->group_end();
        }

        $this->group_start();
        if ($status === false) {
            $this->or_where('tc.table_name', (string) $tablename);
        }
        $this->or_where('ccu.table_name', (string) $tablename);
        $this->group_end();

        $q = $this->select('tc.constraint_name fk_name, tc.table_name parent_table, kcu.column_name parent_name, ccu.table_name refrence_table, ccu.column_name refrence_name')
                ->from('information_schema.table_constraints tc')
                ->join('information_schema.key_column_usage kcu', 'tc.constraint_name = kcu.constraint_name')
                ->join('information_schema.constraint_column_usage ccu', 'ccu.constraint_name = tc.constraint_name')
                ->where('constraint_type', 'FOREIGN KEY')
                ->get();

        return ($q->num_rows() > 0) ? $q->result_array() : null;
    }

    function check_field_exist($tablename = null, $fieldname = null) {
        if (empty($tablename) || empty($fieldname)) {
            return null;
        }

        return $this->where('table_name', (string) $tablename)->where('column_name', $fieldname)->count_all_results('information_schema.columns');
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
    protected function _like($field, $match = '', $type = 'AND ', $side = 'both', $not = '', $escape = NULL) {
        if (!is_array($field)) {
            $field = array($field => $match);
        }

        is_bool($escape) OR $escape = $this->_protect_identifiers;
        // lowercase $side in case somebody writes e.g. 'BEFORE' instead of 'before' (doh)
        $side = strtolower($side);

        foreach ($field as $k => $v) {
            $prefix = (count($this->qb_where) === 0 && count($this->qb_cache_where) === 0) ? $this->_group_get_type('') : $this->_group_get_type($type);

            if ($escape === TRUE) {
                $v = $this->escape_like_str($v);
            }

            if ($side === 'none') {
                $like_statement = "{$prefix} trim(upper(norm_text({$k}))) {$not} LIKE UPPER(norm_text('{$v}'))";
            } elseif ($side === 'before') {
                $like_statement = "{$prefix} trim(upper(norm_text({$k}))) {$not} LIKE UPPER(norm_text('%{$v}'))";
            } elseif ($side === 'after') {
                $like_statement = "{$prefix} trim(upper(norm_text({$k}))) {$not} LIKE UPPER(norm_text('{$v}%'))";
            } else {
                $like_statement = "{$prefix} trim(upper(norm_text({$k}))) {$not} LIKE UPPER(norm_text('%{$v}%'))";
            }

            // some platforms require an escape sequence definition for LIKE wildcards
            if ($escape === TRUE && $this->_like_escape_str !== '') {
                $like_statement .= sprintf($this->_like_escape_str, $this->_like_escape_chr);
            }

            $this->qb_where[] = array('condition' => $like_statement, 'escape' => $escape);
            if ($this->qb_caching === TRUE) {
                $this->qb_cache_where[] = array('condition' => $like_statement, 'escape' => $escape);
                $this->qb_cache_exists[] = 'where';
            }
        }

        return $this;
    }

}
