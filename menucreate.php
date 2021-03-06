<?php
/**
自定义菜单生成。
2015/04/29  
by_xcy
 */

header('Content-Type: text/html; charset=UTF-8');

include("token.php");

$ACC_TOKEN=$token;


$data='{
		 "button":[
		 {
			   "name":"校园服务",
			   "sub_button":[
				{
				   "type":"click",
				   "name":"绑定",
				   "key":"绑定"
				},
				{
				   "type":"click",
				   "name":"查成绩",
				   "key":"查成绩"
				},
				{
				   "type":"click",
				   "name":"四六级",
				   "key":"四六级"
				},
				{
				   "type":"click",
				   "name":"校园动态",
				   "key":"校园动态"
				},
				{
				   "type":"click",
				   "name":"个人中心",
				   "key":"个人中心"
				}]
		 },
		  {
			   "name":"便捷生活",
			   "sub_button":[
				{
				   "type":"click",
				   "name":"购物网赚",
				   "key":"购物网赚"
				},
				{
				   "type":"click",
				   "name":"每日一读",
				   "key":"每日一读"
				},
				{
				   "type":"click",
				   "name":"福利经验",
				   "key":"福利经验"
				},
				{
				   "type":"click",
				   "name":"周边服务",
				   "key":"周边服务"
				}]
		   },
		   {
			   
			   "name":"点我",
			   "sub_button":[
				{
				   "type":"click",
				   "name":"每日签到",
				   "key":"每日签到"
				},
				{
				   "type":"click",
				   "name":"联系我们",
				   "key":"联系我们"
				},
				{
				   "type":"click",
				   "name":"关于我们",
				   "key":"关于我们"
				},
				{
				   "type":"click",
				   "name":"微网站",
				   "key":"微网站"
				}]
		   }]
       }';

$MENU_URL="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$ACC_TOKEN;

$ch = curl_init($MENU_URL);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));
$info = curl_exec($ch);
$menu = json_decode($info);
print_r($info);		//创建成功返回：{"errcode":0,"errmsg":"ok"}

if($menu->errcode == "0"){
	echo "菜单创建成功";
}else{
	echo "菜单创建失败";
}

?>