<?php

require_once __DIR__ . '/readcrm.php';

if (!readcrmconf(1)) { http_response_code(500); exit('Configuration unavailable.'); }
$sessionId = crm_request_value($_POST, 'sessionId');
$campaignId = crm_request_value($_POST, 'campaignId');
$crtObjectId = crm_request_value($_POST, 'crtObjectId');
$userCrtObjectId = crm_request_value($_POST, 'userCrtObjectId');
$userId = crm_request_value($_POST, 'userId');
$customerId = crm_request_value($_POST, 'customerId');
$phone = crm_request_value($_POST, 'phone');
$queueId = crm_request_value($_POST, 'queueId');
$queueName = crm_request_value($_POST, 'queueName');
$phoneoriginal = crm_request_value($_POST, 'phoneoriginal');


$fname  =  crm_request_value($_POST, 'fname');
$lname  =  crm_request_value($_POST, 'lname');
$mname  =  crm_request_value($_POST, 'mname');
$title  =  crm_request_value($_POST, 'title');
$dob  =  crm_request_value($_POST, 'dob');
$companyname  =  crm_request_value($_POST, 'companyname');
$houseno  =  crm_request_value($_POST, 'houseno');
$streetno  =  crm_request_value($_POST, 'streetno');
$landmarks  =  crm_request_value($_POST, 'landmarks');
$city  =  crm_request_value($_POST, 'city');
$zip  =  crm_request_value($_POST, 'zip');
$state  =  crm_request_value($_POST, 'state');
$country  =  crm_request_value($_POST, 'country');
$homephone  =  crm_request_value($_POST, 'homephone');
$officephone  =  crm_request_value($_POST, 'officephone');
$mobileno  =  crm_request_value($_POST, 'mobileno');
$altno  =  crm_request_value($_POST, 'altno');
$faxno  =  crm_request_value($_POST, 'faxno');
$emailid  =  crm_request_value($_POST, 'emailid');


$values = array(
    'fname' => $fname,
    'lname' => $lname,
    'mname' => $mname,
    'title' => $title,
    'dob' => $dob,
    'companyname' => $companyname,
    'houseno' => $houseno,
    'streetno' => $streetno,
    'landmarks' => $landmarks,
    'city' => $city,
    'zip' => $zip,
    'state' => $state,
    'country' => $country,
    'homephone' => $homephone,
    'officephone' => $officephone,
    'mobileno' => $mobileno,
    'altno' => $altno,
    'faxno' => $faxno,
    'emailid' => $emailid
);
$assignments = array();
$params = array_values($values);
foreach (array_keys($values) as $index => $column) {
    $assignments[] = $column . '=$' . ($index + 1);
}
$setClause = implode(',', $assignments) . ',updatedon=now()';
$customerParams = $params;
$usePhone = $customerId === '' || $customerId < 0;
$customerParams[] = $usePhone ? $phone : $customerId;
$insertPGSQL = 'UPDATE customer SET ' . $setClause . ' WHERE ' . ($usePhone ? 'phone1' : 'customer_id') . '=$' . count($customerParams);
$historyParams = $params;
$historyParams[] = $userId;
$userPlaceholder = '$' . count($historyParams);
$historyParams[] = $crtObjectId;
$insertPGSQLCALL = 'UPDATE customer_history SET ' . $setClause . ',user_id=' . $userPlaceholder . ' WHERE crt_object_id=$' . count($historyParams);
$connection = crm_pg_connect("host=$host user=$user dbname=$dbname");
if ($connection === false) { http_response_code(503); exit('Database connection failed.'); }
if (crm_pg_query($connection, 'BEGIN') === false) { http_response_code(500); exit('Could not start database transaction.'); }
$result = crm_pg_query($connection, $insertPGSQL, $customerParams);
$resultcall = $result !== false ? crm_pg_query($connection, $insertPGSQLCALL, $historyParams) : false;
if ($result === false || $resultcall === false) {
    crm_pg_query($connection, 'ROLLBACK');
    http_response_code(500);
    exit('Could not save customer and history.');
}
if (crm_pg_query($connection, 'COMMIT') === false) { http_response_code(500); exit('Could not commit customer and history.'); }
