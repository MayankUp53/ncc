<?php

if ($_REQUEST['save'] )
{

if ((!$_POST['crmdbhost']) || (!$_POST['crmdbuser'])  || (!$_POST['crmdbpassword']) || (!$_POST['crmdbname']) || (!$_POST['ameyodbhost']) || (!$_POST['ameyodbname']) || (!$_POST['ameyohost']))
{
 echo "Some data is missing ";
 echo "<br>Click <a href='javascript:history.go(-1)'>Here</a> to go back.";	
 exit ; 
}

echo "Saving<br>" ;
echo "Verifying Database";
$dbCreateQuery="createdb -U " .  $_POST['crmdbuser'] . " " . $_POST['crmdbname']  . " -h " . $_POST['crmdbhost']  ;

$lastline=system($dbCreateQuery,$retval);

$dbCheckQuery="psql -U ". $_POST['crmdbuser'] . " -d " . $_POST['crmdbname'] . " -h " . $_POST['crmdbhost']  . " -c '\q'";
$lastline=system($dbCheckQuery,$retval);
if ( $retval !=  0)
{
echo "Cannot Connect to " . $_POST['crmdbname'] .  " at " . $_POST['crmdbhost'] . " with user " . $_POST['crmdbuser']  ;
echo "<br>Click <a href='javascript:history.go(-1)'>Here</a> to go back.";	
	throw new Exception ( "Cannot Connect to " . $_POST['crmdbname'] .  " at " . $_POST['crmdbhost'] . " with user " . $_POST['crmdbuser'] );
}	

$dbMakeQuery="psql -U ". $_POST['crmdbuser'] . " -d " . $_POST['crmdbname'] . " -h " . $_POST['crmdbhost']  . " -f './createtables.sql'";
$lastline=system($dbMakeQuery,$retval);
if ( $retval !=  0)
{
echo "Cannot Create tables in " . $_POST['crmdbname'] .  " at " . $_POST['crmdbhost'] . " with user " . $_POST['crmdbuser']  ;
echo "<br>Click <a href='javascript:history.go(-1)'>Here</a> to go back.";	
	throw new Exception ( "Cannot Create tables in  " . $_POST['crmdbname'] .  " at " . $_POST['crmdbhost'] . " with user " . $_POST['crmdbuser'] );
}	

$FP=fopen ("./crmprops",'w');
if (!$FP)
{
	echo "Cannot open file crmprops " ;
	throw new Exception ("Cannot open file crmprops");
}

fprintf($FP,"addNewnumbers = true\n");
fprintf($FP,"pgConnectionString.host = " . $_POST["crmdbhost"]  . "\n");
fprintf($FP,"pgConnectionString.user = " . $_POST['crmdbuser']. "\n");
fprintf($FP,"pgConnectionString.password = " .$_POST['crmdbpassword']."\n");
fprintf($FP,"pgConnectionString.dbname = ".$_POST['crmdbname']."\n");
fprintf($FP,"pgConnectionString.ameyohost = ".$_POST['ameyohost']."\n");
fprintf($FP,"pgConnectionString.ameyodbhost = ".$_POST['ameyodbhost']."\n");
fprintf($FP,"pgConnectionString.ameyodbname = ".$_POST['ameyodbname']."\n");
fprintf($FP,"pgConnectionString.leadTableName = leads\n");
fprintf($FP,"pgConnectionString.customerTableName = customer\n");
fprintf($FP,"enableCrmDebug = ".$_POST['enabledebug']."\n");
fprintf($FP,"crmType = ".$_POST['crmtype']."\n");

fclose($FP);
echo "Done" ;

echo '<meta http-equiv="refresh" content="1 ; url=customer.php?phone=101&sessionId=101&campaignId=100&crtObjectId=101&customerId=101">' ;

}

elseif ($_REQUEST['update'] )
{

$FP=fopen("./crmprops",'r');
if (!$FP)
{
	echo "Cannot open file crmprops " ;
	throw new Exception ("Cannot open file crmprops");
}

$crmprops_text = "";
while(!feof($FP)) {
	$line = fgets($FP,4096);
	$crmprops_text =$crmprops_text . $line;
}

$crmprops_arr = explode("\n",$crmprops_text);
$i = 0;
foreach($crmprops_arr as $val) {
	$pos = strpos($val,'enableCrmDebug');
	if ($pos !== false) {
		break; 
	}
	$i = $i+1;
}
unset($crmprops_arr[$i]);
$crmprops_text = implode("\n",$crmprops_arr);
fclose($FP);
$FP=fopen("./crmprops",'w');
fwrite($FP,$crmprops_text);
if($_POST['enabledebug'] == true) 
	$enableDebug = true;
else
	$enableDebug = false;
	
fprintf($FP,"enableCrmDebug = ".$enableDebug."\n");
fclose($FP);
echo "Done" ;
echo '<meta http-equiv="refresh" content="1 ; url=customer.php?phone=101&sessionId=101&campaignId=100&crtObjectId=101&customerId=101">' ;

}

else
{
require ('./readcrm.php');
readcrmconf(1);
global $host ;
if ($host)
{ 
if($enablecrmdebug == true)
	$debug = "checked";
else
	$debug = "";
echo '
<br><br>
<br><br>
<form  name="install" method="POST">
<table cellpadding=2 cellspacing=4 align=center>
<tr><td align=center colspan=2><H1>CRM Configuration</H1></td></tr>
<tr><th align=center colspan=2>CRM Props Values</th></tr>
<tr><td>Please Enter CRM DB host</td><td>'.$host.'</td></tr>
<tr><td>Please Enter CRM DB User</td><td>'.$user.'</td></tr>
<tr><td>Please Enter CRM DB Password</td><td>'.$password.'</td></tr>
<tr><td>Please Enter CRM DB dbname</td><td>'.$dbname.'</td></tr>
<tr><td>Please Enter Ameyo DB host </td><td>'.$ameyodbhost.'</td></tr>
<tr><td>Please Enter Ameyo DB dbname </td><td>'.$ameyodbname.'</td></tr>
<tr><td>Please Enter Ameyo SERVER host </td><td>'.$ameyohost.'</td></tr>
<tr><td>Enable Debugging</td><td><input type="checkbox" name="enabledebug" value="true" '.$debug.'></td></tr>
<tr><td>CRM Type</td><td>'.$crmtype.'</td></tr>
<tr height=10 ><td colspan=2></td></tr>
<tr><td align=right> <input type="reset" name="reset" value="Reset"> </td><td><input type="Submit" name="update" value="Update"></td></tr>
</table><form> ' ;
}
else
{
echo '
<br><br>

<br><br>
<form  name="install" method="POST">
<table cellpadding=2 cellspacing=4 align=center>
<tr><td align=center colspan=2><H1>CRM Configuration</H1></td></tr>
<tr><th align=center colspan=2>Please Enter CRM Props Values</th></tr>
<tr><td>Please Enter CRM DB host</td><td><input type="text" name="crmdbhost" value="127.0.0.1"></td></tr>
<tr><td>Please Enter CRM DB User</td><td><input type="text" name="crmdbuser" value="postgres"></td></tr>
<tr><td>Please Enter CRM DB Password</td><td><input type="text" name="crmdbpassword" value="postgres"></td></tr>
<tr><td>Please Enter CRM DB dbname</td><td><input type="text" name="crmdbname" value="ameyocrm"></td></tr>
<tr><td>Please Enter Ameyo DB host </td><td><input type="text" name="ameyodbhost" value="127.0.0.1"></td></tr>
<tr><td>Please Enter Ameyo DB dbname </td><td><input type="text" name="ameyodbname" value="ameyodb"></td></tr>
<tr><td>Please Enter Ameyo SERVER host </td><td><input type="text" name="ameyohost" value="127.0.0.1"></td></tr>
<tr><td>Enable Debugging</td><td><input type="checkbox" name="enabledebug" value="true" checked="checked"></td></tr>
<tr><td>CRM Type</td><td><input type="radio" name="crmtype" value="ameyocrm" checked="checked">Ameyo CRM,<input type="radio" name="crmtype" value="sugarcrm">Sugar CRM,<input type="radio" name="crmtype" value="other">Other</td></tr>
<tr height=10 ><td colspan=2></td></tr>
<tr><td align=right> <input type="reset" name="reset" value="Reset"> </td><td><input type="Submit" name="save" value="Save"></td></tr>
</table><form> ' ;
}


}
?>
