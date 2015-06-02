<?php
/**
 删除菜单操作，请不要随意操作
 还有就是要删除请自行修改appid和appsecret
 2015/04/29  
 by_xcy
 */
header('Content-Type: text/html; charset=UTF-8');

include("token.php");

$ACC_TOKEN=$token;

$MENU_URL="https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$ACC_TOKEN;

$cu = curl_init();
curl_setopt($cu, CURLOPT_URL, $MENU_URL);
curl_setopt($cu, CURLOPT_RETURNTRANSFER, 1);
$info = curl_exec($cu);
$res = json_decode($info);
curl_close($cu);

if($res->errcode == "0"){
	echo "菜单删除成功";
}else{
	echo "菜单删除失败";
}

?>