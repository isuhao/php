<?php
function www_51oscar_com_php()
{
	$authorname='大众影评网';

	print_r($authorname);
	$authorurl='http://www.51oscar.com/';
	$url = array('http://news.51oscar.com//',
				 'http://review.51oscar.com/');				
	$urlcat= array('资讯','影评');
		
	print_r($url);
	//寻找各自的updatetime	
	$updatetime = getupdatetime($urlcat,$authorname);
	
	foreach ($url as $key=>$eachurl)
	{
		$change = true;
		$i=0;
		while($change&&$i<3)
		{
			$i++;
			$trueurl = $eachurl.$i.'/list.shtml';
				
			$buff = geturl($trueurl,$authorname);
			//如果失败，就使用就标记失败次数
			if(!$buff)
				break;

			echo "crawl ".$trueurl." </br>\n";
			//print_r($buff);
			preg_match_all('/<li><h4>(.*?)<\/h4><a href="(.*?)" target="_blank">(.*?)<\/a><\/li>/s',$buff,$match);
			//print_r($match);
			if(empty($match[2]))
			{			
				echo 'preg buff error no result!';
				continue;
			}	
			$rssinfo = new rssinfo();
			$rssinfo->author = $authorname;			
			foreach ($match[1] as $key2=>$div)			
			{
				$rssinfo->update = getrealtime($match[1][$key2]);
				if($rssinfo->update<$updatetime[$key])
				{
					echo "爬取到已经爬取文章，爬取结束! </br>\n";
					$change = false;	
					break;
					//continue;
				}
				$rssinfo->cat =trim($urlcat[$key]);
				$rssinfo->link =trim($match[2][$key2]);
				$rssinfo->title = trim($match[3][$key2]);
				//print_r($rssinfo);
				insertonlylink($rssinfo);
			}
		}
	}
}
?>