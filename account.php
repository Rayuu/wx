<?php
/*************
2015/05/09 绑定处理
**************/
header("Content-type: text/html; charset=utf-8");
require("config.php");
include("authcode.php");
if(isset($_POST['submit']) )
{
	$username=$_POST['username'];
	//$pwd=$_POST['password'];
	$openid=$_POST['id'];
	//$sql="SELECT * FROM user WHERE openid='{$openid}'";//查询openid
	$sql="SELECT * FROM user1 WHERE openid='{$openid}'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	$jwid=$row['jwid'];
	$str = $_POST['password'];
	$key = "schoolvip";
	$pass=authcode($str,"ENCODE",$key,0);
	//$sql1="SELECT * FROM user WHERE jwid='{$username}'";//判断是否绑定
	$sql1="SELECT * FROM user1 WHERE jwid='{$username}'";
	if($res=mysql_fetch_array(mysql_query($sql1)) )
	{
		echo '<script type="text/javascript"> 
					alert(\'你的学号已绑定\');setTimeout(window.location.href="login.html",3000); </script>';
	}
	else if(!empty($jwid))
	{
		echo '<script type="text/javascript"> 
					alert(\'你的帐号已绑定了一个学号,请先解绑\');setTimeout(window.location.href="login.html",3000); </script>';
	}
	else
	{
	//mysql_query("UPDATE user SET `jwid`='$username', `jwpwd`='$pwd' WHERE `openid`= '$openid' ");
	mysql_query("UPDATE user1 SET `jwid`='$username', `jwpwd`='$pass' WHERE `openid`= '$openid' ");
	echo '<script type="text/javascript"> 
					alert(\'绑定成功，回复【解绑】取消绑定教务系统\');setTimeout(window.location.href="login.html",3000); </script>';

	}
}
?>