<?php
function www_5281520_com_php()
{
	$authorname='红潮网';
	print_r($authorname);
	$authorurl='http://www.5281520.com/';

	$url = array('http://www.5281520.com/html/10/',
				'http://www.5281520.com/html/52/',
				'http://www.5281520.com/html/74/',
				'http://www.5281520.com/html/72/',
				'http://www.5281520.com/html/60/',
				'http://www.5281520.com/html/73/');
	$urlcat= array('华语电影',
					'欧美电影',
					'日韩电影',
					'电视剧',
					'综艺',
					'动画片');
	print_r($url);
	$updatetime = array();	
	foreach ($urlcat as $eachurlcat)
	{
		$sql="select max(updatetime) from link where author='$authorname' and cat like '%".$eachurlcat."%'";
		$sqlresult=dh_mysql_query($sql);
		$row = mysql_fetch_array($sqlresult);
		array_push($updatetime,date("Y-m-d H:i:s",strtotime($row[0])));
	}
	print_r($updatetime);
	
	$newdate = date("Y-m-d H:i:s",strtotime('0000-00-00 00:00:00'));
	foreach ($url as $key=>$eachurl)
	{
		$change = true;
		$i=0;
		while($change&&$i<4)
		{
			$i++;
			if($i==1)
				$trueurl = $eachurl;
			else
				$trueurl = $eachurl.$i.'.htm';
				
			$buff = get_file_curl($trueurl);
			//如果失败，就使用就标记失败次数
			if(!$buff)
			{
				echo 'error: fail to get file '.$trueurl."!</br>\n";	
				$sql="update author set failtimes=failtimes+1 where name='$authorname';";
				$result=dh_mysql_query($sql);
				continue;
			}
			$buff = iconvbuff($buff);
			$rssinfo = new rssinfo();
			$rssinfo->author = $authorname;
			echo "crawl ".$trueurl." </br>\n";
			//print_r($buff);
			preg_match_all('/·<a href="(.*?)" target="_self" title=\'(.*?)\' style="font-size:14px;color:#07519A;">/s',$buff,$match0);
			preg_match_all('/<span style="float:right;font-size:14px;">\((.*?)\)<\/span>/s',$buff,$match1);			
			//print_r($match0);
			//print_r($match1);
			
			if(empty($match0[2]))
			{
				echo 'preg buff error no result!';
				continue;
			}
			foreach ($match0[2] as $key2=>$div)			
			{	
				$rssinfo->update =getrealtime(date("Y-m-d H:i:s",strtotime($match1[1][$key2])));
				if($rssinfo->update<$updatetime[$key])
				{
					echo "爬取到已经爬取文章，爬取结束! </br>\n";
					$change = false;	
					break;
					//continue;
				}				
				if($newdate<$rssinfo->update)
					$newdate = $rssinfo->update;
				$rssinfo->cat = trim($urlcat[$key]);
				$rssinfo->link = trim($match0[1][$key2]);
				$rssinfo->title = trim($match0[2][$key2]);
				//print_r($rssinfo);
				insertonlylink($rssinfo);
			}
		}
	}
	setupdatetime(true,$newdate,$authorname);
}
?>
