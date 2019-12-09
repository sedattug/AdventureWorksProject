<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class MY_DB_mysqli_driver extends CI_DB_mysqli_driver {
 
        
        
        function show_tables()
        {
            
            $q = $this->query("SHOW TABLES");
            
            if ($q->num_rows() > 0)
            {

                $tables = array();

                foreach ($q->result_array() as $table)
                {

                    $tables[] = $table['Tables_in_' . $this->database];
                }


                return $tables;
            }
            else
            {
                return FALSE;
            }
        }
        
        
        function db_to_localized_date($date, $time = FALSE)
        {
            
            $date_hour_arr = explode(' ', $date);
            
            if (count($date_hour_arr) !== 2)
            {
                throw new Exception('WRONG_DB_DATE_FORMAT');
            }
            else
            {

                $date_arr = explode(DATE_SEPARATOR, $date_hour_arr[0]);
                $time_arr = explode(':', $date_hour_arr[1]);


                if (count($date_arr) !== 3  ||  count($time_arr) !== 3)
                {
                    throw new Exception('WRONG_DATE_FORMAT');
                }
                else
                {

                    $year   = $date_arr[0];
                    $month  = $date_arr[1];
                    $day    = $date_arr[2];
                    $hour   = $time_arr[0];
                    $minute = $time_arr[1];
                    $second = $time_arr[2]; // BURAYI AŞAĞIDAKİ KONTROLE DAHİL ETMEDİM

                    // is_numeric kontrolü de olabilir
                    if (strlen($year) !== 4  || strlen($month) !== 2  || strlen($day) !== 2  || strlen($hour) !== 2  || strlen($minute) !== 2)
                    {
                        throw new Exception('WRONG_DATE_DETAIL_FORMAT');
                    }
                    else
                    {

                        if ($time === FALSE)
                        {
                            return $day. DATE_SEPARATOR . $month . DATE_SEPARATOR . $year;
                        }
                        else
                        {
                            return $day. DATE_SEPARATOR . $month . DATE_SEPARATOR . $year . ' ' . $hour . ':' . $minute . ':' . $second;
                        }

                    }

                }

            }

        }


        function localized_to_db_date($date)
        {

            $date_arr = explode(DATE_SEPARATOR, $date);

            if (count($date_arr) !== 3)
            {
                throw new Exception('WRONG_LOCALIZED_DATE_FORMAT');
            }
            else
            {

                $year  = $date_arr[2];
                $month = $date_arr[1];
                $day   = $date_arr[0];

                if (strlen($day) !== 2 || strlen($month) !== 2 || strlen($year) !== 4)
                {
                    throw new Exception('WRONG_LOCALIZED_DATE_DETAIL_FORMAT');
                }
                else
                {
                    //return $year. DATE_SEPARATOR . $month . DATE_SEPARATOR . $day . ' 00:00:00';
                    return date(DATE_FORMAT, mktime(0, 0, 0, $month, $day, $year));
                }

            }

        }
 
}