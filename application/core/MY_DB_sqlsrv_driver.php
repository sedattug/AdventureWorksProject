<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_DB_sqlsrv_driver extends CI_DB_sqlsrv_driver {

     function db_pconnect() {
        return $this->db_connect(TRUE);
    }

    function escape($str) {
        if (is_string($str)) {
            $str = "N'" . $this->escape_str($str) . "'";
        } elseif (is_bool($str)) {
            $str = ($str === FALSE) ? 0 : 1;
        } elseif (is_null($str)) {
            $str = 'NULL';
        }

        return $str;
    }

    function show_tables() {

        $q = $this->query("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");

        if ($q->num_rows() > 0) {

            $tables = array();

            foreach ($q->result_array() as $table) {

                $tables[] = $table['TABLE_NAME'];
            }


            return $tables;
        } else {
            return FALSE;
        }
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
        $q = $this->query("SELECT column_name FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE OBJECTPROPERTY(OBJECT_ID(constraint_name), 'IsPrimaryKey') = 1 AND table_name = '" . $tablename . "'");
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

    function last_insert_id() {
        return $this->insert_id();
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
                $this->or_where('tp.name <>', $exclude_table);
            }
            $this->group_end();
        }

        $this->group_start();
        if ($status === false) {
            $this->or_where('tp.name', (string) $tablename);
        }
        $this->or_where('tr.name', (string) $tablename);
        $this->group_end();

        $q = $this->select('fk.name fk_name, tp.name parent_table, cp.name parent_name, cp.column_id, tr.name refrence_table, cr.name refrence_name, cr.column_id')
                        ->from('sys.foreign_keys fk')
                        ->join('sys.tables tp', 'fk.parent_object_id = tp.object_id')
                        ->join('sys.tables tr', 'fk.referenced_object_id = tr.object_id')
                        ->join('sys.foreign_key_columns fkc', 'fkc.constraint_object_id = fk.object_id')
                        ->join('sys.columns cp', 'fkc.parent_column_id = cp.column_id AND fkc.parent_object_id = cp.object_id')
                        ->join('sys.columns cr', 'fkc.referenced_column_id = cr.column_id AND fkc.referenced_object_id = cr.object_id')
                        ->order_by('tp.name')->order_by('cp.column_id')->get();

        return ($q->num_rows() > 0) ? $q->result_array() : null;
    }

    function check_field_exist($tablename = null, $fieldname = null) {
        if (empty($tablename) || empty($fieldname)) {
            return null;
        }

        return $this->where('TABLE_NAME', (string) $tablename)->where('COLUMN_NAME', $fieldname)->count_all_results('INFORMATION_SCHEMA.COLUMNS');
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
	 * Execute the query
	 *
	 * @param	string	$sql	an SQL query
	 * @return	resource
	 */
	protected function _execute($sql)
	{
		return ($this->scrollable === FALSE OR $this->is_write_type($sql))
			? sqlsrv_query($this->conn_id, $sql, NULL, array('QueryTimeout' => 300))
			: sqlsrv_query($this->conn_id, $sql, NULL, array('Scrollable' => $this->scrollable, 'QueryTimeout' => 300));
	}    

}
