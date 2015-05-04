<?php
define("TOKEN", "wdianqi");

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
	//��֤ǩ��
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
    //��ȡʵʱ����
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
    $weatherArray[] = array("Title"=>$info[0]['currentCity']."����Ԥ��", "Description"=>"", "PicUrl"=>$e, "Url" =>"http://ww1.sinaimg.cn/bmiddle/005NLjAjjw1erdmhjhzfuj309z06o0tc.jpg");
    $result = "������".$info[0]['weather_data'][0]['weather']." ���٣�".$info[0]['weather_data'][0]['wind']."�¶�:".$info[0]['weather_data'][0]['temperature']."\n".$info[0]['index'][2]['des'];
    $weatherArray[] = array("Title"=>$result, "Description"=>"", "PicUrl"=>$a, "Url" =>"");
    $url = "http://api.map.baidu.com/telematics/v3/weather?location=".$cityName."&output=json&ak=".$mykey;
    $output = file_get_contents($url);
    $weather = json_decode($output, true); 
	$info = $weather['results'];
    $maxlength = 3;
    for ($i = 1; $i <= $maxlength; $i++) {
        $c = $info[0]['weather_data'][$i];
        $subTitle =$c['date'].""."������".$c['weather']." ".$c['wind']." "."�¶ȣ�".$c['temperature'];
		if($b>=6&&$b<=18){
			$d=$c[$i]['dayPictureUrl'];
		}else{
			$d=$c[$i]['nightPictureUrl'];
		}
        $weatherArray[] = array("Title" =>$subTitle, "Description" =>"", "PicUrl" =>"", "Url" =>"");
    }
    return $weatherArray;
    }
	//��ðٶȵ�ͼ�ĸ�����Ϣ
    function catchEntitiesFromLocation($entity, $x, $y, $radius)
    {  	
    $search = $this -> Place_search($entity, $x.",".$y, $radius);
    $results = $search['results'];
    for ($i = 0; $i < count($results); $i++){
        $distance = $this -> getDistance($x, $y, $results[$i]['location']['lat'], $results[$i]['location']['lng']);
        $shopSortArrays[] = array(
            "Title"=>"��".$results[$i]['name']."��<".$distance."M>".$results[$i]['address'].(isset($results[$i]['telephone'])?" ".$results[$i]['telephone']:""),
            "Description"=>"", 
            "PicUrl"=>"http://ww2.sinaimg.cn/thumbnail/005NLjAjjw1erguat6bdzj30gn09zt99.jpg", 
            "Url"=>$results[$i]['detail_url']);
    }
    ksort($shopSortArrays);//����
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
    //��ȡ�����ľ���	
	function getDistance($lat_a, $lng_a, $lat_b, $lng_b) {
    //R�ǵ���뾶���ף�
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
	//��ѯ���ݿ�
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
        return "ϵͳ��û����ĵ���λ����Ϣ�����ȷ���λ�ø��ң������õ���������ٱ�й©����Ϊ����Ի�����ͼ���ѱ𴦵ĵ�ַ���͹�����";
    }
    
    }
	//�������ݿ�����
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
	//��Ӧ��Ϣ��������Ϣ����
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
    //�����¼�
    private function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            case "subscribe":
			$content = "��ӭ��עLhoom���"."\n"."������"."\n"."1.���������ظ�TQ+������"."\n"."2.����Ʒ���ظ�RP+���֣�����RP����"."\n"."3.�����ţ��ظ�����"."\n"."4.����λ����Ϣ��ȡ�ܱ߷���"."\n"."5.����2048������Ϸ";       
                break;
            case "unsubscribe":
                $content = "ȡ����ע";
                break;
        }
        $result = $this->transmitText($object, $content);
        return $result;
    }
	//���ܵ�ַ
    private function receiveLocation($object){
		$content = $this -> updateOrInsert($object->FromUserName,$object->Location_X,$object->Location_Y);
		$content .= "�㷢�͵���λ�ã�γ��Ϊ��".$object->Location_X."������Ϊ��".$object->Location_Y."�����ż���Ϊ��".$object->Scale."��λ��Ϊ��".$object->Label
		."\n"."������"."\n"."1.�ظ�����+�����ѯ�ܱ߷���eg.����ATM"."\n"."2.�ظ��Ź�+�����ѯ�Ź�����eg.�Ź���ʳ";
		$result = $this->transmitText($object,$content);
		return $result;
	}
	//curl��װ�ķ���
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
	//����������Ϣ
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
        }else if($keyword=="����"){
			$content = $this->getNews($keyword);
			$result = $this -> transmitNews($object,$content);
		}else if(substr($keyword,0,6)=="����"||substr($keyword,0,6)=="�Ź�"){
		    $strstr=substr($keyword,6,strlen($keyword));
			if(substr($keyword,0,6)=="����"){
            if($strstr==""){
				$content="������"."\n"."1.�ظ�����+�����ѯ�ܱ߷���eg.����ATM"."\n"."2.�ظ��Ź�+�����ѯ�Ź�����eg.�Ź���ʳ";
				$result= $this->transmitText($object,$content);
			}else{
				$locationXY = $this -> searchUserLocation($object->FromUserName);
				if(is_array($locationXY)){
					$radius=5000;
					$TitleArray=array();
					$TitleArray[]=array("title"=>"��������".$strstr."������:","description"=>"","PicUrl"=>"","Url"=>"");
					$searchArray = $this->catchEntitiesFromLocation($strstr,$locationXY['x'],$locationXY['y'],$radius);
					if(count($searchArray)==0){
						$content="����û��".$strstr;
				        $result= $this->transmitText($object,$content);
					}else{
						$result=$this->transmitNews($object,array_merge_recursive($TitleArray,$searchArray));
					}
				}else{
					$result=$this->transmitText($object,$locationXY);
				}
			}
		}			
		}else if($keyword=="����"){
			$content = "��ӭ��עLhoom���"."\n"."������"."\n"."1.���������ظ�TQ+������"."\n"."2.����Ʒ���ظ�RP+���֣�����RP����"."\n"."3.�����ţ��ظ�����"."\n"."4.����λ����Ϣ��ȡ�ܱ߷���"."\n"."5.����2048������Ϸ";
			$result = $this->transmitText($object, $content);
		}else if(!empty($keyword)&&$keyword!="����"){
			$content = $this->chatRobot($keyword);
			$result = $this->transmitText($object,$content);
		}else{
			$content = "��ӭ��עLhoom���"."\n"."������"."\n"."1.���������ظ�TQ+������"."\n"."2.����Ʒ���ظ�RP+���֣�����RP����"."\n"."3.�����ţ��ظ�����"."\n"."4.����λ����Ϣ��ȡ�ܱ߷���"."\n"."5.����2048������Ϸ";
			$result = $this->transmitText($object, $content);			
		}
        return $result;
    }
	//�����̼ҽӿ�
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
	//��ȡ����
	private function getNews($keyword){
		if($keyword=="����"){
				$newsArray[] = array("Title" =>"��������", "Description" =>"��ȡ�������ŵ������", "PicUrl" =>"http://ww4.sinaimg.cn/thumbnail/005NLjAjjw1erfj5so108j30go0ajmxy.jpg", "Url" =>"http://news.sohu.com");
		}
		return $newsArray;
	}
	private function getGame($keyword){
		if(!empty($keyword)){
			$result[]=array(
			"Title"=>"2048С��Ϸ",
			"Description"=>"��Ϸ����ܼ򵥣�ÿ�ο���ѡ��������������һ������ȥ������ÿ����һ�Σ����е����ַ��鶼���������ķ���£�⣬ϵͳҲ���ڿհ׵ĵط���������һ�����ַ��飬��ͬ���ֵķ����ڿ�£����ײʱ����ӡ�ϵͳ��������ַ��鲻��2����4�����Ҫ��취����СС��16��Χ�дճ���2048��������ַ��顣",
			"PicUrl"=>"http://img.laohu.com/www/201403/27/1395908994962.png",
			"Url"=>"http://gabrielecirulli.github.io/2048/"
			);
		}
		return $result;
	}
	//������������ݽӿ�
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
      //��ȡ���ַ����ڲ������ʾ�����Ա��ļ�Ӧ��utf-8���룡     
      if (is_array( $word))     
         $arr = $word;     
      else       
         $arr = str_split($word);     
      //��ʱ��$arrӦ����array(228, 189, 160)     
      //����һ�����ַ����洢     
       $bin_str = '';     
      //ת��������ת�ɶ������ַ������������������     
       foreach ($arr as $value)     
         $bin_str .= decbin(ord($value));     
      //��ʱ��$bin_strӦ����111001001011110110100000,����Ǻ���"��"     
      //�����ȡ     
       $bin_str = preg_replace('/^.{4}(.{4}).{2}(.{6}).{2}(.{6})$/','$1$2$3', $bin_str);     
      //��ʱ�� $bin_strӦ����0100111101100000,����Ǻ���"��"     
      return bindec($bin_str); //��������20320�� ����"��"     
      //return dechex(bindec($bin_str)); //���뷵��ʮ������4f60�������     
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
                $addd="��粻Ҫ���Ұ������ֶ�û��������ʲô��";  
          
            } else if ($n <= 0) {  
                $addd ="��һ�������˰ɣ���ôһ����Ʒ��û�У���";  
            } else if($n > 0 && $n <= 5) {  
                $addd ="���ˣ�����ûʲô��Ʒ��̸��...";  
            } else if($n > 5 && $n <= 10) {  
                $addd ="���Ҳ���...��Ӧ�ø���̸��Ʒ�����...";  
            } else if($n > 10 && $n <= 15) {  
                $addd ="ɱ����û��?�Ź���û��?��Ӧ���޶�����?";  
            } else if($n > 15 && $n <= 20) {  
                $addd ="��ò��Ӧ�������͵----�����ڴ���ϴ��İ�...";   
            } else if($n > 20 && $n <= 25) {  
                $addd ="�����Ʒ֮����ʵ�����˾��Ȱ�...";   
            } else if($n > 25 && $n <= 30) {  
                $addd ="�����Ʒ̫���ˡ���Ӧ���иɻ��µ��Ⱥð�?";  
            } else if($n > 30 && $n <= 35) {  
                $addd ="�����Ʒ���!�϶�������͵����������...";  
            } else if($n > 35 && $n <= 40) {  
                $addd ="��ӵ����˲����Ʒ�뾭��������汣�����...";  
            } else if($n > 40 && $n <= 45) {  
                $addd ="��ʵ����..��Щ��̳���澭�����ֵ�͵---�����ǲ�����Ľ���?";   
            } else if($n > 45 && $n <= 50) {  
                $addd ="����ش�С��֮�����û�ٸɰ�?";  
            } else if($n > 50 && $n <= 55) {  
                $addd ="�����Ʒ̫����..�Բ�С�ľͻ�ȥ�ɻ����˰�?";   
            } else if($n > 55 && $n <= 60) {  
                $addd ="�����Ʒ�ܲ���..Ҫʱ�̿���ס�����µĳ嶯Ŷ..";   
            } else if($n > 60 && $n <= 65) {  
                $addd ="�����Ʒ�Ƚϲ���..Ҫ�úõ�Լ���Լ���..";   
            } else if($n > 65 && $n <= 70) {  
                $addd ="�����Ʒ����ǿǿ..Ҫ�Լ�����Ϊ֮..";   
            } else if($n > 70 && $n <= 75) {  
                $addd ="������������Ʒ���ǲ�����..";  
            } else if($n > 75 && $n <= 80) {  
                $addd ="���нϺõ���Ʒ..��������..";   
            } else if($n > 80 && $n <= 85) {  
                $addd ="�����Ʒ����..Ӧ��һ���˲Ű�?";  
            } else if($n > 85 && $n <= 90) {  
                $addd ="�����Ʒ���..������Ӧ������İ��ð�..";   
            } else if($n > 90 && $n <= 95) {  
                $addd ="�����Ʒ̫����..����ǵ������׷氡...";  
            } else if($n > 95 && $n <= 99) {  
                $addd ="�������˵İ�����";  
            } else if($n > 100 && $n < 105) {  
                $addd ="�������㲻���ˣ������񣡣���";   
            }else if($n > 105 && $n < 999) {  
                $addd="�����Ʒ�Ѿ��� 100 ��Ʒ�������Ѿ���Ը���䣬3�����Ʒ����������ɱ��";  
            } else if($n > 999) {  
                $addd ="�����Ʒ��Ȼ�������...�Ҷ�������..";   
            }  
            return $name."����Ʒ����Ϊ��".$n."\n".$addd;  
    }  
     //����ı���Ϣ     
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
    //���������Ϣ
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