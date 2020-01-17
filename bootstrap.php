<?php


	if(!defined("BASE_PATH")) {
		define("BASE_PATH",__DIR__);
	}

	if(file_exists(BASE_PATH.'/config/config.php')){		
		require_once BASE_PATH.'/config/config.php';
		require_once BASE_PATH.'/includes/theme.inc';
	}

	require_once BASE_PATH.'/includes/globals.php';

	if(file_exists(BASE_PATH.'/vendor/autoload.php')) {
		include BASE_PATH.'/vendor/autoload.php';
	}

	require_once BASE_PATH.'/includes/Url/Url.php';
	require_once BASE_PATH.'/includes/Http/Http.php';
	require_once BASE_PATH.'/includes/Http/HttpRequest.php';
	require_once BASE_PATH.'/includes/Http/HttpResponse.php';
	require_once BASE_PATH.'/includes/Http/HttpRedirect.php';

	require_once BASE_PATH.'/includes/Html/Html.php';
		
	require_once BASE_PATH.'/includes/Exception/PageNotFoundException.php';		
	
	require_once BASE_PATH.'/includes/Template.php';
	require_once BASE_PATH.'/includes/Application.php';
	require_once BASE_PATH.'/includes/ModuleLoader.php';
	require_once BASE_PATH.'/includes/Route.php';
	require_once BASE_PATH.'/includes/Router.php';
	require_once BASE_PATH.'/includes/Module.php';
	require_once BASE_PATH.'/includes/DocumentParser.php';
