<?php
//including PageTimings
date_default_timezone_set('Asia/Tokyo');
include_once 'PageTimings/PagePerformance.php'; 
include_once 'crmdb_connect.php';
include_once 'day.php';
$pagePerformance = new PagePerformance(true);

$currenttime=date("Y-m-d H:i:s.u",time());
$calldate=date("Y-m-d H:i:s",time());
$crmdate=date("Y-m-d",time());
$crmtime=date("H:i:s",time());
$currentday=date("D",time());
$crmday = convertday($currentday);
$sessionId = $_GET['sessionId'];
$campaignId = $_GET['campaignId'];
$crtObjectId = $_GET['crtObjectId'];
$userCrtObjectId = $_GET['userCrtObjectId'];
$userId = $_GET['userId'];
$customerId = $_GET['customerId'];
$phone = $_GET['phone'];
$queueId = $_GET['queueId'];
$queueName = $_GET['queueName'];
$unique_id = $_GET['unique_id'];
$push_time = $_GET['push_time'];
$url_time = $_GET['url-time-stamp'];
if(!$url_time) {
	$url_time='1970-01-01 00:06:00.000';
}
if(!$push_time) {
	$push_time='1970-01-01 00:06:00.000';
}
$client_server_time_diff = $_GET['client-server-time-diff'];

readcrmconf(1);

//echo "$enablecrmdebug";

require_once ('./lib/debug_time.php');

if ($enablecrmdebug == "true") {
	startTime($phone, $campaignId, $unique_id, $push_time, $url_time, $client_server_time_diff,$sessionId,$userId);
}

//echo $urlTimeFormatted;

//echo "<br>";
//echo "sessionId.$sessionId";
if (strlen($phone) == 0 || ($phone) < 0) {
        echo "電話番号入力エラー";
        exit;
}

if (strlen($customerId) == 0 || ($customerId) < 0) {
error_log("This is just a Notification message : It  seems and inbound campaign URL(customerId is missing) "."http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI']);
$criteria = "phone";

//echo "http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
}
else {
$criteria = "customerid";
}

$url_params = "sessionId=$sessionId&campaignId=$campaignId&crtObjectId=$crtObjectId&userCrtObjectId=$userCrtObjectId&userId=$userId&customerId=$customerId&phone=$phone&queueId=$queueId&queueName=$queueName&unique_id=$unique_id&push_time=$push_time&criteria=$criteria";



$connection = pg_connect("host=$host user=$user dbname=$dbname");

if (!$connection) {
#echo "エラー：".pg_result_error($connection).".";
        echo "DB接続中にエラーが発生しました\n";
        exit;
}
$phonenum = (explode ("-",$phone));
$phoneoriginal = ($phonenum[0]);


if($criteria =="customerid"){
$result = pg_query("SELECT * FROM customer WHERE customer_id='$customerId'");
if (!$result) {
        echo "クエリ実行中にエラーが発生しました\n";
        exit;
}
$data = pg_fetch_assoc($result);
$lid=$data['leadid'];
}
//        echo $lid;
if (!$data) {
	$result = pg_query("SELECT * FROM customer WHERE phone1='$phone'");
	if (!$result) {
       	 	echo "クエリ実行中にエラーが発生しました\n";
        	exit;
	}	
	$data = pg_fetch_assoc($result);
	$lid=$data['leadid'];
	$crm_customerId=$data['customer_id'];
        if ("$customerId" != "$crm_customerId") {
                $UPDATECUSTID="UPDATE customer set customer_id='$customerId' where phone1='$phone'";
                $UpdateCustCRMid = pg_query($connection,$UPDATECUSTID);
                if (!$UpdateCustCRMid) {
                        echo "お客様ID更新中にエラーが発生しました";
                        exit ;
                }
        }
//	echo $lid;
}

if (!$data) {
    
//echo "Adding New Customer"; 
	$connection = pg_connect("host=$host user=$user dbname=$dbname");
	$insertPGSQLcustomer = "INSERT INTO customer(phone1,leadid,customer_id) VALUES ('$phone','Default','$customerId')";

$lid="Default";

//echo " $insertPGSQLcustomer \n  $insertPGSQLT";
        $resultcustomer = pg_query($connection,$insertPGSQLcustomer);
        if (!$resultcustomer) {
		if ($enablecrmdebug == "true") {
			endTime($phone, $campaignId, $unique_id, $push_time, $url_time, $client_server_time_diff,$urlTimeFormatted,$requesttodbtime,$sessionId,$userId,$host, $user, $dbname);
		}
		echo "クエリ実行中にエラーが発生しました（CS）\n";
		exit;
                //break ;
        }

	$isnewNumber = true;

} else {
        $isnewNumber = false;
        //echo "Customer with phone $phone exists!!!";
}

/// Verifying crt_object_id from customer_history..........

$resultHis = pg_query("SELECT * FROM customer_history WHERE crt_object_id='$crtObjectId'");
if (!$resultHis) {
        echo "クエリ実行中にエラーが発生しました（CS履歴DB）\n";
        exit;
}

$dataHis = pg_fetch_assoc($resultHis);
if (!$dataHis) {
        //////// Adding New crt_object_id..................

	$insertPGSQLT = "INSERT INTO customer_history (phone1,phoneoriginal,session_id,campaign_id,crt_object_id,userCrtObjectId,customer_id,user_id,queue_id,queue_name,leadid) VALUES (";
        	$insertPGSQLT .=  "'".$phone . "',";
	        $insertPGSQLT .=  "'".$phoneoriginal . "',";
	        $insertPGSQLT .=  "'".$sessionId . "',";
        	$insertPGSQLT .=  "'".$campaignId . "',";
	        $insertPGSQLT .=  "'".$crtObjectId . "',";
        	$insertPGSQLT .=  "'".$userCrtObjectId . "',";
	        $insertPGSQLT .=  "'".$customerId . "',";
		$insertPGSQLT .=  "'".$userId ."',";
	        $insertPGSQLT .=  "'".$queueId . "',";
        	$insertPGSQLT .=  "'".$queueName . "',";
	        $insertPGSQLT .=  "'".$lid . "'";
        	$insertPGSQLT .=  ");";
	$resultt = pg_query($connection,$insertPGSQLT);
	if (!$resultt) {
		if ($enablecrmdebug == "true") {
			endTime($phone, $campaignId, $unique_id, $push_time, $url_time, $client_server_time_diff,$urlTimeFormatted,$requesttodbtime,$sessionId,$userId,$host, $user, $dbname);
		}
		echo "クエリ実行中にエラーが発生しました（CS履歴）\n";
        	exit;
	}

} else {
        $updatePGSQLT = "UPDATE customer_history SET ";
                $updatePGSQLT .= " phone1 = '".$phone."', ";
                $updatePGSQLT .= " phoneoriginal = '".$phoneoriginal."', ";
                $updatePGSQLT .= " session_id = '".$sessionId."', ";
                $updatePGSQLT .= " campaign_id = '".$campaignId."', ";
                $updatePGSQLT .= " userCrtObjectId = '".$userCrtObjectId."', ";
                $updatePGSQLT .= " customer_id = '".$customerId."', ";
                $updatePGSQLT .= " user_id = '".$userId."', ";
                $updatePGSQLT .= " queue_id = '".$queueId."', ";
                $updatePGSQLT .= " queue_name = '".$queueName."', ";
                $updatePGSQLT .= " leadid = '".$lid."' ";

                $updatePGSQLT .= " WHERE crt_object_id='".$crtObjectId."'";

                $resultt = pg_query($connection,$updatePGSQLT);
        if (!$resultt) {
                echo "データ更新中にエラーが発生しました（CS履歴）\n";
                exit;
        }
}

$ameyodbconn= pg_connect("host=$ameyodbhost user=postgres dbname=$ameyodbname");
if (!$ameyodbconn) {
	if ($enablecrmdebug == "true") {
		endTime($phone, $campaignId, $unique_id, $push_time, $url_time, $client_server_time_diff,$urlTimeFormatted,$requesttodbtime,$sessionId,$userId,$host, $user, $dbname);
	}
        echo "AmeyoJ DBに接続できませんでした\n";
        exit;
}


if (!$customerId) {
        $disp_query="SELECT dc.disposition_code_name from disposition_code dc, disposition_plan_definition dpd where dc.id=dpd.disposition_code_id and dpd.disposition_plan_id in (select disposition_plan_id from campaign_disposition_plan where campaign_context_id='$campaignId') and dc.disposition_class_id not in (select id from disposition_class where disposition_class_name in ('campaign.customer.exclusion','process.customer.exclusion')) order by dc.id asc";

$quickdisp_query="SELECT rj.disposition_code_name  from disposition_class lj, (SELECT *  from disposition_code dc, disposition_plan_definition dpd where dc.id=dpd.disposition_code_id and dpd.disposition_plan_id in (select disposition_plan_id from campaign_disposition_plan where campaign_context_id='$campaignId') and dc.disposition_class_id not in (select id from disposition_class where disposition_class_name in ('campaign.customer.exclusion','process.customer.exclusion')) order by dc.disposition_code_name) rj where lj.id=rj.disposition_class_id  and  lj.disposition_class_name='NonContactable' order by lj.disposition_class_name";

} else {

        $disp_query="SELECT dc.disposition_code_name from disposition_code dc, disposition_plan_definition dpd where dc.id=dpd.disposition_code_id and dpd.disposition_plan_id in (select disposition_plan_id from campaign_disposition_plan where campaign_context_id='$campaignId') order by dc.id asc";

$quickdisp_query="SELECT rj.disposition_code_name  from disposition_class lj, (SELECT *  from disposition_code dc, disposition_plan_definition dpd where dc.id=dpd.disposition_code_id and dpd.disposition_plan_id in (select disposition_plan_id from campaign_disposition_plan where campaign_context_id='$campaignId') order by dc.disposition_code_name) rj where lj.id=rj.disposition_class_id  and  lj.disposition_class_name='NonContactable' order by lj.disposition_class_name";

}


$res_disp = pg_query($ameyodbconn,$disp_query);
$res_quickdisp = pg_query($ameyodbconn,$quickdisp_query);
if(!$res_disp){
        echo "<tr><td colspan=2 align='center'>";
        echo "エラー：AmeyoJ DBにクエリ実行できませんでした（Disposition）";
        echo "</td></tr>";
	if ($enablecrmdebug == "true") {
		endTime($phone, $campaignId, $unique_id, $push_time, $url_time, $client_server_time_diff,$urlTimeFormatted,$requesttodbtime,$sessionId,$userId,$host, $user, $dbname);
	}
        exit();
}
if(!$res_quickdisp){
        echo "<tr><td colspan=2 align='center'>";
        echo "エラー：AmeyoJ DBにクエリ実行できませんでした（Disposition）";
        echo "</td></tr>";
	if ($enablecrmdebug == "true") {
		endTime($phone, $campaignId, $unique_id, $push_time, $url_time, $client_server_time_diff,$urlTimeFormatted,$requesttodbtime,$sessionId,$userId,$host, $user, $dbname);
	}
        exit();
}
if ($enablecrmdebug == "true") {
	endTime($phone, $campaignId, $unique_id, $push_time, $url_time, $client_server_time_diff, $urlTimeFormatted,$requesttodbtime,$sessionId,$userId,$host, $user, $dbname);
}
//echo $responsefromdbtime;

?>




<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>AmeyoJ CRM</title>
<!--script to calculate current time and store it in start variable -->
<script language="javascript">
var thisdate=new Date();
var yearWithoutOffset =  parseInt(thisdate.getYear()) + parseInt(1900);
var monthWithoutOffset = parseInt(thisdate.getMonth()) + parseInt(1);
var browserstarttime=yearWithoutOffset+'-'+monthWithoutOffset+'-'+thisdate.getDate()+' '+thisdate.getHours()+':'+thisdate.getMinutes()+':'+thisdate.getSeconds()+'.'+thisdate.getMilliseconds();
//alert(browserstarttime);
function debug()
{
if(document.getElementById('render').style.display=='none') 
{
document.getElementById('render').style.display='block';
} else {
document.getElementById('render').style.display='none';
}

}
</script>

<style type="text/css">
</style>
<link href="css.css" rel="stylesheet" type="text/css" />
         <!-- for calendar date+time -->
        <link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-2.css" title="win2k-cold-1">
        <script type="text/javascript" src="jscalendar/calendar.js"></script>
        <script type="text/javascript" src="jscalendar/lang/calendar-jp.js"></script>
        <script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
        <script src="address_autoinput/ajaxzip2/jquery.js" type="text/javascript"></script>
        <script src="address_autoinput/ajaxzip2/ajaxzip2.js" charset="UTF-8" type="text/javascript"></script>
        <script>AjaxZip2.JSONDATA = 'address_autoinput/ajaxzip2/data';</script>
        
<!--	<script type='text/javascript' src='firebug-lite.js'></script>
<link href="firebug-lite.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript">
-->
<!--  firebug.env.height = 500;
  </script>	 -->	 
<script type="text/javascript" src="jscripts.js"></script>
</head>

<body >

<form  name="main" action=""  method="post">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0"  vertical-align="top">
	<tr>
		<td width="11%" valign="top" >
        	<table border="0"  width="100%" valign="top"  >
                <tr >
                	<td width="140"  height="45" class="ameyocrmlogo"><img src="img/ameyo-crm-logo.gif"/></td>
                </tr>
                <?  while ($rowquickdisp = pg_fetch_row($res_quickdisp))
					{
						$disposition=$rowquickdisp[0];
				?>
				<tr>
					<td  align="center">
						<input  name="<?echo $disposition?>" class="btton" type="Submit" value="<? echo $disposition?>"  onclick="main.action='./dispose.php?phone=<?=$phone?>&sessionId=<?=$sessionId?>&userId=<?=$userId?>&campaignId=<?=$campaignId?>&crtObjectId=<?=$crtObjectId?>&userCrtObjectId=<?=$userCrtObjectId?>&customerId=<?=$customerId?>&queueId=<?=$queueId?>&queueName=<?=$queueName?>&phoneoriginal=<?=$phoneoriginal?>&selfCallback=true&quick=true&dispositionCode=<?=$disposition?>&criteria=<?=$criteria?>'"  onsubmit="return validate();"/>
					</td>
				</tr>
				<?}?>
				<?
				/* 
				{$quickdisp1=$rowquickdisp[0];
					$quickdisp2=$rowquickdisp[1];
					echo "<option value='$quickdisp1'>$quickdisp1</option>";
				}
				*/
                ?>
                <tr>
                	<td align="center" height="52">
                		<table  class="mytable" height="100%" width="100%">
                			<tr>
                				<th background="#d0e7f3">対応者<th>
                			</tr>
                			<tr>
                				<td  align="center">
                					<select  name="dispositionCode" id="dispositionCode" class="selectbox">
                						<? while ($rowdisp = pg_fetch_row($res_disp))
                                                {
                                                        $disp1=$rowdisp[0];
                                                        $disp2=$rowdisp[1];
                                                        echo "<option value='$disp1'>$disp1</option>";
                                                }
                                                ?>
                                    </select>
                                 </td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table  class="mytable" height="100%" width="100%">
<!--                            <tr>
                            	<th background="#d0e7f3">コールバック予約</th>
                            </tr>
                           	<tr>
                                <td  align="center">
                                 <table border="0"  height="100%"  width="100%"  >
                                  <tr height="8">
                                        <td class="texthdlt">
                                                <input type="checkbox" name="selfCallback" id="selfCallback" value=true>&nbsp;セルフコールバック</input>
                                        </td>
                                  </tr>
                                  <tr height="8">
                                        <td   class="texthdlt" >
                                                 <input align="center" type="radio" name="timezone" id="local" value="local" onchange="setattribute(this.value);">&nbsp;日時（ローカル）</input>
        <input align="center" type="radio" name="timezone" id="local" value="local" onchange="setattribute(this.value);">&nbsp;日時（ローカル）</input>
                                        </td>
                                  </tr>
                                  <tr >
                                        <td colspan=2 align="center">
<input name="cblocal" id="cblocal" style="cursor: pointer; background-color:#fdf6f2;" onmouseover="this.style.backgroundColor='#EFF0F7';" onmouseout="this.style.backgroundColor='#ffffff';" onmouseout="this.style.backgroundColor='#ffffff';" readonly="readonly" type="text" value="日時選択" size=22 onchange="checkRadio(0);setattribute('local');">
                <script type="text/javascript"> 
                function updateDate(){
                }
                Calendar.setup({
                        inputField      : "cblocal",            // id of the input field
                        ifFormat        : "%d-%m-%Y %H:%M",             // format of the input field (even if hidden, this format will be honored)
                        showsTime      :     true,     // show time as well as date
                        daFormat        : "%A, %B %d, %Y"               // format of the displayed date
                });
                var d = new Date();
                //document.getElementById("cblocal").value =  "" + d.print("%Y-%m-%d %H:%M")
                /*document.getElementById('cblocal').onchange = function () {
                alert('bingo chips'); }*/
                </script>

                                        </td>
                                  </tr>
                                  <tr height="8">
                                        <td colspan=2 class="texthdlt">
                                                 <input align="center" type="radio" name="timezone" id="customer" value="customer" onchange="setattribute(this.value);">&nbsp;Customer TZ</input>
                                        </td>
                                  </tr>
                                  <tr >
                                        <td  colspan=2 align="center">
                                        <input name="cbcustomer" id="cbcustomer" style="cursor: pointer; background-color:#fdf6f2;" onmouseover="this.style.backgroundColor='#EFF0F7';" onmouseout="this.style.backgroundColor='#ffffff';" onmouseout="this.style.backgroundColor='#ffffff';"  type="text"  readonly="readonly" value="日時選択" size=22  onchange="checkRadio(1);setattribute('customer');" >
                <script type="text/javascript">
                function updateDate(){
                }
                Calendar.setup({
                        inputField      : "cbcustomer",            // id of the input field
                        ifFormat        : "%d-%m-%Y %H:%M",             // format of the input field (even if hidden, this format will be honored)
                        showsTime      :     true,     // show time as well as date
                        daFormat        : "%A, %B %d, %Y"               // format of the displayed date
                });
                var d = new Date();
                //document.getElementById("cbcustomer").value =  "" + d.print("%Y-%m-%d %H:%M")
                </script>

                                        </td>
                                  </tr>

                                  <tr  height="8">
                                        <td colspan=2 class="texthdlt">
                                                 <input align="center" type="radio" id="after" name="timezone" value="after"  checked onchange="setattribute(this.value);"> 日/時/分後にコールバック</input>
                                     </td>
                                        <td   align="left" class="textdark" >After (days/hrs/mins)</input>
                                        </td>
                                  </tr>
                                  <tr >
                                        <td colspan=2 align="center">
                                        <table height="100%" >
                                            <tr height="8">
                                                <td class="texthdlt" align="center">&nbsp;&nbsp;日</td>
                                                <td class="texthdlt" align="center">&nbsp;&nbsp;時</td>
                                                <td class="texthdlt" align="center">&nbsp;&nbsp;分</td>
                                            </tr>
                                            <tr height="8" >
                                                <td  align="left" class="textdark">
                                                        <select name="afterdate" id="date" class="selectbox" onchange="checkRadio(2);setattribute('after');">
                                                       <?php
                                                        for ($i=0 ; $i <= 31; $i++ ) {
                                                               if ($i < 10) {
                                                                        if ($i == '1') {
                                                                                echo "<option value='0$i' selected >0$i</option>";
                                                                        } else {
                                                                                echo "<option value='0$i'>0$i</option>";
                                                                        }
                                                                } else {
                                                                        echo "<option value='$i'>$i</option>";
                                                                }
                                                        } ?>
                                                        </select>
                                                </td>
                                                <td  align="left" class="textdark">
                                                        <select name="afterhour" id="hour" class="selectbox" onchange="checkRadio(2);setattribute('after');">
                                                        <?php

                                                        for ($i=0 ; $i < 12 ; $i++ ) {
                                                                if ($i < 10) {
                                                                        if ($i == '1') {
                                                                        echo "<option value='0$i' selected >0$i</option>";
                                                                        } else {
                                                                                echo "<option value='0$i'>0$i</option>";
                                                                        }
                                                                } else {
                                                                        echo "<option value='$i'>$i</option>";
                                                                }
                                                        }       ?>
                                                        </select>
                                                </td>
                                                <td  align="left" class="textdark">
                                                        <select name="afterminute" id="minute" class="selectbox" onchange="checkRadio(2);setattribute('after');">
                                                        <?php

                                                        for ($i=0 ; $i < 60 ; $i++ ) {
                                                                if ($i < 10) {
                                                                        if ($i == '1') {
                                                                        echo "<option value='0$i' selected >0$i</option>";
                                                                        } else {
                                                                                echo "<option value='0$i'>0$i</option>";
                                                                        }
                                                                } else {
                                                                        echo "<option value='$i'>$i</option>";
                                                                }
                                                        }       ?>
                                                        </select>
                                                

					</td>
                                            </tr>
                                         </table>
                                        </td>
                                  </tr>
				</table>
				</td>
				</tr>
				<tr>	
					<td align="center">備考:
						<textarea size="25%" cols="15" rows="4"  name="comments" ><?php echo $data['comments'];?></textarea>
					</td>
				</tr> 
-->
<tr valign="bottom">
                <td colspan="7" align="center"><br>
                <input class="btton" name="Submit" id="Submit" type="Submit" value="保存" onclick="main.action='./dispose.php?phone=<?=$phone?>&sessionId=<?=$sessionId?>&userId=<?=$userId?>&campaignId=<?=$campaignId?>&crtObjectId=<?=$crtObjectId?>&userCrtObjectId=<?=$userCrtObjectId?>&customerId=<?=$customerId?>&queueId=<?=$queueId?>&queueName=<?=$queueName?>&phoneoriginal=<?=$phoneoriginal?>&selfCallback=true&quick=false&criteria=<?=$criteria?>'"  onsubmit="return validate();"/>
	<!--   <input name="submit" type="submit"  value="Ready for Next Call" class="text"> -->

                </td>
             </tr>
	     <tr>
			<td>
				<br>
			</td>
	     </tr>
                                </table>
                              </td>
                        </tr>
                </table>
        </td>
<td width="89%" valign="top" align="left">


<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" >
                <div align="center"><span class="style3">
                <?php
                //echo  $data['isnewnumber'] ;
                if ($isnewNumber == true) {
                       // echo "Adding New Number $phone";
                }
                ?>
                </span></div>
<!--
 <tr>
    <td height="35">
        <table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#EEEEEE">
       <tr>
        <td width="150" height="20">&nbsp;</td>
        <td width="20" height="15" class="linktexgrey">Phone:&nbsp;</td>
        <td height="15"><a href="#" class="linktexgrey">&nbsp;<?php echo $phone;?></a></td>
        <td height="15" width="250">&nbsp;</td>
        <td width="12" height="15" class="agentimg">&nbsp;</td>
        <td width="8" height="15"><img src="agent.gif"/></td>
        <td width="140" height="15" class="agentname"><strong>&nbsp;&nbsp;<?php echo $userId;?></strong></td>
        <td width="140" class="ameyocrmlogo"><img src="ameyo-crm-logo.gif"/></td>
        <td width="20" height="15"><input name="url_params"  type="hidden"  id="url_params" value="<? echo $url_params;?>"/></td>
      </tr>
     </table>
    </td>
  </tr>

  <tr>
    <td height="2" bgcolor="#d93f1a"></td>
  </tr>
-->

  <tr  valign=bottom>
    <td height="20">
	<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" >
	<tr>
		<td>
			<ul id="tabnav">
				<li  class="tab1"><a style="cursor:pointer;cursor:hand" onclick="showdiv('basic_info');" >基本情報</a></li>
                                <li  class="tab3"><a style="cursor:pointer;cursor:hand" onclick="showdiv('case_info');">履歴情報</a></li>


				<!-- Commented by Sanjay on Barry Request Mail Sub:CRM Page-Case Info -->
			        <!-- <li  class="tab3"><a style="cursor:pointer;cursor:hand" onclick="showdiv('case_info');">ケース</a></li> -->
		        </ul>
		</td>
		<td width="0" height="15" class="linktexgrey"></td>
	        <td height="15"><a  class="linktexgrey"><img src="img/phone.gif"/>&nbsp;<?php echo $phone;?>&nbsp;</a></td>
		<td width="8" height="15"><img src="img/agent.gif"/></td>
		<td width="93" height="15" class="agentname"><strong>&nbsp;&nbsp;<?php echo $userId;?></strong></td>
		<td width="0" height="15"><input name="url_params"  type="hidden"  id="url_params" value="<? echo $url_params;?>"/></td>
	</tr>
	</table>
    </td>
  </tr>
<!--  <tr >
        <td height="1"   bgcolor="#eaa647" ></td>       
  </tr>   -->
   <tr valign=top width="100%" >
    <td style="width:100%; height:100%; ">
<!--        <div  style="width:99.5%; height:99.5%;  border:solid 1px #eaa647; background-color:#fdf7ee;" >        -->
	<div  style="width:99.5%; height:99.5%;  border:solid 1px  #999; background-color:#f9f9f9;" >

        <table width="97%" border="0"   align="center" cellpadding="0" cellspacing="0">
<!--    <div  style="width:100%;  border:solid 0px #eaa647; background-color:#fdf7ee; " > -->
        <tr valign=top width="100%"  height="100%" >
          <td>
                <div style="width:100%;" id="non_edit_per_info">
<table height="100%"  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td colspan="3">&nbsp;</td>
            </tr>

            <tr>
              <td class="heading"><strong>&nbsp;&nbsp;基本情報</strong></td>
              <td>&nbsp;</td>
              <td width="50"><div id="editcont_div"  style="cursor:pointer;cursor:hand" class="linktexblue" onclick="edit_field();"></div></td>
            </tr>
            <tr>
              <td height="1" colspan="3" bgcolor="#CCCCCC"></td>
            </tr>
            <tr>
              <td height="10" colspan="3">&nbsp;</td>
            </tr>
            <tr>

              <td colspan="3"><table class="mytabledata" height="100%"  width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                <tr>
                        <td width="5%" ></td>
                        <td width="16%" class="textlight">日付</td>
                        <td width="2%" >:</td>
                        <td width="27%" class="textdark">
                        <span id="span_display_1"  name="span_group_personal_disp" style="Display:inline;"> <? echo $crmdate;?> </span>
			<input size="15" id="input_display_14" name="crm_date"   type="hidden"  <?php  echo "class=\"textInput\"";?>    value="<?php echo $crmdate;?>">

			</td>
                        <td width="5%" ></td>
                        <td width="16%" class="textlight">曜日</td>
                        <td width="2%" >:</td>
                        <td width="27%"  class="textdark">
	                <span id="span_display_2"  name="span_group_personal_disp"  style="Display:inline;font-size:17px;"> <? echo $crmday;?> </span>
                        <input size="15" id="input_display_14" name="crm_day"   type="hidden"  <?php  echo "class=\"textInput\"";?>    value="<?php echo $crmday;?>">
                        </td>
                </tr>
                <tr>
                        <td width="5%" ></td>
                        <td width="16%" class="textlight">時間</td>
                        <td width="2%" >:</td>
                        <td width="27%"  class="textdark">
                        <span id="span_display_4"  name="span_group_personal_disp" style="Display:inline;font-size:23px;"><? echo $crmtime;?></span>
                        <input size="15" id="input_display_14" name="crm_time"   type="hidden"  <?php  echo "class=\"textInput\"";?>    value="<?php echo $crmtime;?>">
                       </td>
                        <td width="5%" ></td>
                        <td width="16%" class="textlight">電話番号</td>
                        <td width="2%" >:</td>
                        <td width="27%" class="textdark">
                        <span id="span_display_2"  name="span_group_personal_disp"  style="Display:inline"> <? echo $phone;?> </span>
			</td>
                </tr>
                <tr>
                        <td width="5%" ></td>
                        <td width="16%" class="textlight">相談者</td>
                        <td width="2%" >:</td>
                        <td width="27%" class="textdark">
                                <span id="span_display_5" name="span_group_personal_disp" style="Display:none"></span>
                                <span id="span_edit_6" name="span_group_personal_edt" style="Display:inline">
                                <SELECT name="customer_type" style="width: 200px">
 	                                <option value="">- 選択 -</option>
        	                        <option value="本人" <? if($data['customer_type'] =='本人'){?>selected <? } ?>>本人</option>
                                        <option value="家族" <? if($data['customer_type'] =='家族'){?>selected <? } ?>>家族</option>
                                        <option value="親族" <? if($data['customer_type'] =='親族'){?>selected <? } ?>>親族</option>
                                        <option value="知人" <? if($data['customer_type'] =='知人'){?>selected <? } ?>>知人</option>
                                        <option value="相支C" <? if($data['customer_type'] =='相支C'){?>selected <? } ?>>相支C</option>
                                        <option value="不明" <? if($data['customer_type'] =='不明'){?>selected <? } ?>>不明</option>
                                        <option value="非該当" <? if($data['customer_type'] =='非該当'){?>selected <? } ?>>非該当</option>
                                </SELECT>
                         </span>

</td>                                
                        <td width="5%" ></td>
                        <td width="16%" class="textlight">性別</td>
                        <td width="2%" >:</td>
                        <td width="27%"  class="textdark">
                                <span id="span_display_6"  name="span_group_personal_disp" style="Display:none">  </span>
                                <span id="span_edit_6"  name="span_group_personal_edt" style="Display:inline"> 
                                	<SELECT name="gender" style="width: 200px" required/>
										<option value="">- 選択 -</option>
								<option value="男" <? if($data['gender'] =='男'){?>selected <? } ?>>男</option>
								<option value="女" <? if($data['gender'] =='女'){?>selected <? } ?>>女</option>
					</SELECT>
                                </span>
                        </td>
</tr>
<tr>
                        <td width="5%" ></td>
                        <td width="16%" class="textlight">がん種</td>
                        <td width="2%" >:</td>
                        <td width="27%" class="textdark">
                        <span id="span_display_12"  name="span_group_personal_disp"  style="Display:none"></span>
                        <SELECT name="cancer_name" id="cancer_name" style="width: 200px" required/>
                        <option value="" selected>- 選択 -</option>
                        <?php $cancer = pg_query($connectDB,"SELECT cancer_name from cancer_name order by display_id asc");
                        while ($cancer_name = pg_fetch_array($cancer))
	                        {
        	                $name = $cancer_name['cancer_name'];
                	        echo "<option value='$name'";
                        	if ($name==$data['cancer_name']) { echo " selected ";}
	                        echo ">$name</option>";
        	                }
                        ?>
                        </SELECT>
                        </span>
</td>
                        <td width="5%" ></td>
                        <td width="16%" class="textlight">地域（県名）</td>
                        <td width="2%" >:</td>
                        <td width="27%" class="textdark">
                        <span id="span_display_12"  name="span_group_personal_disp"  style="Display:none"></span>
                        <span id="span_edit_12"  name="span_group_personal_edt" style="Display:inline">
                        <SELECT name="prefecture" id="prefecture" style="width: 200px" required/>
        	                <?php $prefecture = pg_query($connectDB,"select prefecture from prefecture order by sorting asc");
                                while ($name = pg_fetch_array($prefecture))
                                {
                	                $name = $name['prefecture'];
                        	        echo "<option value='$name'"; 
					if ($name==$data['prefecture']) { echo " selected ";}
					echo ">$name</option>";
                                } 
					pg_close($connectDB);
                                ?>
                                        </SELECT>
                                </span>

			</span>        
		</td>
                </tr>
              </table></td>
            </tr>
<!--            <tr>
              <td colspan="3">&nbsp;</td>
            </tr>
-->
          </table>


</div>     
                <div style="width:100%;" id="case_info" valign="top" > </div>
                <div  id="account_info" valign="top" style="display:none;width:100%;"> 

                               <table height="100%"  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                          <td colspan="3">&nbsp;</td>
                                    </tr>
                                    <tr>
                                             <td class="heading"><strong>&nbsp;&nbsp;購入履歴</strong></td>
                                             <td>&nbsp;</td>
                                             <td width="50"></td>
                                    </tr>
                                    <tr>
                                            <td height="1" colspan="3" bgcolor="#CCCCCC"></td>
                                    </tr>
                                    <tr>
                                           <td height="10" colspan="3">&nbsp;</td>
                                    </tr>

				    <tr>
                                              <td colspan="3"><table  class="mytabledata" height="100%"  width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                        <td width="5%" >現在利用できるデータがありません</td>
								</tr>
							       </table>
						</td>
				     </tr>
				</table>


		</div>
          </td>
        </tr>
       <tr >
                <td >
                        <div  id="non_edit_address_info" width="100%"  height="100%" style="width:100%;">
				
				<table height="100%"  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
			            <tr>
				          <td colspan="3">&nbsp;</td>
	   			    </tr>
			            <tr>
				             <td width="50"></td>
			            </tr>
			            <tr>
				            <td height="1" colspan="3" bgcolor="#CCCCCC"></td>
			            </tr>
				    <tr>


			        	    <td height="10" colspan="3">&nbsp;</td>
			            </tr>
			            <tr>
         	<td colspan="3"><table  class="mytabledata" height="100%"  width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
			        <td width="5%" ></td>
			        <td width="16%" class="textlight">主訴</td>
			        <td width="2%" >:</td>
					<td width="40%" class="textdark">
                        <span id="span_display_12"  name="span_group_personal_disp"  style="Display:none"></span>
                        <span id="span_edit_12"  name="span_group_personal_edt" style="Display:inline">
                        <textarea size="50%" cols="80" rows="5"  name="memo1" required/><?php echo $data['memo1'];?></textarea>
			 </span>         </td>
<td colspan="8" align="center" class="textdark">
</td>
</tr>
<tr>
                                                            <td width="5%" ></td>
                                                            <td width="16%" class="textlight">対応</td>
                                                            <td width="2%" >:</td>
                                                            <td width="40%" class="textdark">
                        <span id="span_display_12"  name="span_group_personal_disp"  style="Display:none"></span>
                        <span id="span_edit_12"  name="span_group_personal_edt" style="Display:inline">
                        <textarea size="50%" cols="80" rows="5"  name="memo2" required/><?php echo $data['memo2'];?></textarea>
                                </span>  </td>
<td colspan="8" align="center" class="textdark">
</td>

</tr>
                                                        <tr>
                                                            <td width="5%" ></td>
                                                            <td width="16%" class="textlight">備考、申し送り</td>
                                                            <td width="2%" >:</td>
                                                            <td width="40%" class="textdark">
                        <span id="span_display_12"  name="span_group_personal_disp"  style="Display:none"></span>
                        <span id="span_edit_12"  name="span_group_personal_edt" style="Display:inline">
                        <textarea size="50%" cols="80" rows="5"  name="memo3"><?php echo $data['memo3'];?></textarea>
                                </span>  </td>
<td colspan="8" align="center" class="textdark">
</td>
</tr>

                                                        <tr>
                                                            <td width="5%" ></td>
                                                            <td width="16%" class="textlight">相談者の反応</td>
                                                            <td width="2%" >:</td>
                                                            <td width="40%" class="textdark">
                        <span id="span_display_12"  name="span_group_personal_disp"  style="Display:none"></span>
                        <span id="span_edit_12"  name="span_group_personal_edt" style="Display:inline">
                        <textarea size="50%" cols="80" rows="5"  name="reaction" required/><?php echo $data['reaction'];?></textarea>
                                </span>  </td>
<td colspan="8" align="center" class="textdark">
</td>
</tr>


<tr>
					<td width="5%" ></td>
					<td width="16%" class="textlight">相談内容１</td>
			                <td width="2%" >:</td>
			                <td width="27%" class="textdark">
                                       <span id="span_edit_11"  name="span_group_personal_disp"  style="Display:inline">
                                       <SELECT name="concern1" id="concern1" style="width: 200px" required/>
						<option value="" selected>- 選択 -</option>
                                               <?php $concern = pg_query($connectDB,"select name from concern order by id asc");
						while ($name = pg_fetch_array($concern))
						{ 
						$name = $name['name'];
	                                        echo "<option value='$name'";
	                                        if ($name==$data['concern1']) { echo " selected ";}
	                                        echo ">$name</option>";
						} 
						?>
                                        </SELECT>
<span id="span_edit_11"  name="span_group_personal_disp"  style="Display:inline">
<select name="sub_concern1" id="sub_concern1"> </select>
                                </span>
						</td>

</tr>

<tr>
                                        <td width="5%" ></td>
                                        <td width="16%" class="textlight">相談内容２</td>
                                        <td width="2%" >:</td>
                                        <td width="27%" class="textdark">
                                       <span id="span_edit_11"  name="span_group_personal_disp"  style="Display:inline">
                                       <SELECT name="concern2" id="concern2" style="width: 200px">
                                                <option value="" selected>- 選択 -</option>
                                               <?php $concern = pg_query($connectDB,"select name from concern order by id asc");
						while ($name2 = pg_fetch_array($concern))
                                                {
                                                $name2 = $name2['name'];
	                                        echo "<option value='$name2'";
	                                        if ($name2==$data['concern2']) { echo " selected ";}
	                                        echo ">$name2</option>";
                                                } pg_close($connectDB);
						?>
                                        </SELECT>
                                </span>
<span id="span_edit_11"  name="span_group_personal_disp"  style="Display:inline">
<select name="sub_concern2" id="sub_concern2"> </select>

                                                </td>
</tr>

		              </table></td>
            			</tr>
<!--            			<tr>
             				 <td colspan="3">&nbsp;</td>
            			</tr>
-->
          		  </table>
			
			</div>
                        <div style="width:100%; "id="edit_contact_info"></div>
                </td>
        </tr>

       <tr >
                <td >
                        <div style="width:100%;" id="non_edit_contact_info" width="100%"  height="100%">

                                <table height="100%"  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                          <td colspan="3">&nbsp;</td>
                                    </tr>
                                    <tr>
                                    </tr>
                                    <tr>
                                            <td height="1" colspan="3" bgcolor="#CCCCCC"></td>
                                    </tr>
                                    <tr>


                                            <td height="25" colspan="3">&nbsp;</td>
                                    </tr>
                                    <tr>
<td colspan="3"><table  class="mytabledata" height="100%"  width="100%" border="0" cellspacing="0" cellpadding="0">
         <tr>
              <td width="5%" ></td>
              <td width="16%" class="textlight">主な対応</td>
              <td width="2%" >:</td>
              <td width="27%" class="textdark">
              <span id="span_display_13"  name="span_group_personal_disp"  style="Display:none"></span>
              <span id="span_edit_13"  name="span_group_personal_edt" style="Display:inline">
	      <select name="action1" id="action1" style="width: 180px" required/>
              <option value="" selected >--</option>
                <option value="情報提供" <? if($data['action1'] =='情報提供'){?>selected <? } ?>>情報提供</option>
                <option value="助言・提案" <? if($data['action1'] =='助言・提案'){?>selected <? } ?>>助言・提案</option>
                <option value="傾聴・語りの促進" <? if($data['action1'] =='傾聴・語りの促進'){?>selected <? } ?>>傾聴・語りの促進</option>
                </select>
			</span>
			</td>
                     	<td width="5%" ></td>
                        <td width="16%" class="textlight">その他の対応</td>
                        <td width="2%" >:</td>
                        <td width="27%"  class="textdark">
                        <span id="span_display_14"  name="span_group_personal_disp" style="Display:none"></span>
                        <span id="span_edit_14" name="span_group_personal_edt"  style="Display:inline">
                        <SELECT name="action2" id="action2" style="width: 180px;">
                        <option value="" selected >--</option>
                <option value="情報提供" <? if($data['action2'] =='情報提供'){?>selected <? } ?>>情報提供</option>
                <option value="助言・提案" <? if($data['action2'] =='助言・提案'){?>selected <? } ?>>助言・提案</option>
                <option value="傾聴・語りの促進" <? if($data['action2'] =='傾聴・語りの促進'){?>selected <? } ?>>傾聴・語りの促進</option>
</span>
                                        </SELECT>
</td>
</tr>
<tr>
                        <td width="5%" ></td>
                        <td width="16%" class="textlight">相支C紹介有無</td>
                        <td width="2%" >:</td>
                        <td width="27%"  class="textdark">
                        <span id="span_display_14"  name="span_group_personal_disp" style="Display:none"></span>
                        <span id="span_edit_14" name="span_group_personal_edt"  style="Display:inline">
                                        <SELECT name="is_introduced" id="is_introduced" style="width: 180px;" required/>
                                                <option value="" selected >--</option>
                                                <option value="あり" <? if($data['is_introduced'] =='あり'){?>selected <? } ?>>あり</option>
                                                <option value="なし" <? if($data['is_introduced'] =='なし'){?>selected <? } ?>>なし</option>
                                        </SELECT> </span>
</td>

                        <td width="5%" ></td>
                        <td width="16%" class="textlight">検索システム利用</td>
                        <td width="2%" >:</td>
                        <td width="27%"  class="textdark">
                        <span id="span_display_14"  name="span_group_personal_disp" style="Display:none"></span>
                        <span id="span_edit_14" name="span_group_personal_edt"  style="Display:inline">
                                        <SELECT name="search_available" id="search_available" style="width: 180px;" required/>
                                                <option value="" selected >--</option>
                                                <option value="あり" <? if($data['search_available'] =='あり'){?>selected <? } ?>>あり</option>
                                                <option value="なし" <? if($data['search_available'] =='なし'){?>selected <? } ?>>なし</option>
                                        </SELECT> </span>
</td>
</tr>
<tr>
                        <td width="5%" ></td>
                        <td width="16%" class="textlight">電話番号入手経路</td>
                        <td width="2%" >:</td>
                        <td width="27%"  class="textdark">
                        <span id="span_display_14"  name="span_group_personal_disp" style="Display:none"></span>
                        <span id="span_edit_14" name="span_group_personal_edt"  style="Display:inline">
                                        <SELECT name="route" id="route" style="width: 180px;" required/>
                                                <option value="" selected >--</option>
                                                <option value="インターネット" <? if($data['route'] =='インターネット'){?>selected <? } ?>>インターネット</option>
                   <option value="がん情報サービスの出版物" <? if($data['route'] =='がん情報サービスの出版物'){?>selected <? } ?>>がん情報サービスの出版物</option>
                                                <option value="新聞・出版・放送" <? if($data['route'] =='新聞・出版・放送'){?>selected <? } ?>>新聞・出版・放送</option>
                                                <option value="複数回の方" <? if($data['route'] =='複数回の方'){?>selected <? } ?>>複数回の方</option>
                                                <option value="聞きそびれ" <? if($data['route'] =='聞きそびれ'){?>selected <? } ?>>聞きそびれ</option>
                                                <option value="その他" <? if($data['route'] =='その他'){?>selected <? } ?>>その他</option>
                                        </SELECT>
</span>
                        <td width="5%" ></td>
                        <td width="16%" class="textlight">利用回数</td>
                        <td width="2%" >:</td>
                        <td width="27%"  class="textdark">
                        <span id="span_display_14"  name="span_group_personal_disp" style="Display:none"></span>
                        <span id="span_edit_14" name="span_group_personal_edt"  style="Display:inline">
                                        <SELECT name="count_calling" id="count_calling" style="width: 180px;" required/>
                                                <option value="" selected >--</option>
                                                <option value="初回" <? if($data['count_calling'] =='初回'){?>selected <? } ?>>初回</option>
                                                <option value="複数回" <? if($data['count_calling'] =='複数回'){?>selected <? } ?>>>複数回</option>
                                                <option value="不明" <? if($data['count_calling'] =='不明'){?>selected <? } ?>>>不明</option>
                                        </SELECT>
</span>
</td>
</tr>

<tr>
                        <td width="5%" ></td>
                        <td width="16%" class="textlight">備考</td>
                        <td width="2%" >:</td>
                        <td width="27%" class="textdark">
                        <span id="span_display_12"  name="span_group_personal_disp"  style="Display:none"></span>
                        <span id="span_edit_12"  name="span_group_personal_edt" style="Display:inline">
                        <textarea size="50%" cols="30" rows="7" name="comments"><?php echo $data['comments'];?></textarea>
                                </span>  </td>

                        <td width="5%" ></td>
                        <td width="16%" class="textlight">研究利用</td>
                        <td width="2%" >:</td>
                        <td width="27%" class="textdark">
                                <span id="span_display_5" name="span_group_personal_disp" style="Display:none"></span>
                                <span id="span_edit_6" name="span_group_personal_edt" style="Display:inline">
                                <SELECT name="availibility" id="availibility" style="width: 180px">
                                        <option value="可" <?php if($data['availibility'] =='可'){?>selected <? } ?>>可</option>
                                        <option value="不可" <?php if($data['availibility'] =='不可'){?>selected <? } ?>>不可</option>
                                </SELECT>
                         </span>
</td>

</tr>
                          </table>

                        </div>
	</tr>
<!--
        <tr>
                <td>&nbsp;</td>
        </tr>
-->
<!--    </div> -->
        </table>
        </div>
     </td>
   </tr>
   <tr>
                <td ><input type="hidden" name="isnewNumber" id="isnewNumber" value="<? echo $isnewNumber?>" /><input  type="hidden"  name="dispositionAttr" id="dispositionAttr" value="after-01-00-0000 01:01:00" /></td>
		<td  nowrap>
                        <iframe id="render" src="#" height="60px" width="400px" style="display    :none"> </iframe>
                        <!--<input type="checkbox" target="_blank" onclick="return debug();">Debug -->
       </td>
 </tr>
</table>
</td>
</tr>
<!--
<tr>
        <td>&nbsp;
	</td>
        <td height="0" bgcolor="#ffffff"></td>
</tr>
-->
</table>


<script language="javascript">
var thisdate=new Date();
//alert(thisdate.getMonth());
var yearWithoutOffset =  parseInt(thisdate.getYear()) + parseInt(1900);
//alert(yearWithoutOffset);
var monthWithoutOffset = parseInt(thisdate.getMonth()) + parseInt(1);
var browserendtime=yearWithoutOffset+'-'+monthWithoutOffset+'-'+thisdate.getDate()+' '+thisdate.getHours()+':'+thisdate.getMinutes()+':'+thisdate.getSeconds()+'.'+thisdate.getMilliseconds();
//alert(browserendtime);
//alert(browserendtime);
</script>
<script src="//code.jquery.com/jquery-2.1.3.min.js"></script>
<script>

    $('#concern1').change(function(){
	if($(this).val() == 'がんの治療')
{
         $('#sub_concern1').empty();
        var text1='手術';
        var text2='化学療法（抗がん剤）';
        var text3='放射線療法';
        var text4='免疫療法';
        var text5='臨床試験 先進医療';
	var text6='その他治療に関連するもの';
	var text7='';
	$('<option>').val(text7).text(text7).appendTo('#sub_concern1');
	$('<option>').val(text1).text(text1).appendTo('#sub_concern1');
	$('<option>').val(text2).text(text2).appendTo('#sub_concern1'); 
	$('<option>').val(text3).text(text3).appendTo('#sub_concern1'); 
	$('<option>').val(text4).text(text4).appendTo('#sub_concern1');
	$('<option>').val(text5).text(text5).appendTo('#sub_concern1');
	$('<option>').val(text6).text(text6).appendTo('#sub_concern1');
}
else if($(this).val() == 'サポセン独自')
{
         $('#sub_concern1').empty();
        var text1='がん相談支援センター連絡先';
        var text2='患者必携入手方法';
        var text3='NCC中央病院関連';
        var text4='がん情報サービスweb関連';
        var text5='その他';
	var text7='';
        $('<option>').val(text7).text(text7).appendTo('#sub_concern1');
        $('<option>').val(text1).text(text1).appendTo('#sub_concern1');
        $('<option>').val(text2).text(text2).appendTo('#sub_concern1');
        $('<option>').val(text3).text(text3).appendTo('#sub_concern1');
        $('<option>').val(text4).text(text4).appendTo('#sub_concern1');
        $('<option>').val(text5).text(text5).appendTo('#sub_concern1');
}
else {
	$('#sub_concern1').empty();
	}
});

</script>

<script>
    $('#concern2').change(function(){
        if($(this).val() == 'がんの治療')
{
         $('#sub_concern2').empty();
        var text1='手術';
        var text2='化学療法（抗がん剤）';
        var text3='放射線療法';
        var text4='免疫療法';
        var text5='臨床試験 先進医療';
        var text6='その他治療に関連するもの';
        var text7='';
        $('<option>').val(text7).text(text7).appendTo('#sub_concern2');
        $('<option>').val(text1).text(text1).appendTo('#sub_concern2');
        $('<option>').val(text2).text(text2).appendTo('#sub_concern2');
        $('<option>').val(text3).text(text3).appendTo('#sub_concern2');
        $('<option>').val(text4).text(text4).appendTo('#sub_concern2');
        $('<option>').val(text5).text(text5).appendTo('#sub_concern2');
        $('<option>').val(text6).text(text6).appendTo('#sub_concern2');
}
else if($(this).val() == 'サポセン独自')
{
         $('#sub_concern2').empty();
        var text1='がん相談支援センター連絡先';
        var text2='患者必携入手方法';
        var text3='NCC中央病院関連';
        var text4='がん情報サービスweb関連';
        var text5='その他';
        var text7='';
        $('<option>').val(text7).text(text7).appendTo('#sub_concern2');
        $('<option>').val(text1).text(text1).appendTo('#sub_concern2');
        $('<option>').val(text2).text(text2).appendTo('#sub_concern2');
        $('<option>').val(text3).text(text3).appendTo('#sub_concern2');
        $('<option>').val(text4).text(text4).appendTo('#sub_concern2');
        $('<option>').val(text5).text(text5).appendTo('#sub_concern2');
}
else {
        $('#sub_concern2').empty();
        }
});
</script>



<script language=javascript>
<?
if ($enablecrmdebug == "true") { 
	echo "document.getElementById('render').src='render.php?queryendtime=$responsefromdbtime&unique_id=$unique_id&campaignId=$campaignId&phone=$phone&browserstarttime='+ browserstarttime +'&browserendtime=' + browserendtime +'&responsefromdbtime=$responsefromdbtime&requesttodbtime=$requesttodbtime&crmpopuptime=$urlTimeFormatted&timedifference=$client_server_time_diff';"; 
}
?> 

</script>
</form>

<?php
	$pagePerformance->dbResource['dbtype'] = 'postgres'; 
        $pagePerformance->dbResource['resource'] = $connection;
        $pagePerformance->debugEnd();
	pg_close($connection); 
?>
<?php  pg_close($ameyodbconn); ?>

</body>

</html>

