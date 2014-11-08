<?php
/////////////////////////////////////////////////////
/// 函数名称：gen_author
/// 函数作用：产生静态的最新的文章列表页面
/// 函数作者: DH
/// 作者地址: http://dhblog.org
/////////////////////////////////////////////////////

header('Content-Type:text/html;charset= UTF-8'); 
set_time_limit(120);

#需要使用的基础函数
include("../config.php");
include("../common/base.php");
include("../common/dbaction.php");
include("../common/page_navi.php");
$conn=mysql_connect ($dbip, $dbuser, $dbpasswd) or die('数据库服务器连接失败：'.mysql_error());
mysql_select_db($dbname, $conn) or die('选择数据库失败');
mysql_query("set names utf8;");
dh_gen_author();
dh_gen_static('aboutus');
dh_gen_static('coperation');
dh_gen_static('help');
dh_gen_static('opinion');
dh_gen_static('mianze');
dh_gen_static('links');
mysql_close($conn);

function dh_gen_author()
{
	global $DH_home_url,$DH_input_path,$DH_author_path,$DH_name;
	if (!file_exists($DH_author_path))  
		mkdir($DH_author_path,0777);
	$DH_input_html  = $DH_input_path . 'genauthor/author.html';
	$DH_output_content = dh_file_get_contents("$DH_input_html");
	$DH_output_content = setshare($DH_output_content,'');
	$DH_output_content = str_replace("%home%",$DH_home_url,$DH_output_content);
	$DH_output_content = str_replace("%DH_name%",$DH_name,$DH_output_content);	
	
	$sql="select * from author where rss>0 order by updatetime desc";
	$authorlist=dh_get_author($sql);
	
	$DH_output_content = str_replace("%list_each%",$authorlist,$DH_output_content);
	
	$DH_output_file = $DH_author_path.'author.html';
	dh_file_put_contents($DH_output_file,$DH_output_content);		
}

function dh_gen_static($name)
{
	global $DH_home_url,$DH_input_path,$DH_author_path,$DH_name;
	$DH_input_html  = $DH_input_path . 'genauthor/'.$name.'.html';
	$DH_output_content = dh_file_get_contents("$DH_input_html");
	$DH_output_content = setshare($DH_output_content,'');
	
	$DH_output_content = str_replace("%home%",$DH_home_url,$DH_output_content);
	$DH_output_content = str_replace("%DH_name%",$DH_name,$DH_output_content);	
	$DH_output_file = $DH_author_path.$name.'.html';
	dh_file_put_contents($DH_output_file,$DH_output_content);
}

function dh_get_author($sql)
{
	global $linkquality,$linkway,$DH_home_url;
	$datetoday =getupdatebegin(2);
	$results=dh_mysql_query($sql);
	$liout='<li> <span class="moviemeta">序号</span> <span class="lefttop40v2">网站</span><span class="rt500v2">近日资源</span> <span class="rt420v2">近日资讯</span> <span class="rt340v2">全部资源</span> <span class="rt260v2">全部资讯</span> <span class="rt180v2">失败次数</span><span class="rt5v2" >最后更新时间</span></li>';
	if($results)
	{			
		while($row = mysql_fetch_array($results))
		{
			echo "\n".'genauthor:'.$row['name'];
			//查找资源的数目
			$sqlcount="select count(*) from link where author='".$row['name']."' and (linkway=6 or linkway=7)";
			$lres=dh_mysql_query($sqlcount);
			$linkcount1 = mysql_fetch_array($lres);
			$sqlcount="select count(*) from link where author='".$row['name']."' and !(linkway=6 or linkway=7)";
			$lres=dh_mysql_query($sqlcount);
			$linkcount2 = mysql_fetch_array($lres);
			
			//先处理updatetime
			$authorname=$row['name'];
			$sqlup="select max(updatetime) from link where author='$authorname'";
			$lres=dh_mysql_query($sqlup);
			$lupdatetime = mysql_fetch_array($lres);			
			$updatef = date("Y-m-d H:i:s",strtotime($lupdatetime));
			$sqlup="update author set updatetime = '$updatef' where name='$authorname'";
			$lres=dh_mysql_query($sqlup);			
			
			$sqlcount="select count(*) from link where author='$authorname' and updatetime >= '$datetoday' and (linkway=6 or linkway=7)";
			$lres=dh_mysql_query($sqlcount);
			$linkcount3 = mysql_fetch_array($lres);
			$sqlcount="select count(*) from link where author='$authorname' and updatetime >= '$datetoday' and !(linkway=6 or linkway=7)";
			$lres=dh_mysql_query($sqlcount);
			$linkcount4 = mysql_fetch_array($lres);
			
			//$numlink = $linkcount1[0]+$linkcount2[0];
			
			$name = rawurlencode($row['name']);
			$lieach = '<li> <span class="moviemeta">['.$row['id'].']</span> <span class="lefttop40v2"> <a href="'.$row['url'].'" target="_blank">'.$row['name'].'</a> (<a href="'.$DH_home_url.'search.php?aid='.$name.'" target="_blank">资源列表</a>)</span><span class="rt500v2">'.$linkcount3[0].'</span><span class="rt420v2">'.$linkcount4[0].'</span><span class="rt340v2">'.$linkcount1[0].'</span><span class="rt260v2">'.$linkcount2[0].'</span> <span class="rt180v2">'.$row['failtimes'].'</span><span class="rt5v2" > '.$updatef.'</span></li>';			
			$liout.= $lieach;
		}
	}
	return $liout;
}


function setshare($DH_output_content,$js)
{
	global $DH_home_url,$DH_input_path,$DH_html_path,$DH_name,$DH_name_des;
	$DH_share_output_path = $DH_input_path.'top/';
	$DH_input_html  = $DH_share_output_path . 'meta.html';
	$DH_output_meta = dh_file_get_contents("$DH_input_html");
	$DH_input_html  = $DH_input_path . 'genauthor/head.html';
	$DH_output_head = dh_file_get_contents("$DH_input_html");	
	$DH_input_html  = $DH_share_output_path . 'foot.html';
	$DH_output_foot = dh_file_get_contents("$DH_input_html");
	
	$DH_output_content = str_replace("%meta%",$DH_output_meta,$DH_output_content);
	$DH_output_content = str_replace("%head%",$DH_output_head,$DH_output_content);
	$DH_output_content = str_replace("%foot%",$DH_output_foot,$DH_output_content);		
	$DH_output_content = str_replace("%DH_name%",$DH_name,$DH_output_content);
	$DH_output_content = str_replace("%DH_name_des%",$DH_name_des,$DH_output_content);
	
	return $DH_output_content;
}
?>