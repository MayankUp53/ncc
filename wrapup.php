<?php


require ('./readcrm.php');

readcrmconf();
$sessionId = $_GET['sessionId'];
$campaignId = $_GET['campaignId'];
$crtObjectId = $_GET['crtObjectId'];
$userCrtObjectId = $_GET['userCrtObjectId'];
$userId = $_GET['userId'];
$customerId = $_GET['customerId'];
$phone = $_GET['phone'];
$queueId = $_GET['queueId'];
$queueName = $_GET['queueName'];
$disposition = $_GET["dispositionCode"];
$phoneoriginal = $_GET["phoneoriginal"];
$selfCallback = $_GET["selfCallback"];

$fname = addslashes($_POST["fname"]);
$lname = addslashes($_POST["lname"]);
$designation = addslashes($_POST["designation"]);
$houseno = addslashes($_POST["houseno"]);
$streetno = addslashes($_POST["streetno"]);
$landmarks = addslashes($_POST["landmarks"]);
$city = addslashes($_POST["city"]);
$zip = addslashes($_POST["zip"]);
$state = addslashes($_POST["state"]);
$country = addslashes($_POST["country"]);
$emailid = addslashes($_POST["emailid"]);
$altno = addslashes($_POST["altno"]);
$accno = addslashes($_POST["accno"]);
$bankname = addslashes($_POST["bankname"]);
$sortcode = addslashes($_POST["sortcode"]);
$bankaccname = addslashes($_POST["bankaccname"]);
$verifiername = addslashes($_POST["verifiername"]);
$comments = addslashes($_POST["comments"]);
$isnewNumber = addslashes($_POST["isnewNumber"]);

$timezone = $_POST["timezone"];
$date = $_POST[$timezone."date"];
$month = $_POST[$timezone."month"];
$year = $_POST[$timezone."year"];
$hour = $_POST[$timezone."hour"];
$minute = $_POST[$timezone."minute"];
$second = $_POST[$timezone."second"];
$feedback = $_POST["feedback"];



        $insertPGSQL = "UPDATE customer SET ";
        $insertPGSQL .= "session_id='" . $sessionId . "',";
        $insertPGSQL .= "campaign_id='" . $campaignId . "',";
        $insertPGSQL .= "crt_object_id='" . $crtObjectId . "',";
        $insertPGSQL .= "userCrtObjectId='" . $userCrtObjectId . "',";
        $insertPGSQL .= "customer_id='" . $customerId . "',";
        $insertPGSQL .= "queue_id='" . $queueId . "',";
        $insertPGSQL .= "queue_name='" . $queueName . "',";
        $insertPGSQL .= "disposition='" . $disposition . "',";
        $insertPGSQL .= "user_id='" . $userId . "',";
        $insertPGSQL .= "feedback='" . $feedback . "',";
	    $insertPGSQL .= "updatedon="."now()".",";
        $insertPGSQL .= "fname='" . $fname . "',";
        $insertPGSQL .= "lname='" . $lname . "',";
        $insertPGSQL .= "designation='" . $designation . "',";
        $insertPGSQL .= "houseno='" . $houseno . "',";
        $insertPGSQL .= "streetno='" . $streetno . "',";
        $insertPGSQL .= "landmarks='" . $landmarks . "',";
        $insertPGSQL .= "city='" . $city . "',";
        $insertPGSQL .= "zip='" . $zip . "',";
        $insertPGSQL .= "state='" . $state . "',";
        $insertPGSQL .= "country='" . $country . "',";
        $insertPGSQL .= "emailid='" . $emailid . "',";
        $insertPGSQL .= "altno='" . $altno . "',";
        $insertPGSQL .= "accno='" . $accno . "',";
        $insertPGSQL .= "bankname='" . $bankname . "',";
        $insertPGSQL .= "sortcode='" . $sortcode . "',";
        $insertPGSQL .= "bankaccname='" . $bankaccname . "',";
        $insertPGSQL .= "verifiername='" . $verifiername . "',";
        $insertPGSQL .= "comments='" . $comments . "'";
        $insertPGSQL .= "WHERE phone1='" . $phone . "'";
        //echo "I am updating old record";

       
        $isnewNumber=false;

 //echo "$insertPGSQL";

$dispositionAttr = "$timezone-$date-$month-$year $hour:$minute:$second";

$connection = pg_connect("host=$host user=$user dbname=$dbname");
if (!$connection) {
 // echo "エラー： ".pg_result_error($connection)."." ;
  echo "DB接続中にエラーが発生しました\n";
  exit;
}

$result = pg_query($connection,$insertPGSQL);
if (!$result) {
  echo pg_last_error($connection) ;
  echo "クエリ実行中にエラーが発生しました\n";
  exit;
}

?>

 <html>
<head>
<script language="JavaScript">
//exit_flag is global flag to terminate execution of javascript
var exit_flag = false;
var i=0;
var currtime1 = new Date();
//var currsec1 = currtime.getSeconds();
var currmillisecs1 = currtime1.getTime();
var currsecs1 = currmillisecs1/1000;
var val;
function update(){
   // alert('sss');
	if(exit_flag == true) {
		 return;
	} else {
        //alert(sdfsd);
	      //setTimeout('update()',1000);
        	var currtime2 = new Date();
        	var currmillisecs2 = currtime2.getTime();
	        var currsecs2 = currmillisecs2/1000;
	        var secs_diff = parseInt(currsecs2 - currsecs1);
        	document.getElementById("counter").innerHTML = "リダイレクト中…セッションID（<?echo $sessionId?>）" + secs_diff  + "秒";
         //  alert("sdfds");
	}
}
function redirectToserver()
{
//alert('dfdsfs');
        update();
      //  window.location.href="http://www.slashdot.org";
	 window.location.href="http://<?php echo $ameyohost?>:8888/dacx/dispose?phone=<?=$phone?>&campaignId=<?=$campaignId?>&crtObjectId=<?=$crtObjectId?>&userCrtObjectId=<?=$userCrtObjectId?>&customerId=<?=$customerId?>&dispositionCode=<?=$disposition?>&dispositionAttr=<?=$dispositionAttr?>&sessionId=<?=$sessionId?>&selfCallback=true";
//document.write=val;
//alert(val);        
setTimeout('closeWindow()',5000);

}
function closeWindow() {
	
        document.getElementById("exit").innerHTML = "リダイレクトエラー…コール結果（ディスポジション）URLを確認してください";
	//Set exit_flag to true to stop execution of update function
	exit_flag = true;
	return;
}

       var val="http://<?php echo $ameyohost?>:8888/dacx/dispose?phone=<?=$phone?>&campaignId=<?=$campaignId?>&crtObjectId=<?=$crtObjectId?>&userCrtObjectId=<?=$userCrtObjectId?>&customerId=<?=$customerId?>&dispositionCode=<?=$disposition?>&dispositionAttr=<?=$dispositionAttr?>&sessionId=<?=$sessionId?>&selfCallback=true";
</script>
</head>
<body onload="javascript:redirectToserver();"> 

<div id='counter'  ></div>
<div id='exit'></div>
</body>
</html>


