<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_DB_mssql_driver extends CI_DB_mssql_driver {

    /**
     * Limit string
     *
     * Generates a platform-specific LIMIT clause
     *
     * @param	string	the sql query string
     * @param	integer	the number of rows to limit the query to
     * @param	integer	the offset value
     * @return	string
     */
    function _limit($sql, $limit = false, $offset = false) {

        if ($offset === false) {
            $i = $limit;
            return preg_replace('/(^\SELECT (DISTINCT)?)/i', '\\1 TOP ' . $i . ' ', $sql);
        }

        if (count($this->ar_orderby) > 0) {
            $ordeR_by = "ORDER BY ";
            $ordeR_by .= implode(', ', $this->ar_orderby);

            if ($this->ar_order !== FALSE) {
                $ordeR_by .= ($this->ar_order == 'desc') ? ' DESC' : ' ASC';
            }

            $sql = preg_replace('/(\\' . $ordeR_by . '\n?)/i', '', $sql);
            $sql = preg_replace('/(^\SELECT (DISTINCT)?)/i', '\\1 row_number() OVER (' . $ordeR_by . ') AS rownum, ', $sql);

            /* $columns = implode(',',$this->ar_select);
              $newSQL = "SELECT " . $columns . " \nFROM (\n" . $sql . ") AS A \nWHERE A.CI_offset_row_number BETWEEN (" .($offset + 1) . ") AND (".($offset + $limit).")";
              return     $newSQL; */
            return "SELECT * FROM (" . $sql . ") AS A WHERE A.rownum BETWEEN (" . ($offset + 1) . ") AND (" . ($offset + $limit) . ")";
        } else {
            echo 'Query must have an order_by clause in order to be offset.';
        }
    }

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

        if ($time === false) {
            $date = date("Y-m-d", strtotime($date));
            $date_hour_arr = array();
        } else {
            $date = date("Y-m-d H:i:s", strtotime($date));
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

    function localized_to_db_date($date) {

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

    function get_primary_key($tablename = null, $exclude_tables = array()) {
        if (empty($tablename)) {
            return false;
        }

        if (is_array($exclude_tables) && count($exclude_tables) > 0) {
            foreach ($exclude_tables as $exclude_table) {
                $this->db->where('table_name <>', $exclude_table);
            }
        }

        $this->db->select('column_name')->from('INFORMATION_SCHEMA.KEY_COLUMN_USAGE')
                ->where("OBJECTPROPERTY(OBJECT_ID(constraint_name), 'IsPrimaryKey') = 1", null, false)
                ->where('table_name', (string) $tablename)->get();
        if ($q->num_rows() > 0) {
            return $q->row()->column_name;
        }
        return false;
    }

    function where_for_date_field($field, $date_value) {
        return $this->where($field, $date_value);
    }

}
