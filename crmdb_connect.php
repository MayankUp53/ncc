<?php
require_once __DIR__ . '/readcrm.php';

if (!readcrmconf(1)) { http_response_code(500); exit('Configuration unavailable.'); }

// Retain the existing aliases for pages that use this connector.
$dataBaseName = $dbname;
$dataBaseIP = $host;
$dataBaseUser = $user;

$conStr = crm_db_connection_string();

$connectDB = crm_pg_connect($conStr);

