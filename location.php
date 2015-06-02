<?php
	//获得百度地图的附近信息
    define ("DEBUG_MODE", false);
// var_dump(catchEntitiesFromLocation("银行", "22.123185", "113.23434", "5000"));
function catchEntitiesFromLocation($entity, $x, $y, $radius)
{
    $url = "http://api.map.baidu.com/place/v2/search?ak=ijRjwsFtqX6U3BGxLsiqPoo6&output=json&query=".$entity."&page_size=5&page_num=0&scope=2&location=".$x.",".$y."&radius=".$radius."&filter=sort_name:distance";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    
    $data = json_decode($output, true);

    if ($data['status'] != 0){
        return $data['message'];
    }
    
    $results = $data['results'];
    if (count($results) == 0){
        return "附近没有找到".$entity;
    }
    $shopArray = array();
    $shopArray[] = array("Title"=>"附近的".$entity, "Description"=>"", "PicUrl"=>"", "Url"=>"");
	$shopArray[] = array("Title"=>"现在可发送“附近”加目标的命令，如“附近超市”，“附近酒店”，“附近牛肉面” 获取导航。", "Description"=>"", "PicUrl"=>"", "Url"=>"");
	for ($i = 0; $i < count($results); $i++) {
		$shopArray[] = array(
			"Title"=>"【".$results[$i]['name']."】<".$results[$i]['detail_info']['distance']."米>\n".$results[$i]['address'].
            (isset($results[$i]['telephone'])?"\n".$results[$i]['telephone']:""),
			"Description"=>"", 
			"PicUrl"=>"", 
			"Url"=>(isset($results[$i]['detail_info']['detail_url'])?($results[$i]['detail_info']['detail_url']):""));
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
    require("config.php");	
    $mysql_table = "Location";
    $mysql_state = "SELECT * FROM ".$mysql_table." WHERE userWxid = \"".$userWxid."\"";
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
        require("config.php");	
        $mysql_table = "Location";
		$mysql_state = "INSERT INTO ".$mysql_table." VALUES(\"".$weixinid."\", ".$locationX.", ".$locationY.") ON DUPLICATE KEY UPDATE locationX = ".$locationX.", locationY = ".$locationY.";";
		mysql_query($mysql_state);
    }

function setLocation($openid, $locationX, $locationY)
{
    $mmc = memcache_init();
    if($mmc == true){
        $location = array("locationX"=>$locationX, "locationY"=>$locationY);
        memcache_set($mmc, $openid, json_encode($location), 60);
        return "您的位置已缓存。\n现在可发送“附近”加目标的命令，如“附近酒店”，“附近加油站”。";
    }
    else{
        return "未启用缓存，请先开启服务器的缓存功能。";
    }
}

function getLocation($openid)
{
    $mmc = memcache_init();
    if($mmc == true){
        $location = memcache_get($mmc, $openid);
        if (!empty($location)){
            return json_decode($location,true);
        }else{
            return "请先发送位置给我！\n点击底部的'+'号，再选择'位置'，等地图显示出来以后，点击'发送'";
        }
    }
    else{
        return "未启用缓存，请先开启服务器的缓存功能。";
    }
}
?>