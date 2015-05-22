<?php
/*************
2015/05/09 
**************/
?>
<!DOCTYPE html>
<html>
<head>
<title>Hello World</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<meta name="HandheldFriendly" content="true">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="author" content="rayu">
<link rel="shortcut icon" href="favicon.ico">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="renderer" content="webkit">
<link rel="stylesheet" href="./simple/style/main.css?version=1.14.6.2" type="text/css">

<?php 
	if(isset($_GET['id']))
	{
		$openid=$_GET['id'];
	}
	
	else
	{
		echo '<script type="text/javascript"> 
					alert(\'请使用微信关注公众号”校园VIP“体验此功能。\n如果你已经关注，请重新打开此页面\');setTimeout(window.location.href="login.html",3000); </script>';
	}
?>
</head>
<body>
<div class="wrapper" id="page_login">
<h1>Hello World</h1>
<p class="title_desc">请输入学号和密码，P大写</p>
<div id="content-login" class="center-box">
<img src="./simple/style/no_avatar.png" class="avatar">
<form name="f" method="post" action="account.php" onsubmit="return checkpost();">
<div class="login-info">
<p><input type="text" name="username" required tabindex="1" placeholder="用户名"></p>
<p><input type="password" name="password" id="pw" required tabindex="2" placeholder="密码"></p>
<p><input type="hidden" name="id" id="id" value="<?php echo $openid;?>"></p>
</div>
<p><input type="submit" name="submit" value="绑定" tabindex="3" /></p>
</form></div>
</body>
</html>
<script language="JavaScript" >
function checkpost()
{
	if(f.username.value=="")
	{
		alert("请输入账号");
		f.username.focus();
		return false;
	}
	if(f.password.value=="")
	{
		alert("请输入密码");
		f.password.focus();
		return false;
	}
}
</script>
