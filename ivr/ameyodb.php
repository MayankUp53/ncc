<?php
$host = "ncc.ameyoj.com";
$user = "postgres";
$dbname = "ameyodb_ncc";
//$dbname = "am_dome";
$con = pg_connect("host=$host user=$user dbname=$dbname");

if (!$con) {
 die("DB接続に失敗しました。");
}
?>
