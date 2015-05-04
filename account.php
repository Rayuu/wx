<?php
header("Content-type: text/html; charset=utf-8");
require("config.php");
if(isset($_POST['submit']))
{
	if($res=mysql_fetch_array(mysql_query('SELECT * FROM user WHERE jwid="'.$_POST['username'].'"')))
	{
			echo '<script type="text/javascript"> 
					alert(\'此账号已绑定\');setTimeout(window.location.href="login.php",3000); </script>';
	}
	else
	{
	//mysql_query('INSERT INTO user(jwid,jwpwd) VALUES("'.$_POST['username'].'","'.md5($_POST['password']).'","'.$_POST['openid'].'")');
	mysql_query('INSERT INTO user(jwid,jwpwd) VALUES("'.$_POST['username'].'","'.md5($_POST['password']).'")');
	echo '<script type="text/javascript"> 
					alert(\'绑定成功\');setTimeout(window.location.href="http://www.baidu.com",3000); </script>';
	}
}


?>