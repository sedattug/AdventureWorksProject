<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$this->parser->parse('template1/includes/main/header', $headerdata);
$this->parser->parse($main_content_page, $data);
$this->parser->parse('template1/includes/main/footer', $footerdata);

