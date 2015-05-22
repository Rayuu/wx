<?php
/*********************
 @2015/05/20
 @xcy
 @添加客服功能
 @查成绩功能快要完善了
 @自定义菜单有的可以用了
 @点击“关于我们”可以查看帮助
 *********************/
//define your token
define("TOKEN", "weixin");
$help="回复“你好”，“有人吗”联系客服，连接到客服后可能有一段时间无法响应关键字回复，请谅解。/::,@\n查成绩功能可以用了。/::D\n更多功能正在开发中。。。/:,@f";


$wechatObj = new wechatCallbackapiTest();
if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}
class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }
	//响应消息
    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
      	//extract post data
		if (!empty($postStr))
		{
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
			$RX_TYPE = trim($postObj->MsgType);
			
			switch($RX_TYPE)
			{
				case "event":
					$result = $this->receiveEvent($postObj);
                    break;
				case "text":
					$result = $this->receiveText($postObj);
                    break;
			}
			echo $result;
        }
		else 
		{
        	echo "";
        	exit;
        }
    }
	
	//接收event事件
    private function receiveEvent($object)
    {
        include("token1.php");
		require("config.php");
		global $help;
		//$access_token=acc_token();
		$access_token=$token;
		$openid = $object->FromUserName;
		$time = time();
		switch ($object->Event)
        {
			case "subscribe":
				$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
				$output = $this->https_request($url);
				$jsoninfo = json_decode($output, true);

				$sql = "SELECT * FROM user WHERE openid ='{$openid}'";
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
				$content[]=array("Title"=>"3、更多功能正在开发中","Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
				$result = $this->transmitNews($object, $content);
				if(empty($row))
				{
				require("config.php");
				mysql_query('INSERT INTO user(openid,nichen,sex,date) VALUES("'.$openid.'","'.$jsoninfo["nickname"].'","'.$jsoninfo["sex"].'","'.date('Y年m月d日',$jsoninfo["subscribe_time"]).'")');
				
				}				
				break;   
 
			case "unsubscribe":
				$sql1 = "DELETE FROM user WHERE openid ='{$openid}'";
				mysql_query($sql1);
				break;
			case "CLICK":
				switch($object->EventKey)
				{
					case "查成绩":
						$sql="UPDATE `user` SET `time`='{$time}'where `openid`= '{$openid}'";
						mysql_query($sql);
						$score = $this->grade($openid);
						$content=array();	
		$sql = "SELECT * FROM user WHERE openid ='{$openid}'";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		$jwid=$row['jwid'];
		$content[]=array("Title"=>"您好，".$jwid,"Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
		$content[]=array("Title"=>"您的2014-2015学年成绩如下","Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
		$content[]=array("Title"=>$score,"Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
						$result = $this->transmitNews($object, $content);
						break;
					case "绑定":
						$sql="UPDATE `user` SET `time`='{$time}'where `openid`= '{$openid}'";
						mysql_query($sql);
						$content="使用查询教务成绩系统要先绑定喲！\n <a href=\"http://wx429.sinaapp.com/login.php?id={$openid}\">点 击 绑 定</a>\n";
						$result = $this->transmitText($object, $content);
						break;
					case "四六级":
						$sql="UPDATE `user` SET `time`='{$time}'where `openid`= '{$openid}'";
						mysql_query($sql);
						$content="四六级成绩查询";
						$result = $this->transmitText($object, $content);
						break;
					case "校园动态":
						$sql="UPDATE `user` SET `time`='{$time}'where `openid`= '{$openid}'";
						mysql_query($sql);
						$content="校园动态";
						$result = $this->transmitText($object, $content);
						break;
					case "个人中心":
						$sql="UPDATE `user` SET `time`='{$time}'where `openid`= '{$openid}'";
						mysql_query($sql);
						$content="个人中心";
						$result = $this->transmitText($object, $content);
						break;
					case "每日一读":
						$sql="UPDATE `user` SET `time`='{$time}'where `openid`= '{$openid}'";
						mysql_query($sql);
						$content="每日一读";
						$result = $this->transmitText($object, $content);
						break;
					case "关于我们":
						$sql="UPDATE `user` SET `time`='{$time}'where `openid`= '{$openid}'";
						mysql_query($sql);
						$content=$help;
						$result = $this->transmitText($object, $content);
						break;
					case "每日签到":
						$sql="UPDATE `user` SET `time`='{$time}'where `openid`= '{$openid}'";
						mysql_query($sql);
						/*$sql = "SELECT `score` FROM user WHERE openid ='{$openid}'";
						$b=mysql_query($sql);
						$a=rand(1,5);
						$b=$b+$a;
						$sql="UPDATE `user` SET `score`='{$b}'where `openid`= '{$openid}'";
						mysql_query($sql);*/
						$content="签到功能会尽快上线，不要着急。";
						$result = $this->transmitText($object, $content);
						break;
					case "联系我们":
						$sql="UPDATE `user` SET `time`='{$time}'where `openid`= '{$openid}'";
						mysql_query($sql);
						$content="发送邮件到xxccyyuu@qq.com\n或者加入我们的QQ群：459955065\n给我们提供宝贵的建议。";
						$result = $this->transmitText($object, $content);
						break;
				}
				break;
			default:break;
        }

        
        return $result;
    }
	//接收text事件
	private function receiveText($object)
    {
        require("config.php");
		global $help;
		$keyword = trim($object->Content);
		$openid = $object->FromUserName;
        if($keyword=="成绩")
		{
			//$content = $this->grade($openid);
			//$result = $this->transmitText($object, $content);
			$sql="UPDATE `user` SET `time`='{$time}'where `openid`= '{$openid}'";
			mysql_query($sql);
			$content=array();
			$sql = "SELECT * FROM user WHERE openid ='{$openid}'";
			$result = mysql_query($sql);
			$row = mysql_fetch_array($result);
			$jwid=$row['jwid'];
			$content[]=array("Title"=>"您好，".$jwid,"Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
			$content[]=array("Title"=>"您的2014-2015学年成绩如下","Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
			$score = $this->grade($openid);
						
						$content[]=array("Title"=>"2014-2015学年成绩如下","Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
						$content[]=array("Title"=>$score,"Description"=>"",
			   "PicUrl"=>"",
			   "Url"=>"");
						$result = $this->transmitNews($object, $content);
		}
		else  if (strstr($keyword, "您好") || strstr($keyword, "你好") || strstr($keyword, "在吗") || strstr($keyword, "有人吗"))
		{
            $sql="UPDATE `user` SET `time`='{$time}'where `openid`= '{$openid}'";
			mysql_query($sql);
			$result = $this->transmitService($object);//连接到客服
            return $result;
        }
		else if($keyword=="解绑" )
		{
			$sql="UPDATE `user` SET `time`='{$time}'where `openid`= '{$openid}'";
			mysql_query($sql);
			$content = $this->bangoff($openid);
			$result = $this->transmitText($object, $content);
		}
		else if($keyword=="?" || $keyword=="？")
		{
			$sql="UPDATE `user` SET `time`='{$time}'where `openid`= '{$openid}'";
			mysql_query($sql);
			$content = $help;
			$result = $this->transmitText($object, $content);
		}
        else
		{
			$sql="UPDATE `user` SET `time`='{$time}'where `openid`= '{$openid}'";
			mysql_query($sql);
			$content=date("Y-m-d H:i:s",time()).$keyword;
			$result = $this->transmitText($object, $content);
		}
        return $result;

    }
	//成绩函数
	private function grade($openid)
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
		return $output;
	}
	//解绑
	private function bangoff($openid)
	{
		require("config.php");		
		$sql = "SELECT * FROM user WHERE openid ='{$openid}'";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		$jwid=$row['jwid'];
		//$sql1 = "DELETE FROM user WHERE openid ='{$openid}'";
		$sql1="UPDATE user SET `jwid`='', `jwpwd`='' WHERE `openid`= '{$openid}' ";
		if(mysql_query($sql1)){
			return "你已经成功解除学号：{$jwid}的绑定！\n点击菜单【绑定】再次绑定。";
		}else{
			return "未知原因，解绑失败，请重新尝试！【hi】";
		}
	}
	//转换文字消息
	private function transmitText($object, $content)
    {
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
	//图文消息处理
     private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $newsTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<Content><![CDATA[]]></Content>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
</xml>";

        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }
	//回复多客服消息
    private function transmitService($object)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
	private function https_request($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($curl);
		if (curl_errno($curl)) {return 'ERROR '.curl_error($curl);}
		curl_close($curl);
		return $data;
	}
	//检验签名
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	
}

?>