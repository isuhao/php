<?php 
/* 使用示例 */   

//header('Content-Type:text/html;charset= UTF-8');
//require("../../config.php");
//require("../../common/curl.php");
//require("../../common/dbaction.php");
//
//$conn=mysql_connect ($dbip, $dbuser, $dbpasswd) or die('数据库服务器连接失败：'.mysql_error());
//mysql_select_db($dbname, $conn) or die('选择数据库失败');
//dh_mysql_query("set names utf8;");
//get_cili('超级笑星','名侦探柯南/铁甲奇侠/Iron Man',3,'2000-05-05 00:00:00',4);
//mysql_close($conn);


//处理电影名  
function get_cili($title,$aka,$type,$updatetime,$pageid=-1)
{ 
	echo " \n begin to get from cili.so:\n";	
	$name = rawurlencode($title);
	
	$buffer = get_file_curl('http://www.cili.so/search.php?act=result&keyword='.$name);
	//echo $buffer;

	if(false==$buffer)
	{
		sleep(5);
		$buffer = get_file_curl('http://www.cili.so/search.php?act=result&keyword='.$name);	
		if(false==$buffer)
		{
			echo $title."搜索失败 </br>\n";
			return;
		}
	}
	//判断类型和名字
	$buffer = iconvbuff($buffer);
	//print_r($buffer);
	preg_match_all('/<a href="sort.*?">(.*?)<\/a>/s',$buffer,$match0);
	preg_match_all('/<a href="show.*?.html" target="_blank">(.*?)<\/a>/s',$buffer,$match1);
//	preg_match_all('/<a href="magnet:\?xt=urn:btih:(.*?)" target="_blank">/s',$buffer,$match2);
	preg_match_all('/<td>([^>]+)<\/td>[^>]+<\/tr>/s',$buffer,$match3);
	preg_match_all('/<a href="http:\/\/www.cili.so\/down.php\?date=(.*?)&amp;hash=(.*?)">/s',$buffer,$match2);
	
	//print_r($match0);
	//print_r($match1);
	//print_r($match2);
	//print_r($match3);
	if(empty($match0[1]))
		return;
	
	$i = 0;
	foreach($match1[1] as $key=>$each)
	{	
		if($i>4)
			break;
		$i++;
		//判断类型
		$thistype=0;
		if(strstr($match0[1][$key],"电影"))
			$thistype=1;
		if(strstr($match0[1][$key],"电视剧"))
			$thistype=2;
		if(strstr($match0[1][$key],"综艺"))
			$thistype=3;
		if(strstr($match0[1][$key],"番号"))
			$thistype=4;			
		if(strstr($match0[1][$key],"动漫"))
			$thistype=4;

		if(strstr($match0[1][$key],"MV"))
			continue;
		if($thistype!=0)
			if($type!=0 && $thistype!=$type)
				continue;		

		$url=$match2[1][$key];
		//$title=$match2[2][$key];
		$updatetime = date("Y-m-d H:i:s",$match2[1][$key]);
		$title =preg_replace('/<span class="keyword">(.*?)<\/span>/s','{$1}',$match1[1][$key]);
		$url='magnet:?xt=urn:btih:'.$match2[2][$key];
		
		addorupdatelink($pageid,'cili.so',$title,$url,'',4,7,7,0,0,$updatetime,1);
	}
}
?>  