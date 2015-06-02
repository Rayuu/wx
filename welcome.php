<?php
include("token.php");
require("config.php");

$access_token=$token;
$openid = $object->FromUserName;
$time = time();
$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
$output = $this->https_request($url);
$jsoninfo = json_decode($output, true);
$sql = "SELECT * FROM user1 WHERE openid ='{$openid}'";
//$sql = "SELECT * FROM user WHERE openid ='{$openid}'";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);			
$content=array();
$content[]=array("Title"=>"您好，".$jsoninfo["nickname"],"Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
$content[]=array("Title"=>"欢迎关注校园VIP。","Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
$content[]=array("Title"=>"1、查成绩请先绑定，请点击菜单中的'Hello'->'绑定'，输入学号密码即可。然后点击'查成绩',或者直接回复成绩。回复'解绑'解除绑定","Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
$content[]=array("Title"=>"2、每日签到，获取积分可以兑换神秘大奖哦！菜单->'!'->'每日签到'","Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
$content[]=array("Title"=>"3、每日一读功能上线","Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
$content[]=array("Title"=>"4、现在可发送“附近”加目标的命令，如“附近超市”，“附近酒店”，“附近牛肉面” 获取导航。","Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
$content[]=array("Title"=>"5、回复“你好”，“有人吗”联系客服，连接到客服后可能有一段时间无法响应关键字回复，请谅解。","Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
$content[]=array("Title"=>"6、更多功能正在开发中","Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");

?>