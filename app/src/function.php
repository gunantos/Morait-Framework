<?php

	if(! function_exists('base_url'))
	{
		function base_url()
		{
			$url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
		 	$url .= "://".$_SERVER['HTTP_HOST'];
		 	$url .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
		 	return $url;
		}
	}
	
	if(! function_exists('mylog'))
	{
		function mylog($text, $level='i', $file='logs') {
	    	switch (strtolower($level)) {
	        case 'e':
	        case 'error':
	            $level='ERROR';
	            break;
	        case 'i':
	        case 'info':
	            $level='INFO';
	            break;
	        case 'd':
	        case 'debug':
	            $level='DEBUG';
	            break;
	        default:
	            $level='INFO';
	    	}
	    	error_log(date("[Y-m-d H:i:s]")."\t[".$level."]\t[".basename(__FILE__)."]\t".$text."\n", 3, $file);
		}
	}

	if(! function_exists('check_login'))
	{
		function check_login()
		{
			if(isset($_SESSION['id_user']))
			{
				if(empty($_SESSION['id_user']))
				{
					header("location: ". base_url());
				}
			}else{
				header("location: ". base_url());
			}
		}
	}

?>