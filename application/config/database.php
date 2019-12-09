<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

$active_group = 'postgre';
//$active_record = TRUE;
$query_builder = true;

$db['postgre']['hostname'] = 'localhost';
$db['postgre']['port'] = 5432;
$db['postgre']['username'] = 'postgres';
$db['postgre']['password'] = '';
$db['postgre']['database'] = 'Adventureworks';
$db['postgre']['dbdriver'] = 'postgre';
$db['postgre']['dbprefix'] = '';
$db['postgre']['pconnect'] = false;
$db['postgre']['db_debug'] = true;
$db['postgre']['cache_on'] = false;
/* $db['postgre']['cachedir'] = '/var/subayogam_2.0/cache'; */
//$db['postgre']['cachedir'] = 'C:\Subayogam_v2.0\Codeigniter\cache';
$db['postgre']['char_set'] = 'utf8';
$db['postgre']['dbcollat'] = 'utf8_general_ci';
$db['postgre']['swap_pre'] = '';
$db['postgre']['autoinit'] = TRUE;
$db['postgre']['stricton'] = FALSE;


if (!defined('CURRENT_DATE')) {
    if ($active_group === 'default') {// MSSQL
        define('DATE_SEPARATOR', '-');
        define('DATE_FORMAT', 'Y' . DATE_SEPARATOR . 'm' . DATE_SEPARATOR . 'd');
        define('LOCAL_DATE_FORMAT', 'd' . DATE_SEPARATOR . 'm' . DATE_SEPARATOR . 'Y');
        define('DATE_FORMAT_WITH_HOUR', DATE_FORMAT . ' H:i:s');
        define('CURRENT_DATE', date(DATE_FORMAT_WITH_HOUR));
        define('DATEPICKER_FORMAT', 'dd' . DATE_SEPARATOR . 'mm' . DATE_SEPARATOR . 'yy');
        define('DATEPICKER_FORMAT_NEW', 'dd' . DATE_SEPARATOR . 'mm' . DATE_SEPARATOR . 'yyyy');
        /*
         * deprecated definitions
         */
        define("DATE_FORMAT_TYPE_NO", 6);
    } else if ($active_group === 'mysqli') {// MYSQL
        define('DATE_SEPARATOR', '-');
        define('DATE_FORMAT', 'Y' . DATE_SEPARATOR . 'm' . DATE_SEPARATOR . 'd');
        define('LOCAL_DATE_FORMAT', 'd' . DATE_SEPARATOR . 'm' . DATE_SEPARATOR . 'Y');
        define('DATE_FORMAT_WITH_HOUR', DATE_FORMAT . ' H:i:s');
        define('CURRENT_DATE', date(DATE_FORMAT_WITH_HOUR));
        define('DATEPICKER_FORMAT', 'dd' . DATE_SEPARATOR . 'mm' . DATE_SEPARATOR . 'yy');
        /*
         * deprecated definitions
         */
        define("DATE_FORMAT_TYPE_NO", 7);
    } else if ($active_group === 'oci8') {// ORACLE 11G
        define('DATE_SEPARATOR', '/');
        define('DATE_FORMAT', 'Y' . DATE_SEPARATOR . 'm' . DATE_SEPARATOR . 'd');
        define('LOCAL_DATE_FORMAT', 'd' . DATE_SEPARATOR . 'm' . DATE_SEPARATOR . 'Y');
        define('DATE_FORMAT_WITH_HOUR', DATE_FORMAT . ' H:i:s');
        define('CURRENT_DATE', date(DATE_FORMAT_WITH_HOUR));
        define('DATEPICKER_FORMAT', 'dd' . DATE_SEPARATOR . 'mm' . DATE_SEPARATOR . 'yy');
        /*
         * deprecated definitions
         */
        define("DATE_FORMAT_TYPE_NO", 7);
    } else {// DEFAULT(MSSQL)
        define('DATE_SEPARATOR', '-');
        define('DATE_FORMAT', 'Y' . DATE_SEPARATOR . 'm' . DATE_SEPARATOR . 'd');
        define('LOCAL_DATE_FORMAT', 'd' . DATE_SEPARATOR . 'm' . DATE_SEPARATOR . 'Y');
        define('DATE_FORMAT_WITH_HOUR', DATE_FORMAT . ' H:i:s');
        define('CURRENT_DATE', date(DATE_FORMAT_WITH_HOUR));
        define('DATEPICKER_FORMAT', 'dd' . DATE_SEPARATOR . 'mm' . DATE_SEPARATOR . 'yy');
        /*
         * deprecated definitions
         */
        define("DATE_FORMAT_TYPE_NO", 6);
    }
}
/*
 * 
 * TO_DATE('2003/05/03 21:02:44', 'yyyy/mm/dd hh24:mi:ss')
  | -------------------------------------------------------------------
  | DATABASE CONNECTIVITY SETTINGS
  | -------------------------------------------------------------------
  | This file will contain the settings needed to access your database.
  |
  | For complete instructions please consult the 'Database Connection'
  | page of the User Guide.
  |
  | -------------------------------------------------------------------
  | EXPLANATION OF VARIABLES
  | -------------------------------------------------------------------
  |
  |	['hostname'] The hostname of your database server.
  |	['username'] The username used to connect to the database
  |	['password'] The password used to connect to the database
  |	['database'] The name of the database you want to connect to
  |	['dbdriver'] The database type. ie: mysql.  Currently supported:
  mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
  |	['dbprefix'] You can add an optional prefix, which will be added
  |				 to the table name when using the  Active Record class
  |	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
  |	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
  |	['cache_on'] TRUE/FALSE - Enables/disables query caching
  |	['cachedir'] The path to the folder where cache files should be stored
  |	['char_set'] The character set used in communicating with the database
  |	['dbcollat'] The character collation used in communicating with the database
  |				 NOTE: For MySQL and MySQLi databases, this setting is only used
  | 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
  |				 (and in table creation queries made with DB Forge).
  | 				 There is an incompatibility in PHP with mysql_real_escape_string() which
  | 				 can make your site vulnerable to SQL injection if you are using a
  | 				 multi-byte character set and are running versions lower than these.
  | 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
  |	['swap_pre'] A default table prefix that should be swapped with the dbprefix
  |	['autoinit'] Whether or not to automatically initialize the database.
  |	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
  |							- good for ensuring strict SQL while developing
  |
  | The $active_group variable lets you choose which connection group to
  | make active.  By default there is only one group (the 'default' group).
  |
  | The $active_record variables lets you determine whether or not to load
  | the active record class
 */