<?php

$phone = $_GET['phone'];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://192.168.188.224/bellwww/edenapi/apitlutf8.asp");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,"tl1=$phone&fmt=xl");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$xml = simplexml_load_string($response);

$retval= $xml->retval;

                $retmsg= $xml->retmsg;
                $retrows= $xml->retrows;
                $szTel= $xml->data->szTel;
                $szCP= $xml->data->szCP;
                $szFax= $xml->data->szFax;
                $szPost= $xml->data->szPost;
                $szPubKj= $xml->data->szPubKj;
                $szPubKn= $xml->data->szPubKn;
                $szAdrs1Kj= $xml->data->szAdrs1Kj;
                $szAdrs2Kj= $xml->data->szAdrs2Kj;
                $szAdrs3Kj= $xml->data->szAdrs3Kj;
                $szAdrs4Kj= $xml->data->szAdrs4Kj;
                $szAdrs1Kn= $xml->data->szAdrs1Kn;
                $szAdrs2Kn= $xml->data->szAdrs2Kn;
                $szAdrs3Kn= $xml->data->szAdrs3Kn;
                $szAdrs4Kn= $xml->data->szAdrs4Kn;
                $szAdrsNo= $xml->data->szAdrsNo;
                $szFYear= $xml->data->szFYear;

print_r($xml);


?>

