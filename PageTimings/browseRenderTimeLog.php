<?php
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'xmlhttprequest') { http_response_code(400); exit; }
require_once __DIR__ . '/PagePerformance.php';
if (!$debug_enable) { http_response_code(204); exit; }
$start = crm_request_value($_GET, 'renderStartTime');
$end = crm_request_value($_GET, 'renderEndTime');
$uniqueId = crm_request_value($_GET, 'uniqueId');
if (!ctype_digit($start) || !ctype_digit($end) || !ctype_digit($uniqueId) || (float) $end < (float) $start) {
    http_response_code(400);
    exit('Invalid rendering timestamps.');
}
$obj = new PagePerformance();
$res = $obj->connect($db_config);
if ($res === false) { http_response_code(503); exit; }
$params = array($uniqueId, $start, $end, (float) $end - (float) $start);
if ($db_config['database'] === 'postgres') {
    $params[1] = $obj->millisecondsToPostgresTimestamp($start);
    $params[2] = $obj->millisecondsToPostgresTimestamp($end);
    $sql = 'INSERT INTO crm_render_history (id,render_start_time,render_end_time,total_render_time) VALUES ($1,$2,$3,$4)';
} else {
    $sql = 'INSERT INTO crm_render_history (id,render_start_time,render_end_time,total_render_time) VALUES (?,?,?,?)';
}
$result = $obj->query($db_config, $sql, $res, $params);
$obj->disconnect($db_config, $res);
http_response_code($result === false ? 500 : 204);
