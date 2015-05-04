<?php
if(isset($_SERVER['HTTP_APPNAME']))
{        //SAE
	$mysql_host = SAE_MYSQL_HOST_M;
	$mysql_host_s = SAE_MYSQL_HOST_S;
	$mysql_port = SAE_MYSQL_PORT;
	$mysql_user = SAE_MYSQL_USER;
	$mysql_password = SAE_MYSQL_PASS;
	$mysql_database = SAE_MYSQL_DB;
}
else
{
	$mysql_host = "127.0.0.1";
	$mysql_host_s = "127.0.0.1";
	$mysql_port = "3306";
	$mysql_user = "root";
	$mysql_password = "root";
	$mysql_database = "weixin";
}
$con = mysql_connect($mysql_host.':'.$mysql_port, $mysql_user, $mysql_password);
if (!$con)
{
	die('Could not connect: ' . mysql_error());
}
//else
//{
//	echo "success";
//}
mysql_select_db($mysql_database, $con)or die ("！！！找不到数据库！！！");

mysql_query("SET NAMES 'UTF8'");

?>