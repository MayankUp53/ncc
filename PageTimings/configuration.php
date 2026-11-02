<?php
require_once dirname(__DIR__) . '/readcrm.php';
if (!readcrmconf(1)) { http_response_code(500); exit('Configuration unavailable.'); }

$setup_context = $crmtype;   //possible values are  sugarcrm / ameyocrm / other

$debug_enable = filter_var($enablecrmdebug, FILTER_VALIDATE_BOOLEAN);       //possible values are  true / false

$php_debug_enable = true;   // possible values are  true / false

$js_debug_enable = true;   // possible values are  true / false

$show_on_screen = false;   // possible values are  true / false

$db_config = array(
          'database' => 'postgres',           //possible values are mysql /postgres
          'dbhost' => $host,
          'dbname' => $dbname,
          'dbuser' => $user,
          'dbpassword' => $password,
          'dbport' => $port
);
