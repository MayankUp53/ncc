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

$dropdownSql= "select distinct dd_value, id from custom_applicable_dropdowns where is_deleted='0' order by id asc";
$res_dropdown = pg_query($connection,$dropdownSql);
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

//echo "<pre>";print_r($data);
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
<script>
/*function validate()
    {
    if (customer_type.value == isNaN)
    {
    return false;
    }
    if (b.value == isNaN)
    {
    return false;
    }
    if (c.value == isNaN)
    {
    return false;
    }
    return true;
    }*/
</script>
<style type="text/css">
.mandatory{
color: red;
font-size:12px;
}
</style>
<link href="css.css" rel="stylesheet" type="text/css" />
<!-- for calendar date+time -->
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-2.css" title="win2k-cold-1">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-jp.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<!--	<script type='text/javascript' src='firebug-lite.js'></script>
<link href="firebug-lite.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript">
-->
<!--  firebug.env.height = 500;
  </script>	 -->	 
<script type="text/javascript" src="jscripts.js"></script>

</head>

<body >

<form name="main" action=""  method="post">

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
						<input  name="<?echo $disposition?>" class="btton" type="Submit" value="<? echo $disposition?>"  onclick="main.action='./dispose_preview.php?phone=<?=$phone?>&sessionId=<?=$sessionId?>&userId=<?=$userId?>&campaignId=<?=$campaignId?>&crtObjectId=<?=$crtObjectId?>&userCrtObjectId=<?=$userCrtObjectId?>&customerId=<?=$customerId?>&queueId=<?=$queueId?>&queueName=<?=$queueName?>&phoneoriginal=<?=$phoneoriginal?>&selfCallback=true&quick=true&dispositionCode=<?=$disposition?>&criteria=<?=$criteria?>'"  onsubmit="return validate();"/>
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
                					<select  required name="dispositionCode" id="dispositionCode" class="selectbox">
								<option value=''>- 選択 -</option>
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
                <input class="btton" name="Submit" id="Submit" type="Submit" value="保存" onclick="main.action='./dispose_preview.php?phone=<?=$phone?>&sessionId=<?=$sessionId?>&userId=<?=$userId?>&campaignId=<?=$campaignId?>&crtObjectId=<?=$crtObjectId?>&userCrtObjectId=<?=$userCrtObjectId?>&customerId=<?=$customerId?>&queueId=<?=$queueId?>&queueName=<?=$queueName?>&phoneoriginal=<?=$phoneoriginal?>&selfCallback=true&quick=false&criteria=<?=$criteria?>'"  onsubmit="return validate();"/>
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
                        <td width="30%" class="textdark">
                        <span id="span_display_1"  name="span_group_personal_disp" style="Display:inline;"> <? echo $crmdate;?> </span>
			<input size="15" id="input_display_14" name="crm_date"   type="hidden"  <?php  echo "class=\"textInput\"";?>    value="<?php echo $crmdate;?>">

			</td>
                        <td width="2%" ></td>
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
                        <td width="30%"  class="textdark">
                        <span id="span_display_4"  name="span_group_personal_disp" style="Display:inline;font-size:23px;"><? echo $crmtime;?></span>
                        <input size="15" id="input_display_14" name="crm_time"   type="hidden"  <?php  echo "class=\"textInput\"";?>    value="<?php echo $crmtime;?>">
                       </td>
                        <td width="2%" ></td>
                        <td width="16%" class="textlight">電話番号</td>
                        <td width="2%" >:</td>
                        <td width="27%" class="textdark">
                        <span id="span_display_2"  name="span_group_personal_disp"  style="Display:inline"> <? echo $phone;?> </span>
			</td>
                </tr>
                <tr>
                        <td width="5%" ></td>
                        <td width="16%" class="textlight">相談者 <span class="mandatory">*</span></td>
                        <td width="2%" >:</td>
                        <td width="30%" class="textdark">
                                <span id="span_display_5" name="span_group_personal_disp" style="Display:none"></span>
                                <span id="span_edit_5" name="span_group_personal_edt" style="Display:inline">
                                <SELECT id="customer_type"	 name="customer_type" style="width: 200px" required/>
                                
					<option value="">- 選択 -</option>
					<option value="本人" <? if($data['customer_type'] =='本人'){?>selected <? } ?>>本人</option>
					<option value="家族・親戚" <? if($data['customer_type'] =='家族・親戚'){?>selected <? } ?>>家族・親戚</option>
					<option value="友人・知人" <? if($data['customer_type'] =='友人・知人'){?>selected <? } ?>>友人・知人</option>
					<option value="相談支援C" <? if($data['customer_type'] =='相談支援C'){?>selected <? } ?>>相談支援C</option>
					<option value="その他" <? if($data['customer_type'] =='その他'){?>selected <? } ?>>その他</option>
					<option value="不明" <? if($data['customer_type'] =='不明'){?>selected <? } ?>>不明</option>
				</SELECT>
				<input type="hidden" value="<?php echo $data['other_customer_type']; ?>"  id="other_customer_type" name="other_customer_type" />
                         </span>
						</td>                                
                        <td width="2%" ></td>
                        <td width="16%" class="textlight">相談者の性別<span class="mandatory">*</span></td>
                        <td width="2%" >:</td>
                        <td width="27%"  class="textdark">
                                <span id="span_display_6"  name="span_group_personal_disp" style="Display:none">  </span>
                                <span id="span_edit_6"  name="span_group_personal_edt" style="Display:inline"> 
                                	<SELECT name="gender" style="width: 200px" required/>
										<option value="">- 選択 -</option>
								<option value="男" <? if($data['gender'] =='男'){?>selected <? } ?>>男</option>
								<option value="女" <? if($data['gender'] =='女'){?>selected <? } ?>>女</option>
								<option value="不明" <? if($data['gender'] =='不明'){?>selected <? } ?>>不明</option>
					</SELECT>
                                </span>
                        </td>
</tr>
<tr>
                        <td width="5%" ></td>
                        <td width="16%" class="textlight">がん種<span class="mandatory">*</span></td>
                        <td width="2%" >:</td>
                        <td width="30%" class="textdark">
                        <span id="span_display_12"  name="span_group_personal_disp"  style="Display:none"></span>
                        <SELECT name="cancer_name" id="cancer_name" style="width: 200px" required/>
                        <option value="" selected>- 選択 -</option>
                        <?php $cancer = pg_query($connectDB,"SELECT cancer_name from cancer_name_new order by display_id asc");
                        while ($cancer_name = pg_fetch_array($cancer))
	                        {
        	                $name = $cancer_name['cancer_name'];
                	        echo "<option value='$name'";
                        	if ($name==$data['cancer_name']) { echo " selected ";}
	                        echo ">$name</option>";
        	                }
                        ?>
                        </SELECT>
                        <input type="hidden" value="<?php echo $data['other_cancer_name'] ?>" id="other_cancer_name" name="other_cancer_name" />
			</span>
			</td>
                        <td width="2%" ></td>
                        <td width="16%" class="textlight">地域（県名）</td>
                        <td width="2%" >:</td>
                        <td width="30%" class="textdark">
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
		 <tr>
              <td width="2%" ></td>
                        <td width="16%" class="textlight">患者の年齢</td>
                        <td width="2%" >:</td>
                        <td width="27%" class="textdark">
                        <span id="span_display_12_1"  name="span_group_personal_disp"  style="Display:none"></span>
                        <span id="span_edit_12_1"  name="span_group_personal_edit" style="Display:inline">
                       <SELECT name="parents_age" id="parents_age" style="width: 200px" />
										
								<option value="不明" <? if($data['parents_age'] =='不明'){?>selected <? } ?>>不明</option>
								<option value="非該当" <? if($data['parents_age'] =='非該当'){?>selected <? } ?>>非該当</option>
								<option value="15才未満（小児）" <? if($data['parents_age'] =='15才未満（小児）'){?>selected <? } ?>>15才未満（小児）</option>
								<option value="15-19歳" <? if($data['parents_age'] =='15-19歳'){?>selected <? } ?>>15～19歳</option>
								<option value="20-29歳" <? if($data['parents_age'] =='20-29歳'){?>selected <? } ?>>20～29歳</option>
								<option value="30-39歳" <? if($data['parents_age'] =='30-39歳'){?>selected <? } ?>>30～39歳</option>
								<option value="40-49歳" <? if($data['parents_age'] =='40-49歳'){?>selected <? } ?>>40～49歳</option>
								<option value="50-59歳" <? if($data['parents_age'] =='50-59歳'){?>selected <? } ?>>50～59歳</option>
								<option value="60-69歳" <? if($data['parents_age'] =='60-69歳'){?>selected <? } ?>>60～69歳</option>
								<option value="70-79歳" <? if($data['parents_age'] =='70-79歳'){?>selected <? } ?>>70～79歳</option>
								<option value="80-89歳" <? if($data['parents_age'] =='80-89歳'){?>selected <? } ?>>80～89歳</option>
								<option value="90歳以上" <? if($data['parents_age'] =='90歳以上'){?>selected <? } ?>>90歳以上</option>
					</SELECT> 
                        <!-- <input  name="parents_age" id="parents_age" value=<?php if ($data['parents_age']) echo $data['parents_age']; else echo "不明" ;?> /></input> -->
                        </span>

                        </span>
                </td>

            </tr>

                </tr>
              </table></td>
            </tr>
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
			        <td width="16%" class="textlight">主訴と対応<span class="mandatory">*</span></td>
			        <td width="2%" >:</td>
					<td width="40%" class="textdark">
                        <span id="span_display_12"  name="span_group_personal_disp"  style="Display:none"></span>
                        <span id="span_edit_12"  name="span_group_personal_edt" style="Display:inline">
                        <textarea size="50%" cols="80" rows="5"  name="memo1" required/><?php echo $data['memo1'];?></textarea>
			 </span>         </td>
<td colspan="8" align="center" class="textdark">
</td>
</tr>
<!--tr>
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
                        <textarea size="50%" cols="80" rows="5"  name="reaction" /><?php echo $data['reaction'];?></textarea>
                                </span>  </td>
<td colspan="8" align="center" class="textdark">
</td>
</tr -->


<tr>
					<td width="5%" ></td>
					<td width="16%" class="textlight">相談内容１<span class="mandatory">*</span></td>
			                <td width="2%" >:</td>
			                <td width="27%" class="textdark">
                                       <span id="span_edit_11"  name="span_group_personal_disp"  style="Display:inline">
                                       <SELECT name="concern1" id="concern1" style="width: 200px" required/>
						<option value="" selected>- 選択 -</option>
                                               <?php $concern = pg_query($connectDB,"select name from new_concern_list order by id asc");
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
<select name="sub_concern1" id="sub_concern1"/> 
	<option value="<?php echo $data['sub_concern1'];?>"><?php echo $data['sub_concern1'];?></option>
</select>
                                </span>
						</td>

</tr>

<tr>
                                        <td width="5%" ></td>
                                        <td width="16%" class="textlight">相談内容２ </td>
                                        <td width="2%" >:</td>
                                        <td width="27%" class="textdark">
                                       <span id="span_edit_11"  name="span_group_personal_disp"  style="Display:inline">
                                       <SELECT name="concern2"  id="concern2" style="width: 200px">
                                                <option value="" selected>- 選択 -</option>
                                               <?php $concern = pg_query($connectDB,"select name from new_concern_list order by id asc");
						while ($name2 = pg_fetch_array($concern))
                                                {
                                                $name2 = $name2['name'];
	                                        echo "<option value='$name2'";
	                                        if ($name2==$data['concern2']) { echo " selected ";}
	                                        echo ">$name2</option>";
                                                } 
						?>
                                        </SELECT>
                                </span>
<span id="span_edit_11"  name="span_group_personal_disp"  style="Display:inline">
<select name="sub_concern2" id="sub_concern2"> 
	<option value="<?php echo $data['sub_concern2'];?>"><?php echo $data['sub_concern2'];?></option>
</select>

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
                        <td width="16%" class="textlight">現在の治療状況<span class="mandatory">*</span></td>
                        <td width="2%" >:</td>
                        <td width="27%"  class="textdark">
                        <span id="span_display_14"  name="span_group_personal_disp" style="Display:none"></span>
                        <span id="span_edit_14" name="span_group_personal_edt"  style="Display:inline">
                                        <SELECT name="treatment_status" id="treatment_status" style="width: 180px;" required/>
                                                <option value="" selected >- -</option>
                                                <option value="不明" <? if($data['treatment_status'] =='不明'){?>selected <? } ?>>不明</option>
                                                <option value="治療前" <? if($data['treatment_status'] =='治療前'){?>selected <? } ?>>治療前</option>
                                                <option value="治療中" <? if($data['treatment_status'] =='治療中'){?>selected <? } ?>>治療中</option>
                                                <option value="経過観察中・治療後" <? if($data['treatment_status'] =='経過観察中・治療後'){?>selected <? } ?>>経過観察中・治療後
</option>
                                                <option value="緩和ケアのみ" <? if($data['treatment_status'] =='緩和ケアのみ'){?>selected <? } ?>>緩和ケアのみ</option>
                                                <option value="死亡" <? if($data['treatment_status'] =='死亡'){?>selected <? } ?>>死亡</option>
                                                <option value="非該当" <? if($data['treatment_status'] =='非該当'){?>selected <? } ?>>非該当</option>
                                        </SELECT> </span>
</td>

</tr>

         <tr>
              <td width="5%" ></td>
              <td width="16%" class="textlight">主な対応<span class="mandatory">*</span></td>
              <td width="2%" >:</td>
              <td width="70%" class="textdark">
              <span id="span_display_13"  name="span_group_personal_disp"  style="Display:none"></span>
              <span id="span_edit_13"  name="span_group_personal_edt" style="Display:inline">
	      <select name="action1" id="action1" style="width: 180px" required/>
              <option value="" selected >--</option>
		<?php $ressql = pg_query($connectDB,"select dd_value from  custom_mainresponse_dropdowns where is_deleted='0' order by id asc");
                                                while ($valres = pg_fetch_array($ressql))
                                                {
                                                echo "<option value='$valres[0]'";
                                                if ($valres[0]==$data['action1']) { echo " selected ";}
                                                echo ">$valres[0]</option>";
                                                } 
                                                ?>
	
                </select>
			</span>
<!--		<span id="span_edit_111"  name="span_group_personal_disp"  style="Display:inline">
			<select name="sub_action1" id="sub_action1">
		        	<option value="<?php echo $data['sub_action1'];?>"><?php echo $data['sub_action1'];?></option>
			</select>

		<span> -->
		<input type="text" name="newaction" id="newaction"> <input type="button" onclick="addOption1()" value="アイテムを追加" /> <input type="button" onclick="removeOption1()" value="アイテムを削除" />
  		</span>

		<div id="display-msg1">  </div>
			</td>
                     	<td width="5%" >  </td>
                        <td width="14%"> </td>
                        <td width="2%" ></td>
                        <td width="37%"  class="textdark">
</td>
</tr>
<tr>
                        <td width="5%" ></td>
                        <td width="16%" class="textlight">該当するもの</td>
                        <td width="2%" >:</td>
                        <td width="27%"  class="textdark">
                        <span id="span_display_24"  name="span_group_personal_disp" style="Display:none"></span>
                        <span id="span_edit_24" name="span_group_personal_edt"  style="Display:inline">
                                        <SELECT name="applicable" id="applicable" style="width: 180px;" />
						<option value="" selected >--</option>
                 				<?
						 while ($rowdd = pg_fetch_row($res_dropdown))
                                                {
                                                        $dd=$rowdd[0];
							echo "<option value='$dd'";
                                                	if ($dd==$data['applicable']) { echo " selected ";}
                                                echo ">$dd</option>";
                                                } 
                                                ?>
					 </SELECT>

                        </span>
			<span>
                		<input type="text" name="newapplicable" id="newapplicable"> <input type="button" onclick="addOption()" value="アイテムを追加" /> <input type="button" onclick="removeOption()" value="アイテムを削除" />
                	</span>
                	<div id="display-msg">  </div>
			</td>

</tr>

         <tr>
              <td width="5%" ></td>
              <td width="16%" class="textlight">該当するもの2</td>
              <td width="2%" >:</td>
              <td width="70%" class="textdark">
              <span id="span_display_132"  name="span_group_personal_disp2"  style="Display:none"></span>
              <span id="span_edit_132"  name="span_group_personal_edt2" style="Display:inline">
              <select name="action2" id="action2" style="width: 180px" />
              <option value="" selected >--</option>
                <?php 
			$dropdownSql1= "select distinct dd_value, id from custom_applicable_dropdowns where is_deleted='0' order by id asc";
			$res_dropdown1 = pg_query($connection,$dropdownSql1);

			//$ressql = pg_query($connectDB,"select dd_value from  custom_mainresponse_dropdowns where is_deleted='0' order by id asc");
                                                while ($valres = pg_fetch_array($res_dropdown1))
                                                {
                                                echo "<option value='$valres[0]'";
                                                if ($valres[0]==$data['action2']) { echo " selected ";}
                                                echo ">$valres[0]</option>";
                                                } pg_close($connectDB);
                                                ?>

                </select>
                        </span>
                <input type="text" name="new2" id="newapplicable2"> <input type="button" onclick="addOption2()" value="アイテムを追加" /> <input type="button" onclick="removeOption2()" value="アイテムを削除" />
                </span>
                <div id="display-msg3">  </div>
                        </td>
                        <td width="5%" >  </td>
                        <td width="14%"> </td>
                        <td width="2%" ></td>
                        <td width="37%"  class="textdark">
</td>
</tr>


                         </table>
</td>
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
<!--
<script src="//code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js"></script>
-->
<script src="jquery-2.2.1.min.js"></script>
<script src="jquery.validate.min.js"></script>
<script>
/*String.prototype.toHalfWidth = function() {
    return this.replace(/[Ａ-Ｚａ-ｚ０-９]/g, function(s) {return String.fromCharCode(s.charCodeAt(0) - 0xFEE0)});
}; */
$( document ).ready(function() {
/*
     $('#parents_age').keyup(function(){
    var target = $(this);
    var val = target.val();
    target.val('  '); // double space
    target.val(val.toHalfWidth());
});*/
	if ($('#customer_type').val() == 'その他'){
                $('#other_customer_type').prop('type', 'text');
        }
	if ($('#cancer_name').val() == 'その他のがん種'){
                $('#other_cancer_name').prop('type', 'text');
        }
});
$('#customer_type').change(function(){
	if($(this).val() == 'その他'){
		$('#other_customer_type').prop('type', 'text');
	}else {
		$('#other_customer_type').prop('type', 'hidden');
	}
});

$('#cancer_name').change(function(){
        if($(this).val() == 'その他のがん種'){
                $('#other_cancer_name').prop('type', 'text');
        }
	else{
                $('#other_cancer_name').prop('type', 'hidden');
        }
});

    $('#concern1').change(function(){
	if($(this).val() == '01.がんの治療')
{
        $('#sub_concern1').empty();
		$('#sub_concern1').attr("required", true);
        var text1='01-01.手術';
        var text2='01-02.放射線治療';
        var text3='01-03.薬物療法';
        var text4='01-04.免疫療法';
        var text5='01-05.ゲノム医療';
	var text6='01-99.その他';
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
		$('#sub_concern1').attr("required", true);
        var text1='冊子・資料入手方法';
        var text2='がん相談支援センター連絡先';
        var text3='がん情報サービスweb関連';
        var text4='NCC中央病院関連';
		var text6='症例検索システム関連';						 
        var text5='その他（主訴に具体的内容を記入）';
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
	$('#sub_concern1').attr("required", false);
	}
});

</script>

<script>
    $('#concern2').change(function(){
        if($(this).val() == '01.がんの治療')
{
         $('#sub_concern2').empty();
        var text1='01-01.手術';
        var text2='01-02.放射線治療';
        var text3='01-03.薬物療法';
        var text4='01-04.免疫療法';
        var text5='01-05.ゲノム医療';
        var text6='01-99.その他';
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
/*        var text1='がん相談支援センター連絡先';
        var text2='患者必携入手方法';
        var text3='NCC中央病院関連';
        var text4='がん情報サービスweb関連';
        var text5='その他';
        var text7='';*/
	var text1='冊子・資料入手方法';
        var text2='がん相談支援センター連絡先';
        var text3='がん情報サービスweb関連';
        var text4='NCC中央病院関連';
		var text6='症例検索システム関連';									 
        var text5='その他（主訴に具体的内容を記入）';
        var text7='';

        $('<option>').val(text7).text(text7).appendTo('#sub_concern2');
        $('<option>').val(text1).text(text1).appendTo('#sub_concern2');
        $('<option>').val(text2).text(text2).appendTo('#sub_concern2');
        $('<option>').val(text3).text(text3).appendTo('#sub_concern2');
        $('<option>').val(text4).text(text4).appendTo('#sub_concern2');
		$('<option>').val(text4).text(text6).appendTo('#sub_concern2');
        $('<option>').val(text5).text(text5).appendTo('#sub_concern2');
}
else {
        $('#sub_concern2').empty();
		$('#sub_concern2').attr("required", false);
        }
});
   
/*************************************/
 $('#action1').change(function(){
        if($(this).val() == 'サポセン独自')
	{
         $('#sub_action1').empty();
        	$('#sub_action1').attr("required", true);
        	var text7='--';
        	var text1='資料入手方法・送付';
        	var text2='相談支援センター連絡先';
        	var text3='がん情報サービスweb関連';
        	var text4='検索システム関連';
        	var text5='NCC中央病院';
        	var text6='admin誘導（統計、二次利用など）';
        $('<option>').val(text7).text(text7).appendTo('#sub_action1');
        $('<option>').val(text1).text(text1).appendTo('#sub_action1');
        $('<option>').val(text2).text(text2).appendTo('#sub_action1');
        $('<option>').val(text3).text(text3).appendTo('#sub_action1');
        $('<option>').val(text4).text(text4).appendTo('#sub_action1');
        $('<option>').val(text5).text(text5).appendTo('#sub_action1');
        $('<option>').val(text6).text(text6).appendTo('#sub_action1');
}
else {
        $('#sub_action1').empty();
                $('#sub_action1').attr("required", false);
        }
});


 $('#action2').change(function(){
        if($(this).val() == 'サポセン独自')
        {
         $('#sub_action2').empty();
                $('#sub_action2').attr("required", true);
                var text7='--';
                var text1='資料入手方法・送付';
                var text2='相談支援センター連絡先';
                var text3='がん情報サービスweb関連';
                var text4='検索システム関連';
                var text5='NCC中央病院';
                var text6='admin誘導（統計、二次利用など）';
        $('<option>').val(text7).text(text7).appendTo('#sub_action2');
        $('<option>').val(text1).text(text1).appendTo('#sub_action2');
        $('<option>').val(text2).text(text2).appendTo('#sub_action2');
        $('<option>').val(text3).text(text3).appendTo('#sub_action2');
        $('<option>').val(text4).text(text4).appendTo('#sub_action2');
        $('<option>').val(text5).text(text5).appendTo('#sub_action2');
        $('<option>').val(text6).text(text6).appendTo('#sub_action2');
}
else {
        $('#sub_action2').empty();
                $('#sub_action2').attr("required", false);
        }
});

/********************************************/

function addOption() {

  var select = document.getElementById("applicable");
  var new_option = document.getElementById("newapplicable").value;
  var q = document.URL.split('?')[1];
 $.ajax('./add_remove_applicable_dropdown.php?action=insert&value='+new_option+'&'+q,{
    success: function (data, status, xhr) {
	  var parseData= JSON.parse(data);
	 if (parseData['response']==="success"){
		select.options[select.options.length] = new Option(new_option, new_option);
		document.getElementById('display-msg').innerHTML='<span style="display: inline;color: green;" >追加しました </span>';
		document.getElementById("newapplicable").value='';
	}else{
		document.getElementById('display-msg').innerHTML='<span style="display: inline;color: red;" >エラーが発生しました。 </span>';
	}
    }
  });
	
  //select.options[select.options.length] = new Option(new_option, new_option);
}

function removeOption() {
  var select = document.getElementById("applicable");
  var del_option= select.options[select.selectedIndex].value ;
  var q = document.URL.split('?')[1];

  if (select.selectedIndex > 7){
    $.ajax('./add_remove_applicable_dropdown.php?action=delete&value='+del_option+'&'+q,{
    success: function (data, status, xhr) {
          var parseData= JSON.parse(data);
         if (parseData['response']==="success"){
		select.remove(select.selectedIndex);
		document.getElementById('display-msg').innerHTML='<span style="display: inline;color: green;" > '+ del_option+'を削除しました </span>';
                //select.options[select.options.length] = new Option(new_option, new_option);
        }else {
		document.getElementById('display-msg').innerHTML='<span style="display: inline;color: red;" >削除できませんでした </span>';
        }
    }
  });	
  }
}

function addOption1() {

  var select = document.getElementById("action1");
  var new_option = document.getElementById("newaction").value;
  var q = document.URL.split('?')[1];
 $.ajax('./add_remove_response_dropdown.php?action=insert&value='+new_option+'&'+q,{
    success: function (data, status, xhr) {
          var parseData= JSON.parse(data);
         if (parseData['response']==="success"){
                select.options[select.options.length] = new Option(new_option, new_option);
                document.getElementById('display-msg1').innerHTML='<span style="display: inline;color: green;" >追加しました </span>';
		document.getElementById("newaction").value='';
        }else{
                document.getElementById('display-msg1').innerHTML='<span style="display: inline;color: red;" >エラーが発生しました。 </span>';
        }
    }
  });
        
  //select.options[select.options.length] = new Option(new_option, new_option);
}

function removeOption1() {
  var select = document.getElementById("action1");
  var del_option= select.options[select.selectedIndex].value ;
  var q = document.URL.split('?')[1];

  if (select.selectedIndex > 5){
    $.ajax('./add_remove_response_dropdown.php?action=delete&value='+del_option+'&'+q,{
    success: function (data, status, xhr) {
          var parseData= JSON.parse(data);
         if (parseData['response']==="success"){
                select.remove(select.selectedIndex);
                document.getElementById('display-msg1').innerHTML='<span style="display: inline;color: green;" > '+ del_option+' を削除しました </span>';
                //select.options[select.options.length] = new Option(new_option, new_option);
        }else {
                document.getElementById('display-msg1').innerHTML='<span style="display: inline;color: red;" >削除できませんでした </span>';
        }
    }
  });   
  }
}

function addOption2() {

  var select = document.getElementById("action2");
  var new_option = document.getElementById("newapplicable2").value;
  var q = document.URL.split('?')[1];
 $.ajax('./add_remove_applicable_dropdown.php?action=insert&value='+new_option+'&'+q,{
    success: function (data, status, xhr) {
          var parseData= JSON.parse(data);
         if (parseData['response']==="success"){
                select.options[select.options.length] = new Option(new_option, new_option);
                document.getElementById('display-msg3').innerHTML='<span style="display: inline;color: green;" >追加しました </span>';
		document.getElementById('newapplicable2').value="";
        }else{
                document.getElementById('display-msg3').innerHTML='<span style="display: inline;color: red;" >エラーが発生しました。 </span>';
        }
    }
  });


/*
  var select = document.getElementById("action2");
  var new_option = document.getElementById("newaction2").value;
  var q = document.URL.split('?')[1];
 $.ajax('./add_remove_response_dropdown.php?action=insert&value='+new_option+'&'+q,{
    success: function (data, status, xhr) {
          var parseData= JSON.parse(data);
         if (parseData['response']==="success"){
                select.options[select.options.length] = new Option(new_option, new_option);
                document.getElementById('display-msg3').innerHTML='<span style="display: inline;color: green;" >追加しました </span>';
        }else{
                document.getElementById('display-msg3').innerHTML='<span style="display: inline;color: red;" >エラーが発生しました。 </span>';
        }
    }
  });
*/
        
  //select.options[select.options.length] = new Option(new_option, new_option);
}

function removeOption2() {
  
  var select = document.getElementById("action2");
  var del_option= select.options[select.selectedIndex].value ;
  var q = document.URL.split('?')[1];

  if (select.selectedIndex > 7){
    $.ajax('./add_remove_applicable_dropdown.php?action=delete&value='+del_option+'&'+q,{
    success: function (data, status, xhr) {
          var parseData= JSON.parse(data);
         if (parseData['response']==="success"){
                select.remove(select.selectedIndex);
                document.getElementById('display-msg3').innerHTML='<span style="display: inline;color: green;" > '+ del_option+'を削除しました </span>';
                //select.options[select.options.length] = new Option(new_option, new_option);
        }else {
                document.getElementById('display-msg3').innerHTML='<span style="display: inline;color: red;" >削除できませんでした </span>';
        }
    }
  });   
  }



  /*var select = document.getElementById("action2");
  var del_option= select.options[select.selectedIndex].value ;
  var q = document.URL.split('?')[1];

  if (select.selectedIndex > 5){
    $.ajax('./add_remove_response_dropdown.php?action=delete&value='+del_option+'&'+q,{
    success: function (data, status, xhr) {
          var parseData= JSON.parse(data);
         if (parseData['response']==="success"){
                select.remove(select.selectedIndex);
                document.getElementById('display-msg3').innerHTML='<span style="display: inline;color: green;" > '+ del_option+' を削除しました </span>';
                //select.options[select.options.length] = new Option(new_option, new_option);
        }else {
                document.getElementById('display-msg3').innerHTML='<span style="display: inline;color: red;" >削除できませんでした </span>';
        }
    }
  });   
  }*/
}


function removeAllOptions() {

  var select = document.getElementById("action1");
  select.options.length = 0;
}

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

