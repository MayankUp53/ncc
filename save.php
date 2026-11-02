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
$phoneoriginal = $_GET['phoneoriginal'];
$selfCallback = $_GET['selfCallback'];

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
$companyname = addslashes($_POST["companyname"]);


	$insertPGSQL = "UPDATE customer SET ";
	$insertPGSQL .= "session_id='" . $sessionId . "',";
    $insertPGSQL .= "campaign_id='" . $campaignId . "',";
    $insertPGSQL .= "crt_object_id='" . $crtObjectId . "',";
    $insertPGSQL .= "userCrtObjectId='" . $userCrtObjectId . "',";
    $insertPGSQL .= "customer_id='" . $customerId . "',";
    $insertPGSQL .= "queue_id='" . $queueId . "',";
    $insertPGSQL .= "queue_name='" . $queueName . "',";
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
	$insertPGSQL .= "companyname='" . $companyname . "',";
	$insertPGSQL .= "comments='" . $comments . "'";
	$insertPGSQL .= "WHERE phone1='" . $phone . "'";
	//echo "I am updating old record";

	$isnewNumber=false;

#echo "$insertPGSQL";
#echo "$host\t$user\t$password\t$dbname";

$connection = pg_connect("host=$host user=$user dbname=$dbname");
if (!$connection) {
 // echo "Error: ".pg_result_error($connection)."." ;
  echo "An error occured while making connection to the DB.PLease Bug report to CRM Builder\n";
  exit;
}

$result = pg_query($connection,$insertPGSQL);
if (!$result) {
 // echo pg_last_error($connection) ;
  echo "An error occured while quering the DB.Please Bug report to CRM Builder\n";
  exit;
}
   
$result = pg_query("SELECT * FROM customer WHERE phone1='$phone'");
if (!$result) {
  //echo "Error: ".pg_result_error($connection)."." ;
  echo "An error occured while quering with CRM Database.\n";
  exit;
}
$data = pg_fetch_assoc($result);

$ameyodbconn= pg_connect("host=$ameyodbhost user=postgres dbname=$ameyodbname");
//echo $ameyodbname;
if (!$ameyodbconn) {
 // echo "Error: ".pg_result_error($connection)."." ;
  echo "Could not connect to ameyo database .\n";
  exit;
}

if (!$customerId) {
	$disp_query="SELECT dc.disposition_code_name from disposition_code dc, disposition_plan_definition dpd where dc.id=dpd.disposition_code_id and dpd.disposition_plan_id in (select disposition_plan_id from campaign_disposition_plan where campaign_context_id='$campaignId') and dc.disposition_class_id not in (select id from disposition_class where disposition_class_name in ('campaign.customer.exclusion','process.customer.exclusion')) order by dc.disposition_code_name";
} else {
		
	$disp_query="SELECT dc.disposition_code_name from disposition_code dc, disposition_plan_definition dpd where dc.id=dpd.disposition_code_id and dpd.disposition_plan_id in (select disposition_plan_id from campaign_disposition_plan where campaign_context_id='$campaignId') order by dc.disposition_code_name";

}

$res_disp = pg_query($ameyodbconn,$disp_query);
	if(!$res_disp){
                echo "<tr><td colspan=2 align='center'>";
                echo "Error : Could not query to ameyo database for Disposition Plane values!";
                echo "</td></tr>";
                exit();
        }
?>

<html>
   <head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<SCRIPT language="JavaScript">
	</SCRIPT>
  	<link rel="stylesheet" href="drishti.css">
   </head>
<body>
  <form name="main" action="dispose.php?phone=<?=$phone?>&sessionId=<?=$sessionId?>&campaignId=<?=$campaignId?>&crtObjectId=<?=$crtObjectId?>&userCrtObjectId=<?=$userCrtObjectId?>&customerId=<?=$customerId?>&userId=<?=$userId?>&phoneoriginal=<?=$phoneoriginal?>" method="post">

    <table width="100%" height="64px" border="0" cellpadding="0" cellspacing="0">
      <tr bgcolor="#a9e0ff">
        <td width="50%"><img src="img/drishti-logo.png"/>
        <td width="50%" class="text">
          <span class="style2">
            <div align="right">Welcome to DACX &nbsp;</div>
          </span>
        </td>
      </tr>
    </table>

    <table align="center" width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#eeeeee">
      <tr valign="top">
        <td><a class="text" href="customer.php?phone=<?=$phone?>&sessionId=<?=$sessionId?>&userId=<?=$userId?>&campaignId=<?=$campaignId?>&crtObjectId=<?=$crtObjectId?>&userCrtObjectId=<?=$userCrtObjectId?>&customerId=<?=$customerId?>&queueId=<?=$queueId?>&queueName=<?=$queueName?>"> [ Go back ] </a></td>
      </tr>
      <tr valign="top">
        <td colspan="4" class="text" align="center">
          <h2>Set Disposition</h2>
        </td>
      </tr>
      <tr valign="top">
        <td colspan="4" align="center" class="style5">
          Dialed Number: <span class="style1"><?php echo " ";echo $phone;?></span>
        </td>
      </tr>
      <tr valign="top">
	  	<td colspan="4">&nbsp;</td>
	  </tr>
	  <tr valign="top">
	    <td class="text"></td>
	    <td align="left" class="style5">Disposition Code </td>
	    <td><select  name="dispositionCode" id="dispositionCode">
            	<? while ($rowdisp = pg_fetch_row($res_disp))
                {
                	$disp1=$rowdisp[0];
                	$disp2=$rowdisp[1];
                	echo "<option value='$disp1'>$disp1</option>";
                }
              	?>
        	</select></td>
	    <td class="text"></td>
      </tr>
        <tr valign="top">
                <td colspan="4">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td class="text"></td>
            <td align="left" class="style5">Self Callback </td>
                <td><input type="checkbox" name="selfCallback" id="selfCallback" value=true checked="checked"> </td>
            <td class="text"></td>
      </tr>

      
	  <tr valign="top">
	    <td class="text"></td>
	    <td align="left" class="style5"> </td>
	    <td>&nbsp;</td>
	    <tdclass="text"></td>
      </tr>
      <tr valign="top">
        <td width="25%"class="text"></td>
        <td width="25%" class="style5" align="left">Callback Time</td>
        <td width="25%"class="text"></td>
        <td width="25%"class="text"></td> 
      </tr>
<?   if (!$customerId) {
	echo "<tr valign=\"top\">";
	echo "<td width=\"25%\" class=\"text\"></td>";
        echo "<td  width=\"25%\" class=\"text\" align=\"left\">";
     } else {

      echo "<tr valign=\"top\">";
	    echo "<td width=\"25%\" class=\"text\"></td>"; 
	    echo "<td  width=\"25%\" class=\"text\" align=\"left\">";
	    echo "<input name=\"timezone\" type=\"radio\" value=\"customer\">
		  Customer TimeZone";
	    echo "</td>";
        echo "<td width=\"25%\">";
        echo "<select name=\"customerdate\" id=\"date\" onclick=\"checkRadio(0)\">";
		
	for ($i=1 ; $i <=31 ; $i++ ) {
			if ($i < 10) {
				if ($i == '1') {
					echo "<option value='0$i' selected >0$i</option>";
				} else {
					echo "<option value='0$i'>0$i</option>";
				}
			} else {
		    		echo "<option value='$i'>$i</option>"; 
			}
		}
          echo "</select>";
          echo "<select name=\"customermonth\" id=\"month\" onclick=\"checkRadio(0)\">";
                
		for ($i=1 ; $i <=12 ; $i++ ) {
                        if ($i < 10) {
                                if ($i == '1') {
                                        echo "<option value='0$i' selected >0$i</option>";
                                } else {
                                        echo "<option value='0$i'>0$i</option>";
                                }
                        } else {
                                echo "<option value='$i'>$i</option>"; 
                        }
                }
          echo "</select>";
          echo "<select name=\"customeryear\" id=\"year\" onclick=\"checkRadio(0)\">";
	   
                for ($i=2008 ; $i <=2015 ; $i++ ) {
                	echo "<option value='$i'>$i</option>"; 
                }
          
          echo "</select>";
        echo "</td>";
        echo "<td width=\"25%\" class=\"text\"></td> ";
	 echo "</tr>";
	 echo "<tr valign=\"top\">";
	 echo "<td width=\"25%\" class=\"text\"></td> ";
	 echo "<td width=\"25%\"></td>";
	 echo "<td width=\"25%\">";
	 echo "<select name=\"customerhour\" id=\"hour\" onclick=\"checkRadio(0)\">";
	     
                for ($i=0 ; $i <=24 ; $i++ ) {
                        if ($i < 10) {
                                if ($i == '1') {
                                        echo "<option value='0$i' selected >0$i</option>";
                                } else {
                                        echo "<option value='0$i'>0$i</option>";
                                }
                        } else {
                                echo "<option value='$i'>$i</option>";
                        }
                }
          
          echo "</select>";        
	  	 echo " <strong class=\"text\">hr </strong>";
          echo "<select name=\"customerminute\" id=\"minute\" onclick=\"checkRadio(0)\">";
	    
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
                }
          
          echo "</select>";
          echo "<strong class=\"text\">min <strong>";
          echo "<input name=\"customersecond\" type=\"hidden\" id=\"second\" size=\"4\" maxlength=\"4\" value=\"00\">";
 	  echo "</td>";
 	  echo "<td width=\"25%\" class=\"text\"></td>";
 	  echo "</tr>";
     }
 	  echo "<tr valign=\"top\">";
	  	echo "<td  colspan=\"4\">&nbsp;</td>";
      echo "</tr>";
	  echo "<tr valign=\"top\">";
	    echo "<td width=\"25%\" class=\"text\"></td>";
	    echo "<td  width=\"25%\" class=\"text\" align=\"left\"><input type=\"radio\" name=\"timezone\" value=\"local\" > Local TimeZone</td>";
        echo "<td width=\"25%\">";
          echo "<select name=\"localdate\" id=\"date\" onclick=\"checkRadio(1)\">";
	   
                for ($i=1 ; $i <= 31 ; $i++ ) {
                        if ($i < 10) {
                                if ($i == '1') {
                                        echo "<option value='0$i' selected >0$i</option>";
                                } else {
                                        echo "<option value='0$i'>0$i</option>";
                                }
                        } else {
                                echo "<option value='$i'>$i</option>";
                        }
                }
          
          echo "</select>";
          echo "<select name=\"localmonth\" id=\"month\" onclick=\"checkRadio(1)\">";
	    
                for ($i=1 ; $i <= 12 ; $i++ ) {
                        if ($i < 10) {
                                if ($i == '1') {
                                        echo "<option value='0$i' selected >0$i</option>";
                                } else {
                                        echo "<option value='0$i'>0$i</option>";
                                }
                        } else {
                                echo "<option value='$i'>$i</option>";
                        }
                }
          
          echo "</select>";
          echo "<select name=\"localyear\" id=\"year\" onclick=\"checkRadio(1)\">";
	   
                for ($i=2008 ; $i <= 2015 ; $i++ ) {
                      echo "<option value='$i'>$i</option>";
                }
          
          echo "</select>";
        echo "</td>";
        echo "<td width=\"25%\" class=\"text\"></td> ";
	 echo " </tr>";
	  echo "<tr valign=\"top\">";
	    echo "<td width=\"25%\" class=\"text\"></td>";
	    echo "<td width=\"25%\">&nbsp;</td>";
  	    echo "<td width=\"25%\">";
  	      echo "<select name=\"localhour\" id=\"hour\" onclick=\"checkRadio(1)\">";
		 
                for ($i=0 ; $i <= 23 ; $i++ ) {
                        if ($i < 10) {
                                if ($i == '1') {
                                        echo "<option value='0$i' selected >0$i</option>";
                                } else {
                                        echo "<option value='0$i'>0$i</option>";
                                }
                        } else {
                                echo "<option value='$i'>$i</option>";
                        }
                }
          
          echo "</select>";        
	      echo "<strong class=\"text\">hr </strong>";
          echo "<select name=\"localminute\" id=\"minute\" onclick=\"checkRadio(1)\">";
	   
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
                }
          
          echo "</select>";
          echo "<strong class=\"text\">min <strong>";
          echo "<input name=\"localsecond\" type=\"hidden\" id=\"second\" size=\"4\" maxlength=\"4\" value=\"00\">";
        echo "</td>";
        echo "<td width=\"25%\" class=\"text\"></td>";
	  echo "</tr>";
	  echo "<tr valign=\"top\">";
	  	echo "<td  colspan=\"4\">&nbsp;</td>";
      echo "</tr>";
	  echo "<tr valign=\"top\">";
  	    echo "<td width=\"25%\" class=\"text\"></td>";
	    echo "<td width=\"25%\" class=\"text\" align=\"left\"><input type=\"radio\" name=\"timezone\" value=\"after\" checked>After (days/hrs/mins)</td>";
	    echo "<td width=\"25%\">";
	      echo "<select name=\"afterdate\" id=\"date\" onclick=\"checkRadio(2)\">";
		 
                for ($i=1 ; $i <= 30 ; $i++ ) {
                        if ($i < 10) {
                                if ($i == '1') {
                                        echo "<option value='0$i' selected >0$i</option>";
                                } else {
                                        echo "<option value='0$i'>0$i</option>";
                                }
                        } else {
                                echo "<option value='$i'>$i</option>";
                        }
                }
          
          echo "</select>";
          echo "<strong class=\"text\">days </strong>";
          echo "<input name=\"aftermonth\" type=\"hidden\" id=\"month\" value=\"00\" onClick=\"checkRadio(2)\">";
	      echo "<input name=\"afteryear\" type=\"hidden\" id=\"year\" value=\"0000\" onClick=\"checkRadio(2)\">";
	      echo "<select name=\"afterhour\" id=\"hour\" onclick=\"checkRadio(2)\">";
	      
                for ($i=0 ; $i <= 23 ; $i++ ) {
                        if ($i < 10) {
                                if ($i == '1') {
                                        echo "<option value='0$i' selected >0$i</option>";
                                } else {
                                        echo "<option value='0$i'>0$i</option>";
                                }
                        } else {
                                echo "<option value='$i'>$i</option>";
                        }
                }
          
          echo "</select>";        
	  	 echo " <strong class=\"text\">hr </strong>";
          echo "<select name=\"afterminute\" id=\"minute\" onclick=\"checkRadio(2)\">";
	   
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
                }
          
          echo "</select>";
          echo "<strong class=\"text\">min </strong>";
          echo "<input name=\"aftersecond\" type=\"hidden\" id=\"second\" value=\"00\" onClick=\"checkRadio(2)\">";
        echo "</td>";
        echo "<td width=\"25%\" class=\"text\"></td>";
	  echo "</tr>";
  ?>
      <tr valign="top">
	    <td colspan="4">&nbsp;</td>
      </tr>
      <tr valign="top">
	    <td width="25%"class="text"></td>
        <td width="25%" class="style5">Feedback</td>
        <td width="25%"><textarea cols="28" rows="8" name="feedback"><?php echo $data['feedback'];?></textarea></td>
        <td width="25%"class="text"></td>
      </tr>
      <tr valign="top">
	    <td colspan="4"><br/><br/></td>
      </tr>
      <tr valign="top">
        <td colspan="4" align="center">
          <input name="submit" type="submit"  value="Ready for Next Call" class="text">
        </td>
      </tr>
      <tr valign="top">
	    <td colspan="4"><br/><br/><br/><br/><br/><br/><br/></td>
      </tr>      
    </table>
  </form>
</body>
</html>
