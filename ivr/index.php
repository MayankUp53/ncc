<?php
//注意事項
//(1)23:59～24:00はパターンに関係無く、常に時間外とする。checkTimerange()参照。

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include 'ameyodb.php';//DB接続
$did = $_GET['dstphone'];
date_default_timezone_set('Asia/Tokyo');//念の為指定

/////*****グローバル変数定義*****/////
global $log_path; //ログ出力先
global $ccid; //コールセンターID
$log_path = "/ameyo_mnt/dacx/ameyo/crm/html/ncc/ivr/IVR_System.log";
$ccid = 1;
/////**********************/////


outputLog("***start***");//debug
//本日の日付情報取得
$now = date('Y-m-d H:i:s');
$date = substr($now,0,10);
$time = substr($now,11,5);

//現在の曜日番号（日:0 月:1 火:2 水:3 木:4 金:5 土:6）を取得
$weekno = date('w');
//$time = "23:59";
//$date = "2016-09-16";
//$weekno = "0";

//////debug Start////
if (!empty($_GET['time'])){$time = $_GET['time'];}
if (!empty($_GET['date'])){$date = $_GET['date'];}
if (!empty($_GET['weekno'])){$weekno = $_GET['weekno'];}


////debug End////

outputLog("***date time***");//debug
outputLog("now:".$now);//debug
outputLog("date:".$date);//debug
outputLog("time:".$time);//debug
outputLog("weekno:".$weekno);//debug

//DID基本情報取得SQL
$data = getDidInfo($did,$now);
if ($data == 0){
	//DID情報なし
	outputLog("Error : DID Not Found");//エラーログ出力
	exit;
}
$day_conf_id = $data['day_conf_id'];
$did_id = $data['id'];
$dstCampaign_id = $data['campaign_id'];
$feature_context_id = $data['feature_context_id'];

outputLog("***ips_did***");//debug
outputLog("did:".$did);//debug
outputLog("day_conf_id:".$day_conf_id);//debug
outputLog("did_id:".$did_id);//debug
outputLog("dstCampaign_id:".$dstCampaign_id);//debug
outputLog("feature_context_id:".$feature_context_id);//debug

//本日の日付が例外登録されているか確認
$pattern_id = checkExceptions($did_id,$date);
if ($pattern_id == 0){
	//例外登録されていない
	outputLog("本日の日付が例外（非営業日）登録されていない");//debug
	//echo "本日の日付が例外（非営業日）登録されていない<br>";//debug
	
	//休日マスタチェック（本日が祝日かどうか）
	$return = checkHolidays($date);
	if ($return == 1){//祝祭日の場合
		outputLog("休日マスタにて祝祭日と判定");//debug
		//echo "休日マスタにて祝祭日と判定<br>";//debug
		$weekno = 7;
	}
	//パターンID取得
	$pattern_id = getPatternId($day_conf_id,$weekno);
	outputLog("pattern_id:".$pattern_id);//debug
	//営業時間判定
	$isNonOfficeHour = checkNonOfficeHour($pattern_id,$time);
}else{
	//例外登録されている
	outputLog("本日の日付が例外（非営業日）登録されている pattern_id:".$pattern_id);//debug
	//echo "本日の日付が例外（非営業日）登録されている / pattern_id:".$pattern_id,"<br>";//debug
	
	//営業時間判定
	$isNonOfficeHour = checkNonOfficeHour($pattern_id,$time);
}
$prompt_name = "";
if ($isNonOfficeHour == 1){
	//音声ガイダンスファイル名取得
	$prompt_name = getVoicePrompt($pattern_id);
	outputLog("音声ガイダンス:".$prompt_name);//debug
	//echo "音声ガイダンス:".$prompt_name."<br>";//debug
}

$json = getParamArr($isNonOfficeHour,$dstCampaign_id,$feature_context_id,$prompt_name);
$json = json_encode($json);
echo $json;
outputLog($json);//debug
outputLog("***end***");//debug
exit;

function getDidInfo($did,$now){
	global $ccid;
	//DID基本情報取得SQL
	$sql = "select * from ips_dids 
				 where enable = 1 
				 and did like '%$did'
				 and (is_deleted <> 1 or is_deleted is null)
				 and (enable_from < '$now' and enable_to > '$now') 
				 and ccid = '$ccid'
				 order by id asc limit 1";
	$result = pg_query($sql);
	outputLog($sql);//debug
	if (!$result){
		return $sql;
	}else{
		$data = pg_fetch_array($result);
		return $data;
	}
}
function checkExceptions($did_id,$date){
	global $ccid;
	//例外登録情報取得SQL
	$sql = "select * from ips_exceptions 
				where did_id = $did_id 
				and \"yyyy-mm-dd\" = '$date'
				and (is_deleted <> 1 or is_deleted is null)
				and ccid = '$ccid'
				order by updated desc limit 1
				";
				
	$result = pg_query($sql);
	outputLog($sql);//debug
	$row = pg_num_rows($result);
	if ($row == 0){
		outputLog("本日は例外登録されていない");//debug
		return 0;//本日は例外登録されていない
	}else{
		//本日が例外登録されている
		outputLog("本日が例外登録されている");//debug
		$data = pg_fetch_array($result);
		return $data['pattern'];//例外登録がある場合、パターンIDを返却
	}
}
function checkNonOfficeHour($pattern_id,$time){
	//パターンと現在の時刻で判定する
	$return = checkPattern($pattern_id,$time); 
	if ($return == 0){
		//営業時間内
		//$isNonOfficeHour = 0;
		//$prompt_name = "";
		outputLog("営業時間内と判定");//debug
	}elseif ($return == 1){
		//営業時間外
		//$isNonOfficeHour = 1;
		outputLog("営業時間外と判定");//debug
	}else{
		//エラー
		outputLog("エラー:checkNonOfficeHour");//debug
		//$return == -1
		//営業時間内とする
		outputLog("営業時間内と判定:パターン未設定");//debug
		$return = 0;
	}
	return $return;

}

function checkPattern($pattern_id,$time){
	global $ccid;
	//パターンチェックSQL
	if (!empty($pattern_id)){
		$sql = "select * from ips_patterns 
					where id = $pattern_id 
					and (is_deleted <> 1 or is_deleted is null)
					and ccid = '$ccid'
				";
		$result = pg_query($sql);
	}else{
		$result = -1;
		return $result; //パターン未設定時は時間内として処理
	}
	//戻り値
	////時間内：0////
	////時間外：1////
	////エラー：-1////
	if (!$result){
		return -1;
	}else{
		$data = pg_fetch_array($result);
		
		//時間範囲（from to）の1~5を配列に格納
		$arrFrom = array($data['from1'],$data['from2'],$data['from3'],$data['from4'],$data['from5']);
		$arrTo = array($data['to1'],$data['to2'],$data['to3'],$data['to4'],$data['to5']);
		$return = 0; //初期値
		for ($i=0; $i<5; $i++){
			$return = checkTimerange($arrFrom[$i],$arrTo[$i],$time);
			if ($return == 1){ //判定結果が「時間外」の場合、即ループを抜ける
				break;
			}
		}
		return $return;
	}

}
function checkTimerange($from,$to,$time){
	global $ccid;
	//23:59～24:00はパターンに関係無く、常に時間外とする。
	if ($time == "23:59"){
		return 1;
	}
	if ($from == "" || $to == ""){ //from to のいずれかが空白の場合（基本的には存在しないはず）
		outputLog("from/to is not configured");
		return 0;
	}elseif (strtotime($from) <= strtotime($time) && strtotime($to) > strtotime($time)) {
		//時間外
		//未来 > 過去
		//strtotime($from 00:00) =< strtotime($time 00:00)
		//strtotime($to 09:00) > strtotime($time 00:00)
		
		outputLog("1::"."現在の時刻".$time." パターン:".$from."-".$to);
		return 1;
	}else{
		//時間内
		outputLog("0::"."現在の時刻".$time." パターン:".$from."-".$to);
		return 0;
	}
}
function outputLog($str){
	global $log_path;
	error_log($str."\n","3",$log_path);
}
function checkHolidays($date){
	global $ccid;
	//休日マスタチェックSQL
	$sql = "select * from ips_holidays 
				where \"yyyy-mm-dd\" = '$date' 
				and (is_deleted <> 1 or is_deleted is null)
				and ccid = '$ccid'
			";
	$result = pg_query($sql);
	$row = pg_num_rows($result);
	outputLog($sql);//debug
	if ($row > 0){
		return 1;//休日マスタにデータ有り
	}else{
		return 0;//休日マスタにデータなし
	}
}
function getPatternId($day_conf_id,$weekno){
	global $ccid;
	$pattern_id = 0;//初期値
	
	//パターンID取得SQL
	$sql = "select * from ips_weeks 
				where id = $day_conf_id 
				and (is_deleted <> 1 or is_deleted is null)
				and ccid = '$ccid'
			";
				
	$result = pg_query($sql);
	outputLog($sql);//debug
	if (!$result){
		return -1;//レコードなし
		exit;
	}else{
		$data = pg_fetch_array($result);
		switch ($weekno){
			case 0://日
				$pattern_id = $data['sunday'];
				break;
			case 1://月
				$pattern_id = $data['monday'];
				break;
			case 2://火
				$pattern_id = $data['tuesday'];
				break;
			case 3://水
				$pattern_id = $data['wednesday'];
				break;
			case 4://木
				$pattern_id = $data['thursday'];
				break;
			case 5://金
				$pattern_id = $data['friday'];
				break;
			case 6://土
				$pattern_id = $data['saturday'];
				break;
			case 7://祝祭日
				$pattern_id = $data['holiday'];
				break;
			default://通常ありえない。
				$pattern_id = -1;
		}
	}
	return $pattern_id;//パターンIDを返却。未設定の場合、0を返却
}

function getVoicePrompt($pattern_id){
	global $ccid;
	//音声ガイダンスファイル名取得
	$sql = "select voice_prompt from ips_patterns 
				where id = $pattern_id 
				and is_deleted = 0 
				and ccid = '$ccid'
			";
	$result = pg_query($sql);
	outputLog($sql);//debug
	if (!$result){
		return -1;//レコードなし
		exit;
	}
	$data = pg_fetch_array($result);
	return $data['voice_prompt'];
}
function getParamArr($var1,$var2,$var3,$var4){
	$array = array('isNonOfficeHour'=>$var1);
	$array = array_merge($array,array('dstCampaignId'=>$var2));
	$array = array_merge($array,array('feature_context_id'=>$var3));
	$array = array_merge($array,array('promptFile'=>$var4));
	return $array;
}
?>
