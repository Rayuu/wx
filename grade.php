<?php
function grade($openid)
{
	require("config.php");
	$sql = "SELECT * FROM user WHERE openid ='{$openid}'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	$jwid=$row['jwid'];
	$jwpwd=$row['jwpwd'];

	$username=$jwid;
	$password=$jwpwd;

	$url = "http://rayu.wicp.net/getgrade.php?username=".$username."&password=".$password;
	$output = file_get_contents($url);
	$contentstr=array();
		$contentstr[]=array("Title"=>"2014-2015学年成绩如下","Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
		$contentstr[]=array("Title"=>"123","Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
	//$result = $this->transmitNews($object, $contentstr);
	return $contentstr;
	//return $output;
}
?>