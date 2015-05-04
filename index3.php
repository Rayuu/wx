<?php
/*********************
 @2015/05/02
 @xcy
 @添加客服功能
 @查成绩功能还没添加自定义功能
 @自定义菜单有的可以用了
 @点击“关于我们”可以查看帮助
 *********************/
//define your token
define("TOKEN", "weixin");
$help="回复“你好”，“有人吗”或者点击菜单“关于我们”联系客服，连接到客服后可能有一段时间无法响应关键字回复，请谅解。/::,@\n查成绩功能可以很快就能用了。/::D\n更多功能正在开发中。。。/:,@f";
include("token.php");
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
        require("config.php");
		global $help;
		$access_token=acc_token();
		$openid = $object->FromUserName;
		switch ($object->Event)
        {
			case "subscribe":
				$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
				$output = $this->https_request($url);
				$jsoninfo = json_decode($output, true);

				$sql = "SELECT * FROM user WHERE openid ='{$openid}'";
				$result = mysql_query($sql);
				$row = mysql_fetch_array($result);

				$content= "你好，".$jsoninfo["nickname"]."\n欢迎关注校园VIP\n首次使用查询教务成绩系统要先绑定喲！\n <a href=\"http://wx429.sinaapp.com/login.php?id={$openid}\">点 击 绑 定</a>\n".$help;
				if(empty($row))
				{
				require("config.php");
				mysql_query('INSERT INTO user(openid,nichen,sex,date) VALUES("'.$openid.'","'.$jsoninfo["nickname"].'","'.$jsoninfo["sex"].'","'.date('Y年m月d日',$jsoninfo["subscribe_time"]).'")');

				}				
				break;   
 
			case "unsubscribe":
				break;
			case "CLICK":
				switch($object->EventKey)
				{
					case "查成绩":
						$content=$this->grade($openid);
						break;
					case "绑定":
						$content="使用查询教务成绩系统要先绑定喲！\n <a href=\"http://wx429.sinaapp.com/login.php?id={$openid}\">点 击 绑 定</a>\n";
						break;
					case "四六级":
						$content="四六级成绩查询";
						break;
					case "校园动态":
						$content="校园动态";
						break;
					case "个人中心":
						$content="个人中心";
						break;
					case "每日一读":
						$content="每日一读";
						break;
					case "关于我们":
						$content=$help;
						break;
				}
				break;
			default:break;
        }

        $result = $this->transmitText($object, $content);
        return $result;
    }
	//接收text事件
	private function receiveText($object)
    {
        global $help;
		$keyword = trim($object->Content);
		$openid = $object->FromUserName;
        if($keyword=="成绩")
		{
			$content = $this->grade($openid);

		}
		else  if (strstr($keyword, "您好") || strstr($keyword, "你好") || strstr($keyword, "在吗") || strstr($keyword, "有人吗"))
		{
            $result = $this->transmitService($object);//连接到客服
            return $result;
        }
		else if($keyword=="解绑" )
		{
			
			$content = $this->bangoff($openid);
		}
		else if($keyword=="?" || $keyword=="？")
		{
			$content = $help;
		}
        else 
			$content=date("Y-m-d H:i:s",time()).$keyword;

		$result = $this->transmitText($object, $content);
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

		$url = "http://rayu.wicp.net/helper/getgrade.php?username=".$username."&password=".$password;
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
	//转换图文消息
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