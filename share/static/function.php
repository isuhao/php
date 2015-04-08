<?php

function dh_gen_static($name)
{
	global $DH_home_url,$DH_static_input_path,$DH_author_path,$DH_name;
	$DH_input_html  = $DH_static_input_path .$name.'.html';
	$DH_output_content = dh_file_get_contents("$DH_input_html");
	$DH_output_content = setshare($DH_output_content,'static.js');
	
	$DH_output_content = str_replace("%home%",$DH_home_url,$DH_output_content);
	$DH_output_content = str_replace("%DH_name%",$DH_name,$DH_output_content);	
	$DH_output_content = str_replace("%permalink%",$DH_home_url.'static/'.$name.'.html',$DH_output_content);	
	$DH_output_file = $DH_author_path.$name.'.html';
	dh_file_put_contents($DH_output_file,$DH_output_content);
}

?>
