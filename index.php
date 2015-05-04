<?php
define("TOKEN", "weixin");

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
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
	//验证签名
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if($tmpStr == $signature){
            return true;
        }else{
            return false;
        }
    }
	function httpRequest($url)
    {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    if ($output === FALSE){
        return "cURL Error: ". curl_error($ch);
    }
    return $output;
    }
	function getWeatherInfo($cityName)
    {
    $mykey="YGWEw6TyaGFvnGoXGGOaW23U";	
    //获取实时天气
    $url = "http://api.map.baidu.com/telematics/v3/weather?location=".$cityName."&output=json&ak=".$mykey;
    $output = file_get_contents($url);
    $weather = json_decode($output, true); 
		$b=date('H',time());
		$e="http://ww1.sinaimg.cn/bmiddle/005NLjAjjw1erdmhjhzfuj309z06o0tc.jpg";	
		if($b>=6&&$b<=18){
			$a = $weather['results'][0]['weather_data'][0]['dayPictureUrl'];
		}else{
			$a = $weather['results'][0]['weather_data'][0]['nightPictureUrl'];
		}
    $info = $weather['results'];
    $weatherArray = array();
    $weatherArray[] = array("Title"=>$info[0]['currentCity']."天气预报", "Description"=>"", "PicUrl"=>$e, "Url" =>"http://ww1.sinaimg.cn/bmiddle/005NLjAjjw1erdmhjhzfuj309z06o0tc.jpg");
    $result = "天气：".$info[0]['weather_data'][0]['weather']." 风速：".$info[0]['weather_data'][0]['wind']."温度:".$info[0]['weather_data'][0]['temperature']."\n".$info[0]['index'][2]['des'];
    $weatherArray[] = array("Title"=>$result, "Description"=>"", "PicUrl"=>$a, "Url" =>"");
    $url = "http://api.map.baidu.com/telematics/v3/weather?location=".$cityName."&output=json&ak=".$mykey;
    $output = file_get_contents($url);
    $weather = json_decode($output, true); 
	$info = $weather['results'];
    $maxlength = 3;
    for ($i = 1; $i <= $maxlength; $i++) {
        $c = $info[0]['weather_data'][$i];
        $subTitle =$c['date'].""."天气：".$c['weather']." ".$c['wind']." "."温度：".$c['temperature'];
		if($b>=6&&$b<=18){
			$d=$c[$i]['dayPictureUrl'];
		}else{
			$d=$c[$i]['nightPictureUrl'];
		}
        $weatherArray[] = array("Title" =>$subTitle, "Description" =>"", "PicUrl" =>"", "Url" =>"");
    }
    return $weatherArray;
    }
	//获得百度地图的附近信息
    function catchEntitiesFromLocation($entity, $x, $y, $radius)
    {  	
    $search = $this -> Place_search($entity, $x.",".$y, $radius);
    $results = $search['results'];
    for ($i = 0; $i < count($results); $i++){
        $distance = $this -> getDistance($x, $y, $results[$i]['location']['lat'], $results[$i]['location']['lng']);
        $shopSortArrays[] = array(
            "Title"=>"【".$results[$i]['name']."】<".$distance."M>".$results[$i]['address'].(isset($results[$i]['telephone'])?" ".$results[$i]['telephone']:""),
            "Description"=>"", 
            "PicUrl"=>"http://ww2.sinaimg.cn/thumbnail/005NLjAjjw1erguat6bdzj30gn09zt99.jpg", 
            "Url"=>$results[$i]['detail_url']);
    }
    ksort($shopSortArrays);//排序
    $shopArray = array(); 
    foreach ($shopSortArrays as $key => $value) {  
        $shopArray[] =  array(
                        "Title" => $value["Title"],
                        "Description" => $value["Description"],
                        "PicUrlic" => $value["PicUrl"],
                        "Url" => $value["Url"],
                    );
        if (count($shopArray) > 6){break;}
    }
    return $shopArray;
    }
    //获取两点间的距离	
	function getDistance($lat_a, $lng_a, $lat_b, $lng_b) {
    //R是地球半径（米）
    $R = 6366000;
    $pk = doubleval(180 / 3.14169);
    
    $a1 = doubleval($lat_a / $pk);
    $a2 = doubleval($lng_a / $pk);
    $b1 = doubleval($lat_b / $pk);
    $b2 = doubleval($lng_b / $pk);

    $t1 = doubleval(cos($a1) * cos($a2) * cos($b1) * cos($b2));
    $t2 = doubleval(cos($a1) * sin($a2) * cos($b1) * sin($b2));
    $t3 = doubleval(sin($a1) * sin($b1));
    $tt = doubleval(acos($t1 + $t2 + $t3));

    return round($R * $tt);
    }
	//查询数据库
	function searchUserLocation($userWxid)
    {
    $mysql_host = "localhost";
    $mysql_host_s = "localhost";
    $mysql_port = "3360";
    $mysql_user = "ol_internal2";
    $mysql_password = "01|0plko9";
    $mysql_database = "internal2";
    $mysql_table = "Location";
    $mysql_state = "SELECT * FROM ".$mysql_table." WHERE userWxid = \"".$userWxid."\"";
    $con = mysql_connect($mysql_host.':'.$mysql_port, $mysql_user, $mysql_password);
    if (!$con){
        die('Could not connect: ' . mysql_error());
    }
    mysql_query("SET NAMES 'UTF8'");
    mysql_select_db($mysql_database, $con);
    $result = mysql_query($mysql_state);
    $location = array(); 
    while($row = mysql_fetch_array($result))
    {
        $location["x"] = $row["locationX"]; 
        $location["y"] = $row["locationY"]; 
    }
    mysql_close($con);
    if (isset($location["x"]) && $location["x"] != 0.0){
        return $location;
    }else{
        return "系统中没有你的地理位置信息，请先发送位置给我！您不用担心你的行踪被泄漏，因为你可以滑动地图，把别处的地址发送过来。";
    }
    
    }
	//更新数据库数据
    function updateOrInsert($weixinid, $locationX, $locationY)
    {    

        $mysql_host = "localhost";
        $mysql_host_s = "localhost";
        $mysql_user = "ol_internal2";
        $mysql_password = "01|0plko9";
        $mysql_database = "internal2";
        $mysql_table = "Location";
		$mysql_state = "INSERT INTO ".$mysql_table." VALUES(\"".$weixinid."\", ".$locationX.", ".$locationY.") ON DUPLICATE KEY UPDATE locationX = ".$locationX.", locationY = ".$locationY.";";
    $con = mysql_connect($mysql_host,$mysql_user,$mysql_password);
    if (!$con){
        die('Could not connect: ' . mysql_error());
    }
    mysql_query("SET NAMES 'UTF8'");
    mysql_select_db($mysql_database, $con);
	$result = mysql_query($mysql_state);
}
	//响应消息并进行消息分离
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $this->logger("R ".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
				case "location":
				    $result = $this->receiveLocation($postObj);
					break;
            }
            $this->logger("T ".$result);
            echo $result;
        }else {
            echo "";
            exit;
        }
    }
    //接受事件
    private function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            case "subscribe":
			$content = "欢迎关注校园VIP！"."\n"."帮助："."\n"."1.查天气：回复TQ+城市名"."\n"."2.查人品：回复RP+名字，例如RP张三"."\n"."3.看新闻：回复新闻"."\n"."4.发送位置信息获取周边服务"."\n"."5.输入2048，玩游戏";       
                break;
            case "unsubscribe":
                $content = "取消关注";
                break;
        }
        $result = $this->transmitText($object, $content);
        return $result;
    }
	//接受地址
    private function receiveLocation($object){
		$content = $this -> updateOrInsert($object->FromUserName,$object->Location_X,$object->Location_Y);
		$content .= "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label
		."\n"."帮助："."\n"."1.回复附近+服务查询周边服务，eg.附近ATM"."\n"."2.回复团购+服务查询团购服务，eg.团购美食";
		$result = $this->transmitText($object,$content);
		return $result;
	}
	//curl封装的方法
	 function curl_request($url,$post='',$cookie='', $returnCookie=0){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
        if($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if($returnCookie){
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie']  = substr($matches[1][0], 1);
            $info['content'] = $body;
            return $info;
        }else{
            return $data;
        }
}
	//接受文字信息
    private function receiveText($object)
    {
        $keyword = trim($object->Content);
		if(preg_match('/^(TQ)|(tq)[\x{4e00}-\x{9fa5}]+$/iu',$keyword)){ 
		    $a = substr($keyword,2,strlen($keyword));
            $content = $this-> getWeatherInfo($a);
			$result = $this->transmitNews($object, $content);
		}else if(preg_match('/^RP[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/iu',$keyword)){
            $a = substr($keyword,2,strlen($keyword));  
            $content = $this->getMoralInfo($a);	
            $result = $this->transmitText($object, $content);
		}else if(substr($keyword,0,2)=="20"){
			$userid=substr($keyword,0,12);
			$usersecret=substr($keyword,13,18);
			$url="http://jwxt.sdu.edu.cn:7890/zhxt_bks/zhxt_bks.html";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $a = curl_exec($curl);
            curl_close($curl);
            $result = $this->transmitText($object, $content);
        }else if($keyword=="2048"){
            $content=$this->getGame($keyword);
            $result=$this->transmitNews($object,$content);			
        }else if($keyword=="新闻"){
			$content = $this->getNews($keyword);
			$result = $this -> transmitNews($object,$content);
		}else if(substr($keyword,0,6)=="附近"||substr($keyword,0,6)=="团购"){
		    $strstr=substr($keyword,6,strlen($keyword));
			if(substr($keyword,0,6)=="附近"){
            if($strstr==""){
				$content="帮助："."\n"."1.回复附近+服务查询周边服务，eg.附近ATM"."\n"."2.回复团购+服务查询团购服务，eg.团购美食";
				$result= $this->transmitText($object,$content);
			}else{
				$locationXY = $this -> searchUserLocation($object->FromUserName);
				if(is_array($locationXY)){
					$radius=5000;
					$TitleArray=array();
					$TitleArray[]=array("title"=>"【附近的".$strstr."】如下:","description"=>"","PicUrl"=>"","Url"=>"");
					$searchArray = $this->catchEntitiesFromLocation($strstr,$locationXY['x'],$locationXY['y'],$radius);
					if(count($searchArray)==0){
						$content="附近没有".$strstr;
				        $result= $this->transmitText($object,$content);
					}else{
						$result=$this->transmitNews($object,array_merge_recursive($TitleArray,$searchArray));
					}
				}else{
					$result=$this->transmitText($object,$locationXY);
				}
			}
		}			
		}else if($keyword=="帮助"){
			$content = "欢迎关注校园VIP！"."\n"."帮助："."\n"."1.查天气：回复TQ+城市名"."\n"."2.查人品：回复RP+名字，例如RP张三"."\n"."3.看新闻：回复新闻"."\n"."4.发送位置信息获取周边服务"."\n"."5.输入2048，玩游戏";
			$result = $this->transmitText($object, $content);
		}else if(!empty($keyword)&&$keyword!="帮助"){
			$content = $this->chatRobot($keyword);
			$result = $this->transmitText($object,$content);
		}else{
			$content = "欢迎关注校园VIP！"."\n"."帮助："."\n"."1.查天气：回复TQ+城市名"."\n"."2.查人品：回复RP+名字，例如RP张三"."\n"."3.看新闻：回复新闻"."\n"."4.发送位置信息获取周边服务"."\n"."5.输入2048，玩游戏";
			$result = $this->transmitText($object, $content);			
		}
        return $result;
    }
	//附近商家接口
    function Geocoding_coordinate_address($location) 
    {   
        return $this-> call("geocoder", array("location" => $location));
    } 
    function Place_search($query, $location, $radius) 
    {
        return $this -> call("place/search", array("query" => $query, "location" => $location, "radius" => $radius));
    }
    function call($method, $params = array())
    {
		$api_server_url = "http://api.map.baidu.com/";
        $auth_params = array();
        $auth_params['key'] = "YGWEw6TyaGFvnGoXGGOaW23U";
        $auth_params['output'] = "json";
        $headers = array(
            "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1",
           "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
           "Accept-Language: en-us,en;q=0.5",
            "Referer: http://developer.baidu.com/"
        );
        $params = array_merge($auth_params, $params);
        $url = $api_server_url . "$method?".http_build_query($params);
        //if (DEBUG_MODE){echo "REQUEST: $url" . "\n";}
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);    
        $result = null;
        if (!empty($data)){
            $result = json_decode($data,ture);
        }
        else{
            echo "cURL Error:". curl_error($ch);
        }
        return $result;
    }	
	//获取新闻
	private function getNews($keyword){
		if($keyword=="新闻"){
				$newsArray[] = array("Title" =>"新闻联播", "Description" =>"获取最新新闻点击进入", "PicUrl" =>"http://ww4.sinaimg.cn/thumbnail/005NLjAjjw1erfj5so108j30go0ajmxy.jpg", "Url" =>"http://news.sohu.com");
		}
		return $newsArray;
	}
	private function getGame($keyword){
		if(!empty($keyword)){
			$result[]=array(
			"Title"=>"2048小游戏",
			"Description"=>"游戏规则很简单，每次可以选择上下左右其中一个方向去滑动，每滑动一次，所有的数字方块都会往滑动的方向靠拢外，系统也会在空白的地方乱数出现一个数字方块，相同数字的方块在靠拢、相撞时会相加。系统给予的数字方块不是2就是4，玩家要想办法在这小小的16格范围中凑出“2048”这个数字方块。",
			"PicUrl"=>"http://img.laohu.com/www/201403/27/1395908994962.png",
			"Url"=>"http://gabrielecirulli.github.io/2048/"
			);
		}
		return $result;
	}
	//聊天机器人数据接口
    private function chatRobot($keyword){
		$apikey = "40265d861d9d7465c9280ef326d91964";
		$apiURL = "http://www.tuling123.com/openapi/api?key=KEY&info=INFO"; 
		header("Content-type: text/html; charset=utf-8"); 
		$url = str_replace("INFO", $keyword, str_replace("KEY", $apikey, $apiURL)); 
		$a =file_get_contents($url); 
		$b=json_decode($a,ture);
		$content=$b['text'];
		return $content;
	}
    private function getUnicodeFromUTF8($word) {     
      //获取其字符的内部数组表示，所以本文件应用utf-8编码！     
      if (is_array( $word))     
         $arr = $word;     
      else       
         $arr = str_split($word);     
      //此时，$arr应类似array(228, 189, 160)     
      //定义一个空字符串存储     
       $bin_str = '';     
      //转成数字再转成二进制字符串，最后联合起来。     
       foreach ($arr as $value)     
         $bin_str .= decbin(ord($value));     
      //此时，$bin_str应类似111001001011110110100000,如果是汉字"你"     
      //正则截取     
       $bin_str = preg_replace('/^.{4}(.{4}).{2}(.{6}).{2}(.{6})$/','$1$2$3', $bin_str);     
      //此时， $bin_str应类似0100111101100000,如果是汉字"你"     
      return bindec($bin_str); //返回类似20320， 汉字"你"     
      //return dechex(bindec($bin_str)); //如想返回十六进制4f60，用这句     
    }  
    private function getMoralInfo($name){  
            $name = str_replace("+", "", $name);  
            $f = mb_substr($name,0,1,'utf-8');  
            $s = mb_substr($name,1,1,'utf-8');  
            $w = mb_substr($name,2,1,'utf-8');  
            $x = mb_substr($name,3,1,'utf-8');  
            $n=($this->getUnicodeFromUTF8($f) + $this->getUnicodeFromUTF8($s) + $this->getUnicodeFromUTF8($w) + $this->getUnicodeFromUTF8($x)) % 100;  
            $addd='';  
            if(empty($name)){  
                $addd="大哥不要玩我啊，名字都没有你想算什么！";  
          
            } else if ($n <= 0) {  
                $addd ="你一定不是人吧？怎么一点人品都没有？！";  
            } else if($n > 0 && $n <= 5) {  
                $addd ="算了，跟你没什么人品好谈的...";  
            } else if($n > 5 && $n <= 10) {  
                $addd ="是我不好...不应该跟你谈人品问题的...";  
            } else if($n > 10 && $n <= 15) {  
                $addd ="杀过人没有?放过火没有?你应该无恶不做吧?";  
            } else if($n > 15 && $n <= 20) {  
                $addd ="你貌似应该三岁就偷----看隔壁大妈洗澡的吧...";   
            } else if($n > 20 && $n <= 25) {  
                $addd ="你的人品之低下实在让人惊讶啊...";   
            } else if($n > 25 && $n <= 30) {  
                $addd ="你的人品太差了。你应该有干坏事的嗜好吧?";  
            } else if($n > 30 && $n <= 35) {  
                $addd ="你的人品真差!肯定经常做偷鸡摸狗的事...";  
            } else if($n > 35 && $n <= 40) {  
                $addd ="你拥有如此差的人品请经常祈求佛祖保佑你吧...";  
            } else if($n > 40 && $n <= 45) {  
                $addd ="老实交待..那些论坛上面经常出现的偷---拍照是不是你的杰作?";   
            } else if($n > 45 && $n <= 50) {  
                $addd ="你随地大小便之类的事没少干吧?";  
            } else if($n > 50 && $n <= 55) {  
                $addd ="你的人品太差了..稍不小心就会去干坏事了吧?";   
            } else if($n > 55 && $n <= 60) {  
                $addd ="你的人品很差了..要时刻克制住做坏事的冲动哦..";   
            } else if($n > 60 && $n <= 65) {  
                $addd ="你的人品比较差了..要好好的约束自己啊..";   
            } else if($n > 65 && $n <= 70) {  
                $addd ="你的人品勉勉强强..要自己好自为之..";   
            } else if($n > 70 && $n <= 75) {  
                $addd ="有你这样的人品算是不错了..";  
            } else if($n > 75 && $n <= 80) {  
                $addd ="你有较好的人品..继续保持..";   
            } else if($n > 80 && $n <= 85) {  
                $addd ="你的人品不错..应该一表人才吧?";  
            } else if($n > 85 && $n <= 90) {  
                $addd ="你的人品真好..做好事应该是你的爱好吧..";   
            } else if($n > 90 && $n <= 95) {  
                $addd ="你的人品太好了..你就是当代活雷锋啊...";  
            } else if($n > 95 && $n <= 99) {  
                $addd ="你是世人的榜样！";  
            } else if($n > 100 && $n < 105) {  
                $addd ="天啦！你不是人！你是神！！！";   
            }else if($n > 105 && $n < 999) {  
                $addd="你的人品已经过 100 人品计算器已经甘愿认输，3秒后人品计算器将自杀啊";  
            } else if($n > 999) {  
                $addd ="你的人品竟然负溢出了...我对你无语..";   
            }  
            return $name."的人品分数为：".$n."\n".$addd;  
    }  
     //输出文本信息     
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
    //输出新闻信息
    private function transmitNews($object, $arr_item)
    {
        if(!is_array($arr_item))
            return;

        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($arr_item as $item)
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);

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
        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item));
        return $result;
    }
    
    private function logger($log_content)
    {

    }
}
?>
