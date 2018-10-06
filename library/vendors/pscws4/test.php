<?php
//test.php
//
// Usage on command-line: php test.php <file|textstring>
// Usage on web: 
error_reporting(E_ALL);

//名字允许复查?
/* $text = <<<EOF
中国航天官员应邀到美国与太空总署官员开会
发展中国家
上海大学城书店
表面的东西
今天我买了一辆面的，于是我坐着面的去上班
化妆和服装
这个门把手坏了，请把手拿开
将军任命了一名中将，产量三年中将增长两倍
王军虎去广州了，王军虎头虎脑的
欧阳明练功很厉害可是马明练不厉害
毛泽东北京华烟云
人中出吕布 马中出赤兔Q1,中我要买Q币充值name
EOF; */

$text = '江湖传言：PHP是世界上最好的编程语言。真的是这样吗？这个梗究竟是从哪来的？学会本课程，你就会明白了。 PHP中文网出品的PHP入门系统教学视频，完全从初学者的角度出发，绝不玩虚的，一切以实用、有用';

/* if (isset($_SERVER['argv'][1])) 
{
	$text = $_SERVER['argv'][1];
	if (strpos($text, "\n") === false && is_file($text)) $text = file_get_contents($text);
}
elseif (isset($_SERVER['QUERY_STRING']))
{
	$text = $_SERVER['QUERY_STRING'];
} */

// 
require 'pscws4.class.php';
$cws = new PSCWS4();
// $cws->set_charset('utf8');
// $cws->set_dict('./etc/dict.utf8.xdb');
// $cws->set_rule('./etc/rules.utf8.ini');
// $cws->set_multi(6);
$cws->set_ignore(true);
//$cws->set_debug(true);
$cws->set_duality(true);
$cws->send_text($text);


while ($tmp = $cws->get_result())
{	
	foreach ($tmp as $w) 
	{
	    var_dump($w);
	}
}

// top:
$ret = array();
$ret = $cws->get_tops(5,'r,v,p');
echo "No.\tWord\t\t\tAttr\tTimes\tRank\n------------------------------------------------------<br>";
foreach ($ret as $tmp)
{
    var_dump($tmp);
}
$cws->close();
?>