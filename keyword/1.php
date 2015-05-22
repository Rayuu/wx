<?php
function hh()
{
 $contentStr =array();
			   $contentStr[]= array("Title"=>"日常信息",
			   "Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
			   $contentStr[]= array("Title"=>"【天气预报】\n发送城市+天气，如‘兰州天气’",
			   "Description"=>"",
			   "PicUrl"=>"http://dzsjgzs1.sinaapp.com/image/3.jpg",
			   "Url"=>"");
			   $contentStr[]= array("Title"=>"【号码归属】\n发送手机号+归属，如‘18300000000归属’",
			   "Description"=>"",
			   "PicUrl"=>"http://dzsjgzs1.sinaapp.com/image/4.jpg",
			   "Url"=>"");
			   $contentStr[]= array("Title"=>"【历史上的今天】\n直接回复历史上的今天",
			   "Description"=>"",
			   "PicUrl"=>"http://dzsjgzs1.sinaapp.com/image/5.jpg",
			   "Url"=>"");
			   $contentStr[]= array("Title"=>"【返回菜单】\n回复 ‘menu’ 或者 ‘?’ ",
			   "Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
			   //$resultStr = $this->transmitNews($postObj, $contentStr);//这句话必须要有
               //echo $resultStr;
			   return $contentStr;
}
?>