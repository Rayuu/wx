<?php


$mmc=memcache_init();//初始化缓存
$token=memcache_get($mmc,"token");//获取token
if(empty($token))
{
$appid="";//填写appid
$secret="";//填写secret
$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$a = curl_exec($ch);
$strjson=json_decode($a);
$access_token = $strjson->access_token;//获取access_token
memcache_set($mmc,"token",$access_token,0,7200);//过期时间7200秒
$token=memcache_get($mmc,"token");//获取token
}



?>



