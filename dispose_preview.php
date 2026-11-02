<?php


require ('./readcrm.php');

readcrmconf(1);
$sessionId = $_GET['sessionId'];
$campaignId = $_GET['campaignId'];
$crtObjectId = $_GET['crtObjectId'];
$userCrtObjectId = $_GET['userCrtObjectId'];
$userId = $_GET['userId'];
$customerId = $_GET['customerId'];
$phone = $_GET['phone'];
$queueId = $_GET['queueId'];
$queueName = $_GET['queueName'];

$phoneoriginal = $_GET["phoneoriginal"];

$crm_date =  addslashes($_POST["crm_date"]);
$crm_day =  addslashes($_POST["crm_day"]);
$crm_time =  addslashes($_POST["crm_time"]);
$customer_type = addslashes($_POST["customer_type"]);
$gender =  addslashes($_POST["gender"]);
$cancer_name =  addslashes($_POST["cancer_name"]);
$other_cancer_name='';
if ($cancer_name=='その他のがん種'){
	$other_cancer_name=addslashes($_POST["other_cancer_name"]);
}

$other_customer_type='';
if ($customer_type=='その他'){
        $other_customer_type=addslashes($_POST["other_customer_type"]);
}

$prefecture =  addslashes($_POST["prefecture"]);
$memo1 =  addslashes($_POST["memo1"]);
$memo2 =  addslashes($_POST["memo2"]);
$memo3 =  addslashes($_POST["memo3"]);
$reaction =  addslashes($_POST["reaction"]);
$concern1 =  addslashes($_POST["concern1"]);
$sub_concern1 =  addslashes($_POST["sub_concern1"]);
$concern2 =  addslashes($_POST["concern2"]);
$sub_concern2 =  addslashes($_POST["sub_concern2"]);
$action1 =  addslashes($_POST["action1"]);
$action2 =  addslashes($_POST["action2"]);
$is_introduced =  addslashes($_POST["is_introduced"]);
$search_available =  addslashes($_POST["search_available"]);
$route =  addslashes($_POST["route"]);
$count_calling =  addslashes($_POST["count_calling"]);
$comments = addslashes($_POST["comments"]);
$availibility = addslashes($_POST["availibility"]);
$treatment_status = addslashes($_POST["treatment_status"]);
$parents_age = addslashes($_POST["parents_age"]);
$applicable = addslashes($_POST["applicable"]);
$sub_action1 = addslashes($_POST["sub_action1"]);
//echo $ameyoip;

$quick  = $_GET["quick"];
if($quick=="true")
{
//echo $quick."Hi";
$dispositionCode = $_GET["dispositionCode"];
} else  {
//echo "hello";
$dispositionCode = $_REQUEST["dispositionCode"];
}
$selfCallback = addslashes($_POST["selfCallback"]);
$dispositionAttr =addslashes($_POST["dispositionAttr"]);

$timezone = addslashes($_POST["timezone"]);
$feedback = addslashes($_POST["feedback"]);

if (strlen($customerId) == 0 || ($customerId) < 0) {
error_log("This is just a Notification message : It  seems and inbound campaign URL(customerId is missing.. proceeding with phone number ) "."http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI']);
$criteria = "phone";

$CUSTOMER_WHERECLAUSE = " WHERE phone1='".$phone."'";
//echo "Iam here";

}
else {
$criteria = "customerid";


$CUSTOMER_WHERECLAUSE = " WHERE customer_id='".$customerId."'";
//echo "Iam here";

}


$insertPGSQLCALL = "UPDATE customer_history SET ";
$insertPGSQLCALL .= "crm_date ='".$crm_date."',";
$insertPGSQLCALL .= "crm_day ='".$crm_day."',";
$insertPGSQLCALL .= "crm_time ='".$crm_time."',";
$insertPGSQLCALL .= "customer_type ='".$customer_type."',";
$insertPGSQLCALL .= "other_customer_type ='".$other_customer_type."',";
$insertPGSQLCALL .= "gender ='".$gender."',";
$insertPGSQLCALL .= "cancer_name ='".$cancer_name."',";
$insertPGSQLCALL .= "other_cancer_name ='".$other_cancer_name."',";
$insertPGSQLCALL .= "prefecture ='".$prefecture."',";
$insertPGSQLCALL .= "memo1 ='".$memo1."',";
$insertPGSQLCALL .= "memo2 ='".$memo2."',";
$insertPGSQLCALL .= "memo3 ='".$memo3."',";
$insertPGSQLCALL .= "reaction ='".$reaction."',";
$insertPGSQLCALL .= "concern1 ='".$concern1."',";
$insertPGSQLCALL .= "sub_concern1 ='".$sub_concern1."',";
$insertPGSQLCALL .= "concern2 ='".$concern2."',";
$insertPGSQLCALL .= "sub_concern2 ='".$sub_concern2."',";
$insertPGSQLCALL .= "action1 ='".$action1."',";
$insertPGSQLCALL .= "action2 ='".$action2."',";
$insertPGSQLCALL .= "is_introduced ='".$is_introduced."',";

$insertPGSQLCALL .= "treatment_status='".$treatment_status."',";
$insertPGSQLCALL .= "parents_age='".$parents_age."',";
$insertPGSQLCALL .= "applicable='".$applicable."',";
$insertPGSQLCALL .= "sub_action1='".$sub_action1."',";


$insertPGSQLCALL .= "search_available ='".$search_available."',";
$insertPGSQLCALL .= "route ='".$route."',";
$insertPGSQLCALL .= "count_calling ='".$count_calling."',";
$insertPGSQLCALL .= "comments ='".$comments."',";
$insertPGSQLCALL .= "availibility ='".$availibility."',";
$insertPGSQLCALL .= "disposition='".$dispositionCode."',";
$insertPGSQLCALL .= "dispositionattr='".$dispositionAttr."',";
$insertPGSQLCALL .= "user_id='".$userId."',";
$insertPGSQLCALL .= "updatedon="."now()";
$insertPGSQLCALL .= " WHERE crt_object_id='".$crtObjectId."'";

$insertPGSQL = "UPDATE customer SET ";
$insertPGSQL .= "crm_date ='".$crm_date."',";
$insertPGSQL .= "crm_day ='".$crm_day."',";
$insertPGSQL .= "crm_time ='".$crm_time."',";
$insertPGSQL .= "customer_type ='".$customer_type."',";
$insertPGSQL .= "other_customer_type ='".$other_customer_type."',";
$insertPGSQL .= "gender ='".$gender."',";
$insertPGSQL .= "cancer_name ='".$cancer_name."',";
$insertPGSQL .= "other_cancer_name ='".$other_cancer_name."',";
$insertPGSQL .= "prefecture ='".$prefecture."',";
$insertPGSQL .= "memo1 ='".$memo1."',";
$insertPGSQL .= "memo2 ='".$memo2."',";
$insertPGSQL .= "memo3 ='".$memo3."',";
$insertPGSQL .= "reaction ='".$reaction."',";
$insertPGSQL .= "concern1 ='".$concern1."',";
$insertPGSQL .= "sub_concern1 ='".$sub_concern1."',";
$insertPGSQL .= "concern2 ='".$concern2."',";
$insertPGSQL .= "sub_concern2 ='".$sub_concern2."',";
$insertPGSQL .= "action1 ='".$action1."',";
$insertPGSQL .= "action2 ='".$action2."',";
$insertPGSQL .= "is_introduced ='".$is_introduced."',";
$insertPGSQL .= "search_available ='".$search_available."',";
$insertPGSQL .= "route ='".$route."',";

$insertPGSQL .= "treatment_status='".$treatment_status."',";
$insertPGSQL .= "parents_age='".$parents_age."',";
$insertPGSQL .= "applicable='".$applicable."',";
$insertPGSQL .= "sub_action1='".$sub_action1."',";



$insertPGSQL .= "count_calling ='".$count_calling."',";
$insertPGSQL .= "comments ='".$comments."',";
$insertPGSQL .= "availibility ='".$availibility."',";
$insertPGSQL .= "user_id='".$userId."',";
$insertPGSQL .= "disposition='".$dispositionCode."',";
$insertPGSQL .= "dispositionattr='".$dispositionAttr."',";
$insertPGSQL .= "updatedon="."now()"."";
$insertPGSQL .= $CUSTOMER_WHERECLAUSE ;

//echo $insertPGSQL;

//echo $insertPGSQLCALL;
//$dispositionAttr = "$timezone-$date-$month-$year $hour:$minute:$second";

//echo " $dispositionCode $selfCallback   $dispositionAttr  $timezone";
//echo "Hi";
 


//exit;
//header("Location: http://google.com/");
//echo "Updating CRM database ...";
//String padding to buffer output in IE
//echo str_pad('', 4096)."\n";
//ob_flush();
//flush();
$connection = pg_connect("host=$host user=$user dbname=$dbname");
if (!$connection) {
	//echo "Error: ".pg_result_error($connection).".";
	echo "An error occured while making connection to the DB.\n";
	exit;
}
//exit ;
// Database connection uses the deployment configuration loaded above.
$resultcust = pg_query($connection, $insertPGSQL);
$resultcall  = pg_query($connection, $insertPGSQLCALL);

//$result_history = pg_query($connection, $qry_history);
if (!$resultcust) {
	//echo pg_last_error($connection);
echo "An error occured while quering the DB for see.\n";
	exit;
}
if (!$resultcall) {
        //echo pg_last_error($connection);
        echo "An error occured while quering the DB for see.\n";
        exit;
}
/*
*/
//echo "$insertPGSQL";
//sleep(3);
//echo "CRM database Updated !!";
//String padding to buffer output in IE
//echo str_pad('', 4096)."\n";
//ob_flush();
//flush();
//sleep(3);
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
        return;
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
        	document.getElementById("counter").innerHTML = "Redirecting call with sessionId  <?echo $sessionId?> to AMEYO ..." + secs_diff  + "seconds";
         //  alert("sdfds");
	}
}
function redirectToserver()
{
//alert('dfdsfs');
        update(); 
      //  window.location.href="http://www.slashdot.org";
//       window.location.href="http://<?php echo $ameyohost?>:8888/dacx/dispose?phone=<?=urlencode($phone)?>&campaignId=<?=urlencode($campaignId)?>&crtObjectId=<?=urlencode($crtObjectId)?>&userCrtObjectId=<?=urlencode($userCrtObjectId)?>&customerId=<?=urlencode($customerId)?>&dispositionCode=<?=urlencode($dispositionCode)?>&dispositionAttr=<?=urlencode($dispositionAttr)?>&sessionId=<?=urlencode($sessionId)?>&selfCallback=<?=urlencode($selfCallback) ?>";
//document.write=val;
//alert(val);        
//setTimeout('closeWindow()',5000);

}
function closeWindow() {
	
        document.getElementById("exit").innerHTML = "Redirecting call to AMEYO failed.Please check disposition URL and performance of Ameyo !!!";
	//Set exit_flag to true to stop execution of update function
	exit_flag = true;
	return;
}

 //      var val="http://<?php echo $ameyohost?>:8888/dacx/dispose?phone=<?=$phone?>&campaignId=<?=$campaignId?>&crtObjectId=<?=$crtObjectId?>&userCrtObjectId=<?=$userCrtObjectId?>&customerId=<?=$customerId?>&dispositionCode=<?=$dispositionCode?>&dispositionAttr=<?=$dispositionAttr?>&sessionId=<?=$sessionId?>&selfCallback=<?=$selfCallback ?>";
</script>
</head>
<body onload="javascript:redirectToserver();"> 

<div id='counter'  ></div>
<div id='exit'></div>
</body>
</html>

<?php  pg_close($connection); ?>

