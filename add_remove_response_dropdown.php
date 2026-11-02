<?php
require_once __DIR__ . '/readcrm.php';
header('Content-Type: application/json; charset=UTF-8');
foreach (array('sessionId', 'userId', 'action', 'value') as $key) {
    if (!isset($_GET[$key]) || !is_string($_GET[$key])) {
        http_response_code(400);
        echo json_encode(array('response' => 'failure', 'message' => 'Missing or invalid parameter: ' . $key));
        exit;
    }
}
$sessionId = $_GET['sessionId'];
$userId = $_GET['userId'];
$action = $_GET['action'];
$dropdownValue = $_GET['value'];
if (!in_array($action, array('insert', 'delete'), true)) {
    http_response_code(400);
    echo json_encode(array('response' => 'failure', 'message' => 'Invalid action'));
    exit;
}
if (!readcrmconf(1)) {
    http_response_code(500);
    echo json_encode(array('response' => 'failure', 'message' => 'Configuration unavailable'));
    exit;
}
$connection = crm_pg_connect("host=$host user=$user dbname=$dbname");
if ($connection === false) {
    http_response_code(503);
    echo json_encode(array('response' => 'failure', 'message' => 'Database unavailable'));
    exit;
}
if ($action === 'insert') {
    $sql = 'INSERT INTO custom_mainresponse_dropdowns (dd_value, ins_session_id, ins_user_id) VALUES ($1, $2, $3)';
    $result = crm_pg_query($connection, $sql, array($dropdownValue, $sessionId, $userId));
    $message = $result !== false ? 'Inserted Successfully' : 'Not Inserted';
} else {
    $sql = "UPDATE custom_mainresponse_dropdowns SET is_deleted='1', del_session_id=$1, del_user_id=$2 WHERE dd_value=$3";
    $result = crm_pg_query($connection, $sql, array($sessionId, $userId, $dropdownValue));
    $message = $result !== false ? 'Deleted Successfully' : 'Not Deleted';
}
if ($result === false) { http_response_code(500); }
echo json_encode(array('response' => $result !== false ? 'success' : 'failure', 'message' => $message));
