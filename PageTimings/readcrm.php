<?php
$file = "./crmprops";
$user = "";
$host = "";
$addNewnumbers = "";
$dbname = "";
$password = "";
$ameyohost = "";
$ameyodbhost = "";
$ameyodbname = "";
$enablecrmdebug = "";
$crmtype = "";

function readcrmconf($returntype) {
	global $file;
	global $addNewnumbers;
	global $host;
	global $user;
	global $password;
	global $dbname;
	global $ameyohost;
	global $ameyodbhost;
	global $ameyodbname;
	global $enablecrmdebug;
	global $crmtype;

	if (!$fp = fopen($file, "r")) {



if ($returntype == 1)
		return false ;
		else{
		echo "CRMプロパティファイルのオープンエラー。ファイルの存在を確認してください";
		exit;
	}
}
	while (!feof($fp)) {
		$line = fgets($fp);
		//Check for comments starting with special chars
		$comment = preg_replace("^[~`!@#$%^&*()-_+=:;'\",.<>/?{[}]|\]", "$line");

		if (!$comment) {

			$str = trim($line);
			$str = explode("=", $str);
			$str[0] = trim($str[0]);
			$str[1] = trim($str[1]);
			switch ($str[0]) {
				case "addNewnumbers" :

					if ($str[1] == "true" || $str[1] == "false") {
						$addNewnumbers = $str[1];
						//echo $addNewnumbers;
					} else {
						echo "無効な値が入力されました（新規番号）";
					}

					break;
				case "pgConnectionString.host" :
					$host = $str[1];
					//echo $host;
					/*$not_alphanumeric = ereg("^[~`!@#$%^&*()-_+=:;',.<>/?{[}]|\]", "$str[1]");					
					if (!$not_alphanumeric) {
								echo $host;
					} else 	{
									echo "無効なホスト名です";
					}*/

					break;

				case "pgConnectionString.user" :
					$user = $str[1];
					//echo $user;					
					/*$not_alphanumeric = ereg("^[~`!@#$%^&*()-_+=:;',.<>/?{[}]|\]", "$str[1]");					
					if (!$not_alphanumeric) {
								echo $user;
					} else 	{
									echo "無効なユーザ名です";
					}*/

					break;

				case "pgConnectionString.password" :
					$password = $str[1];
					//echo $password;					
					/*$not_alphanumeric = ereg("^[~`!@#$%^&*()-_+=:;',.<>/?{[}]|\]", "$str[1]");					
					if (!$not_alphanumeric) {
								echo $password;
					} else 	{
									echo "無効なパスワードです";
					}*/

					break;

				case "pgConnectionString.dbname" :
					$dbname = $str[1];
					//echo $dbname;				
					/*$not_alphanumeric = ereg("^[~`!@#$%^&*()-_+=:;',.<>/?{[}]|\]", "$str[1]");					
					if (!$not_alphanumeric) {
								echo $dbname;
					} else 	{
									echo "無効なDB名です";
					}*/

					break;

				case "pgConnectionString.ameyohost" :
					$ameyohost = $str[1];
					//echo $ameyoip;
					/*$not_alphanumeric = ereg("^[~`!@#$%^&*()-_+=:;',.<>/?{[}]|\]", "$str[1]");
					if (!$not_alphanumeric) {
					                        echo $dbname;
					} else  {
					                                echo "無効なDB名です";
					}*/

					break;
				
				case "pgConnectionString.ameyodbhost" :
					$ameyodbhost = $str[1];
					//echo $ameyodbip;
					/*$not_alphanumeric = ereg("^[~`!@#$%^&*()-_+=:;',.<>/?{[}]|\]", "$str[1]");
					if (!$not_alphanumeric) {
					                        echo $ameyodbip;
					} else  {
					                                echo "無効なDBIPです";
					}*/

					break;
				case "pgConnectionString.ameyodbname" :
					$ameyodbname = $str[1];
					//echo $ameyodbname;
					/*$not_alphanumeric = ereg("^[~`!@#$%^&*()-_+=:;',.<>/?{[}]|\]", "$str[1]");
					if (!$not_alphanumeric) {
					                        echo $ameyodbname;
					} else  {
					                                echo "無効なDB名です";
					}*/

					break;
				case "enableCrmDebug" :
                                        $enablecrmdebug = $str[1];
                                        //echo $ameyodbname;
                                        /*$not_alphanumeric = ereg("^[~`!@#$%^&*()-_+=:;',.<>/?{[}]|\]", "$str[1]");
                                        if (!$not_alphanumeric) {
                                                                echo $ameyodbname;
                                        } else  {
                                                                        echo "無効なDB名です";
                                        }*/

                                        break;
				case "crmType" :
					$crmtype = $str[1];
					break;

			}
		}

	}
	fclose($fp);
}
?>

