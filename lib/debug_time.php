<?php

#require_once __DIR__ . '/readcrm.php';
#readcrmconf(1);


function startTime($phone, $campaignId, $unique_id, $push_time, $url_time, $client_server_time_diff,$sessionId,$userId)
{
	global $urlTimeFormatted;
	global	$requesttodbtime;
	if($push_time=="") $push_time='1970-1-1 00:06:00.000';
	if($unique_id=="") $unique_id=null;
	if($userId=="") $userId=null;
	if($sessionId=="") $sessionId=null;
    // Accept the original yyyy-mm-dd and dd-mm-yyyy formats, including absent milliseconds.
    $urlTimeFormatted = '1970-01-01 00:06:00.000';
    if (is_string($url_time) && preg_match('/^(\d{4})-\d{1,2}-\d{1,2} /', $url_time)) {
        $urlTimeFormatted = $url_time;
    } elseif (is_string($url_time) && preg_match('/^(\d{2})-(\d{2})-(\d{4}) (\d{1,2}):(\d{1,2}):(\d{1,2})(?:\.(\d+))?$/', trim($url_time), $parts)) {
        $urlTimeFormatted = sprintf('%s-%s-%s %02d:%02d:%02d.%03d', $parts[3], $parts[2], $parts[1], $parts[4], $parts[5], $parts[6], (int) ($parts[7] ?? 0));
    }
	$requesttodbtime=date("Y-m-d H:i:s.u",time());

}

function endTime($phone, $campaignId, $unique_id, $push_time, $url_time, $client_server_time_diff, $urlTimeFormatted,$requesttodbtime,$sessionId,$userId,$host, $user, $dbname) {
	
	global $responsefromdbtime;
	$responsefromdbtime=date("Y-m-d H:i:s.u",time());

    $push_query = 'INSERT INTO crm_push_history (phone,campaign_id,unique_id,push_time,crmpopup_time,databaserequesttime,databaseresponsetime,server_client_timediff,userId,sessionId) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10)';
    $conn2 = crm_pg_connect(crm_db_connection_string(array(
        'host' => $host, 'user' => $user, 'dbname' => $dbname,
        'password' => $GLOBALS['password'] ?? '', 'port' => $GLOBALS['port'] ?? ''
    )), PGSQL_CONNECT_FORCE_NEW);
    if ($conn2 === false) { error_log('Push timing database unavailable.'); return; }
    $push_hist = crm_pg_query($conn2, $push_query, array($phone, $campaignId, $unique_id, $push_time, $urlTimeFormatted, $requesttodbtime, $responsefromdbtime, $client_server_time_diff, $userId, $sessionId));
    if ($push_hist === false) { error_log('Push timing query failed.'); }
    pg_close($conn2);
}
