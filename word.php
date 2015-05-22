<?php
$time = time()-172800;//当前时间-48小时（172800秒）
$mysql = new SaeMysql();//sae内部类链接数据库 
$sql = "select `openid` FROM `user` WHERE '{$time}'<`time`  ";				 
$data = $mysql->getData($sql);//获取符合条件二维数组
include("token1.php");	
//遍历$data数组进行群发
foreach($data as $openid){
	foreach ($openid as $fromUsername){
//构造客服信息的Json数据 
echo $fromUsername;
$contentStr="你好"; 
$contentStr=urlencode($contentStr);//转码urlencode，避免json转码成unicode       
$a=array("content"=>"{$contentStr}");
$b=array("touser"=>"{$fromUsername}","msgtype"=>"text","text"=>$a);
$post=json_encode($b); 
$post=urldecode($post);//解码
$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$token}";
 $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);//url  
curl_setopt($ch, CURLOPT_POST, 1);  //post
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);  
curl_exec($ch);    
curl_close($ch); 
	}
}
?>



