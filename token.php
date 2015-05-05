<?php
function acc_token()
{
	$APPID="";
	$APPSECRET="";

	$TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;

	$json=file_get_contents($TOKEN_URL);
	$result=json_decode($json);

	$ACC_TOKEN=$result->access_token;
	return $ACC_TOKEN;
	
}
?>