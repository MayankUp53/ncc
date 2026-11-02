<?php
require_once __DIR__ . '/readcrm.php';

$dataBaseName = "ncc";
$dataBaseIP = "ncc.ameyoj.com";
$dataBaseUser = "postgres";


$conStr="host=$dataBaseIP user=$dataBaseUser dbname=$dataBaseName";

$connectDB = crm_pg_connect($conStr);

