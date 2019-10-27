<?php

	define("BASE_PATH",__DIR__);
	define("ACTIVE_THEME","default");

	if(file_exists(BASE_PATH.'/vendor/autoload.php')) {
		include BASE_PATH.'/vendor/autoload.php';
	}
		
	require_once BASE_PATH.'/config/config.php';
	require_once BASE_PATH.'/includes/theme.inc';
	
		
	require_once BASE_PATH.'/includes/HTTPRequest.php';
	require_once BASE_PATH.'/includes/HTTPResponse.php';
	
	require_once BASE_PATH.'/includes/Template.php';
	require_once BASE_PATH.'/includes/AppRouter.php';
