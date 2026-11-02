<?php


require_once __DIR__ . '/readcrm.php';

if (!readcrmconf(1)) { http_response_code(500); exit('Configuration unavailable.'); }
$sessionId = crm_request_value($_GET, 'sessionId');
$campaignId = crm_request_value($_GET, 'campaignId');
$crtObjectId = crm_request_value($_GET, 'crtObjectId');
$userCrtObjectId = crm_request_value($_GET, 'userCrtObjectId');
$userId = crm_request_value($_GET, 'userId');
$customerId = crm_request_value($_GET, 'customerId');
$phone = crm_request_value($_GET, 'phone');
$queueId = crm_request_value($_GET, 'queueId');
$queueName = crm_request_value($_GET, 'queueName');

$phoneoriginal = crm_request_value($_GET, 'phoneoriginal');

$crm_date =  crm_request_value($_POST, 'crm_date');
$crm_day =  crm_request_value($_POST, 'crm_day');
$crm_time =  crm_request_value($_POST, 'crm_time');
$customer_type = crm_request_value($_POST, 'customer_type');
$gender =  crm_request_value($_POST, 'gender');
$cancer_name =  crm_request_value($_POST, 'cancer_name');
$other_cancer_name='';
if ($cancer_name=='その他のがん種'){
	$other_cancer_name=crm_request_value($_POST, 'other_cancer_name');
}

$other_customer_type='';
if ($customer_type=='その他'){
        $other_customer_type=crm_request_value($_POST, 'other_customer_type');
}

$prefecture =  crm_request_value($_POST, 'prefecture');
$memo1 =  crm_request_value($_POST, 'memo1');
$memo2 =  crm_request_value($_POST, 'memo2');
$memo3 =  crm_request_value($_POST, 'memo3');
$reaction =  crm_request_value($_POST, 'reaction');
$concern1 =  crm_request_value($_POST, 'concern1');
$sub_concern1 =  crm_request_value($_POST, 'sub_concern1');
$concern2 =  crm_request_value($_POST, 'concern2');
$sub_concern2 =  crm_request_value($_POST, 'sub_concern2');
$action1 =  crm_request_value($_POST, 'action1');
$action2 =  crm_request_value($_POST, 'action2');
$is_introduced =  crm_request_value($_POST, 'is_introduced');
$search_available =  crm_request_value($_POST, 'search_available');
$route =  crm_request_value($_POST, 'route');
$count_calling =  crm_request_value($_POST, 'count_calling');
$comments = crm_request_value($_POST, 'comments');
$availibility = crm_request_value($_POST, 'availibility');
$treatment_status = crm_request_value($_POST, 'treatment_status');
$age_options = array('不明', '非該当', '15才未満（小児）', '15-19歳', '20-29歳', '30-39歳', '40-49歳', '50-59歳', '60-69歳', '70-79歳', '80-89歳', '90歳以上');
$parents_age = isset($_POST['parents_age']) ? $_POST['parents_age'] : '';
if (!is_string($parents_age) || !in_array($parents_age, $age_options, true)) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: text/plain; charset=UTF-8');
    echo '患者の年齢を選択してください。';
    exit;
}
// Keep validated text unchanged; values are bound as database parameters.
$applicable = crm_request_value($_POST, 'applicable');
$sub_action1 = crm_request_value($_POST, 'sub_action1');

$new_field_options = array(
    'caller_age' => $age_options,
    'patient_gender' => array('男', '女', '不明'),
    'family_same_household' => array('不明', '有', '無'),
    'hospital_visited' => null
);
$new_field_labels = array(
    'caller_age' => '相談者の年齢',
    'patient_gender' => '患者の性別',
    'family_same_household' => '患者の同居家族の有無',
    'hospital_visited' => '受診先の病院'
);
$new_field_values = array();
foreach ($new_field_options as $field_name => $allowed_values) {
    $is_required = in_array($field_name, array('caller_age', 'patient_gender'), true);
    // Omitted optional fields retain their saved values.
    if (!$is_required && !isset($_POST[$field_name])) { continue; }
    $field_value = isset($_POST[$field_name]) ? $_POST[$field_name] : '';
    if (!is_string($field_value) || ($allowed_values !== null && !in_array($field_value, $allowed_values, true))) {
        header('HTTP/1.1 400 Bad Request');
        header('Content-Type: text/plain; charset=UTF-8');
        echo $new_field_labels[$field_name].($is_required ? 'を選択してください。' : 'の入力内容が正しくありません。');
        exit;
    }
    $new_field_values[$field_name] = $field_value;
}
//echo $ameyoip;

$quick  = crm_request_value($_GET, 'quick');
if($quick=="true")
{
//echo $quick."Hi";
$dispositionCode = crm_request_value($_GET, 'dispositionCode');
} else  {
//echo "hello";
$dispositionCode = crm_request_value($_REQUEST, 'dispositionCode');
}
$selfCallback = crm_request_value($_POST, 'selfCallback', crm_request_value($_GET, 'selfCallback'));
$dispositionAttr =crm_request_value($_POST, 'dispositionAttr');

$timezone = crm_request_value($_POST, 'timezone');
$feedback = crm_request_value($_POST, 'feedback');

$criteria = ($customerId === '' || $customerId < 0) ? 'phone' : 'customerid';
$values = $new_field_values;
$values['crm_date'] = $crm_date;
$values['crm_day'] = $crm_day;
$values['crm_time'] = $crm_time;
$values['customer_type'] = $customer_type;
$values['other_customer_type'] = $other_customer_type;
$values['gender'] = $gender;
$values['cancer_name'] = $cancer_name;
$values['other_cancer_name'] = $other_cancer_name;
$values['prefecture'] = $prefecture;
$values['memo1'] = $memo1;
$values['memo2'] = $memo2;
$values['memo3'] = $memo3;
$values['reaction'] = $reaction;
$values['concern1'] = $concern1;
$values['sub_concern1'] = $sub_concern1;
$values['concern2'] = $concern2;
$values['sub_concern2'] = $sub_concern2;
$values['action1'] = $action1;
$values['action2'] = $action2;
$values['is_introduced'] = $is_introduced;
$values['search_available'] = $search_available;
$values['route'] = $route;
$values['treatment_status'] = $treatment_status;
$values['parents_age'] = $parents_age;
$values['applicable'] = $applicable;
$values['sub_action1'] = $sub_action1;
$values['count_calling'] = $count_calling;
$values['comments'] = $comments;
$values['availibility'] = $availibility;
$values['user_id'] = $userId;
$values['disposition'] = $dispositionCode;
$values['dispositionattr'] = $dispositionAttr;
$assignments = array();
$params = array_values($values);
foreach (array_keys($values) as $index => $column) {
    $assignments[] = $column . '=$' . ($index + 1);
}
$setClause = implode(',', $assignments) . ',updatedon=now()';
$customerParams = $params;
$customerParams[] = $criteria === 'phone' ? $phone : $customerId;
$customerColumn = $criteria === 'phone' ? 'phone1' : 'customer_id';
$insertPGSQL = 'UPDATE customer SET ' . $setClause . ' WHERE ' . $customerColumn . '=$' . count($customerParams);
$historyParams = $params;
$historyParams[] = $crtObjectId;
$insertPGSQLCALL = 'UPDATE customer_history SET ' . $setClause . ' WHERE crt_object_id=$' . count($historyParams);

$connection = crm_pg_connect(crm_db_connection_string());
if ($connection === false) {
    http_response_code(503);
    exit('Database connection failed.');
}
// Save the customer and matching history together before redirecting to Ameyo.
if (crm_pg_query($connection, 'BEGIN') === false) {
    http_response_code(500);
    exit('Could not start database transaction.');
}
$resultcust = crm_pg_query($connection, $insertPGSQL, $customerParams);
$resultcall = $resultcust !== false ? crm_pg_query($connection, $insertPGSQLCALL, $historyParams) : false;
if ($resultcust === false || $resultcall === false) {
    crm_pg_query($connection, 'ROLLBACK');
    http_response_code(500);
    exit('Could not save customer and history.');
}
if (crm_pg_query($connection, 'COMMIT') === false) {
    http_response_code(500);
    exit('Could not commit customer and history.');
}
$redirectUrl = 'https://' . $ameyohost . ':8443/dacx/dispose?' . http_build_query(array(
    'phone' => $phone,
    'campaignId' => $campaignId,
    'crtObjectId' => $crtObjectId,
    'userCrtObjectId' => $userCrtObjectId,
    'customerId' => $customerId,
    'dispositionCode' => $dispositionCode,
    'dispositionAttr' => $dispositionAttr,
    'sessionId' => $sessionId,
    'selfCallback' => $selfCallback
), '', '&', PHP_QUERY_RFC3986);
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
        	document.getElementById("counter").textContent = "Redirecting call with sessionId " + <?=json_encode($sessionId, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES)?> + " to AMEYO ..." + secs_diff + "seconds";
         //  alert("sdfds");
	}
}
function redirectToserver()
{
//alert('dfdsfs');
        update(); 
      //  window.location.href="http://www.slashdot.org";
       window.location.href=<?=json_encode($redirectUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES)?>;
//document.write=val;
//alert(val);        
setTimeout('closeWindow()',5000);

}
function closeWindow() {
	
        document.getElementById("exit").innerHTML = "Redirecting call to AMEYO failed.Please check disposition URL and performance of Ameyo !!!";
	//Set exit_flag to true to stop execution of update function
	exit_flag = true;
	return;
}

</script>
</head>
<body onload="javascript:redirectToserver();"> 

<div id='counter'  ></div>
<div id='exit'></div>
</body>
</html>

<?php  pg_close($connection); ?>

