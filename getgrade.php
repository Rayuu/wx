<?php

include_once('simple_html_dom.php');

function getGrade($username, $password) {
    //global $jxglurl;
	$jxglurl = "http://210.26.0.32/";
    $ch = curl_init($jxglurl . 'default_ysdx.aspx');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    
    //这里设置文件头可见
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    $header = curl_exec($ch);
    curl_close($ch);
    
    preg_match('/ASP.NET_SessionId=(.*);/', $header, $matches[0]);
    $SessionId = $matches[0][1];
    
    //preg_match('/xmgxy=(.*);/', $header,$matches[0]);
    // $xmgxy = $matches[0][1];
    $xmgxy = '';
    preg_match('/__VIEWSTATE\" value=\"(.*)\" \/>/', $header, $matches[0]);
    $VIEWSTATE = $matches[0][1];
    $attr = array('Button1' => '登录', 'RadioButtonList1' => '学生', 'TextBox1' => $username, 'TextBox2' => $password, '__VIEWSTATE' => $VIEWSTATE);
    $ch = curl_init($jxglurl . 'default_ysdx.aspx');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $attr);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    
    curl_setopt($ch, CURLOPT_COOKIE, "xmgxy=" . $xmgxy . ";ASP.NET_SessionId=" . $SessionId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_exec($ch);
    curl_close($ch);
    
    $ch = curl_init($jxglurl . "xscj_gc.aspx?xh=" . $username);
    curl_setopt($ch, CURLOPT_COOKIE, "xmgxy=" . $xmgxy . ";ASP.NET_SessionId=" . $SessionId);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $header = curl_exec($ch);
    curl_close($ch);
    
    preg_match('/__VIEWSTATE\" value=\"(.*)\" \/>/', $header, $matches[0]);
    $VIEWSTATE = $matches[0][1];
    
    //$attr = array('Button1' => '按学期查询', 'ddlXN' => '2014-2015', 'ddlXQ' => '1', '__VIEWSTATE' => $VIEWSTATE);
	$attr = array('Button1' => '在校学习成绩查询', '__VIEWSTATE' => $VIEWSTATE);
    
    $ch = curl_init($jxglurl . 'xscj_gc.aspx?xh=' . $username);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $attr);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    
    curl_setopt($ch, CURLOPT_COOKIE, "xmgxy=" . $xmgxy . ";ASP.NET_SessionId=" . $SessionId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    
    $html = str_get_html($data);
    $grade = array();
    $tr = $html->find('table#Datagrid1 tr');
    $string = '';
    foreach ($tr as $key => $value) {
        if ($key != 0) {
            $string.= $value->find('td', 3)->plaintext . " ";
            $string.= $value->find('td', 8)->plaintext . "\n";
			$string.='<br />';
        }
    }
	$string = mb_convert_encoding($string, "gb2312", "auto");
    return $string;
}

echo getGrade($_POST['username'],$_POST['password']);

//curl get请求
function curlGet($url,$cookie=false){
    $jxglurl = $config['jxglurl'];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);//这里设置文件头可见
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    if($cookie){
        curl_setopt($ch, CURLOPT_COOKIE, $cookie); //设置cookie
    }
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

//curl POST请求
function curlPost($url,$data,$cookie=false){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $attr);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.4");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host" => $jxglurl, "Referer" => $jxglurl . "default_ysdx.aspx", "Accept-Language" => "zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", "Accept-Encoding" => "gzip, deflate", "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Connection" => "keep-alive", "x-insight" => "activate"));
    
    if($cookie){
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);//设置cookie
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

?>
