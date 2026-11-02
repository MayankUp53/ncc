<?php
require_once __DIR__ . '/readcrm.php';
if (!readcrmconf(1)) { http_response_code(500); exit('Configuration unavailable.'); }
$renderingstarttime = crm_request_value($_GET, 'browserstarttime');
$renderingendtime = crm_request_value($_GET, 'browserendtime');
$queryendtime = crm_request_value($_GET, 'queryendtime');
$responsefromdbtime = crm_request_value($_GET, 'responsefromdbtime');
$requesttodbtime = crm_request_value($_GET, 'requesttodbtime');
$crmpopuptime = crm_request_value($_GET, 'crmpopuptime');
$timedifference = crm_request_value($_GET, 'timedifference');

echo "ブラウザがサービスを開始した時間：".$renderingstarttime;
echo "<br>";
echo "ブラウザがサービスを終了した時間：".$renderingendtime;
echo "<br>";
echo "CRMポップアップ時間：".$crmpopuptime;
echo "<br>";
echo "クライアントサーバ時間差異：".$timedifference;
echo "<br>";
$sessionId = crm_request_value($_GET, 'sessionId');
$campaignId = crm_request_value($_GET, 'campaignId');
$crtObjectId = crm_request_value($_GET, 'crtObjectId');
$userCrtObjectId = crm_request_value($_GET, 'userCrtObjectId');
$userId = crm_request_value($_GET, 'userId');
$customerId = crm_request_value($_GET, 'customerId');
$phone = crm_request_value($_GET, 'phone');
$queueId = crm_request_value($_GET, 'queueId');
$queueName = crm_request_value($_GET, 'queueName');
$unique_id = crm_request_value($_GET, 'unique_id');
$push_time = crm_request_value($_GET, 'push_time');


$connection = crm_pg_connect("host=$host user=$user dbname=$dbname");

if (!$connection) {
        #echo "エラー：".pg_result_error($connection).".";
        echo "DB接続中にエラーが発生しました\n";
        exit;
}

$ren_query='INSERT INTO crm_browser_render_history (phone,campaign_id,unique_id,rendering_starttime,renderingendtime) VALUES ($1,$2,$3,$4,$5)';

$ren_hist = crm_pg_query($connection,$ren_query,array($phone,$campaignId,$unique_id,$renderingstarttime,$renderingendtime));
if(!$ren_hist){
                echo "<tr><td colspan=2 align='center'>";
                echo "エラー：クエリ実行エラー（crm_browser_render_history）";
                echo "</td></tr>";
                exit();
        }

?>
