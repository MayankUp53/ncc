<?php
// Get querystring variables from URL
//var_dump($_GET);
//echo "Hi";
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
date_default_timezone_set('Asia/Tokyo');
$currenttime=date("Y-m-d H:i:s.u",time());
require_once __DIR__ . '/readcrm.php';
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
$div_id  = crm_request_value($_GET, 'div_id');
//echo "<br>";

if (!readcrmconf(1)) { http_response_code(500); exit('Configuration unavailable.'); }
$connection = crm_pg_connect(crm_db_connection_string());

if (!$connection) {
#echo "Error: ".pg_result_error($connection).".";
        echo "DB接続中にエラーが発生しました\n";
        exit;
}
$phonenum = (explode ("-",$phone));
$phoneoriginal = ($phonenum[0]);

$result = crm_pg_query($connection, 'SELECT * FROM customer WHERE phone1=$1', array($phone));
if (!$result) {
        echo "クエリ実行中にエラーが発生しました\n";
        exit;
}
$res_hist = crm_pg_query($connection, 'select updatedon,disposition,customer_type,memo1,memo2 FROM customer_history WHERE phone1=$1 order by call_date desc limit 12', array($phone));
#$temp = "select updatedon,disposition,customer_type,memo1,memo2 FROM customer_history WHERE phone1='$phone' or customer_id='$customerId' order by call_date desc limit 12";
#echo $temp;
if (!$res_hist) {
        echo "クエリ実行中にエラーが発生しました\n";
        exit;
}

$data = pg_fetch_assoc($result);


?>
                        <table height="100%"  width="100%" border="0" align="center" cellpadding="0" cellspacing="0" >
                                <tr width="100%">
                                        <td colspan="3">&nbsp;</td>
                                </tr>
                                <tr width="100%">
                                        <td class="heading"><strong>&nbsp;&nbsp;履歴情報</strong></td>
                                        <td>&nbsp;</td>
                                        <td width="50"></td>
                                </tr>
                                <tr width="100%">
                                        <td height="1" colspan="3" bgcolor="#CCCCCC"></td>
                                </tr>
                                <tr width="100%">
                                        <td height="25" colspan="3">&nbsp;</td>
                                </tr>
                                <tr width="100%">
                                        <td colspan="3">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                                <td height="25" colspan="5" >&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                                <td class="linktexblue" width="20%" align="center">コンタクト日時</td>
                                                                <td class="linktexblue" width="20%" align="center">対応者</td>
                                                                <td class="linktexblue" width="20%"align="center">相談者</td>
								<td class="linktexblue" width="20%"align="center">主訴</td>
								<td class="linktexblue" width="20%"align="center">対応</td>
                                                        </tr>
                                                        <?php while($data_history=pg_fetch_row($res_hist))
                                                        { ?>
                                                        <tr>
                                                                <td class="textlitcust" align="center"><?php  echo mb_substr($data_history[0] ?? '',0,19) ?></td>
                                                                <td class="textlitcust" align="center"><?php  echo $data_history[1] ?></td>
                                                                <td class="textlitcust" align="center"><?php  echo $data_history[2] ?></td>
                                                                <td class="textlitcust" align="center"><?php  echo $data_history[3] ?></td>
                                                                <td class="textlitcust" align="center"><?php  echo $data_history[4] ?></td>
                                                        </tr>
                                                        <?php }  ?>
                                                        <tr>
                                                                <td height="25" colspan="5" >&nbsp;</td>
                                                        </tr>
                                                </table>


                                        </td>
                                </tr>
                        </table>

<?php  pg_close($connection); ?>	
