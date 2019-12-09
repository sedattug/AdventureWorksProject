<?php

defined('BASEPATH') || exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$config['librid_indexer_default'] = array();

define('FORCE_SSL', false);

define('WEBSERVICE_IP_WHITESLIST', '127.0.0.1, ::1');

define('ADD_REVIEW_WEBSERVICE', 'api/reviews/add_review');

define('BAD_WORDS', ["fee", "nee", "cruul", "leent"]);

define('DEFAULT_LANG', 'tr');

define('SITE_TITLE_TR', 'AdventureWorks');

define('SITE_TITLE_EN', 'AdventureWorks');

define('SITE_TITLE_SA', 'AdventureWorks');



define("EMAIL_SMTP_HOST", "");

define("EMAIL_SMTP_PORT", "");

define("EMAIL_SMTP_USER", "");

define("EMAIL_SMTP_PASSWORD", "");

define("EMAIL_SENDER", "");

define("EMAIL_SMTP_CRYPTO", 0); // 1 = SSL, 2 = TLS



define('GRAB_DATA_MAX_COUNT', 100);

