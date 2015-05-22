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
//客服图文回复           
//构造客服信息的Json数据 
$c1=array("title"=>urlencode("欢迎大家关注校园VIP"),"description"=>"","url"=>"","picurl"=>"http://wx429.sinaapp.com/images/logo.jpg");

$c2=array("title"=>urlencode("1、查成绩请先绑定，请点击菜单中的'Hello'->'绑定'，输入学号密码即可。然后点击'查成绩',或者直接回复成绩。回复'解绑'解除绑定"),"description"=>"此条无法显示","url"=>"","picurl"=>"");

$c3=array("title"=>urlencode("2、今天更新了查成绩界面，更加稳定了"),"description"=>"","url"=>"","picurl"=>"");

$c4=array("title"=>urlencode("3、更多功能正在开发中，"),"description"=>"","url"=>"","picurl"=>"");

$c=array($c1,$c2,$c3,$c4);
$a=array("articles"=>$c);

$b=array("touser"=>"{$fromUsername}","msgtype"=>"news","news"=>$a);
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



