<?php

    if(!defined("BASE_PATH")) {
	    define("BASE_PATH",__DIR__);
    }
	define("ACTIVE_THEME","default");

    require_once BASE_PATH.'/includes/globals.php';

	if(file_exists(BASE_PATH.'/vendor/autoload.php')) {
		include BASE_PATH.'/vendor/autoload.php';
	}

    if(file_exists(BASE_PATH.'/config/config.php')){		
	    require_once BASE_PATH.'/config/config.php';
	    require_once BASE_PATH.'/includes/theme.inc';
	}

	require_once BASE_PATH.'/includes/http/Http.php';		
	require_once BASE_PATH.'/includes/http/HttpRequest.php';
	require_once BASE_PATH.'/includes/http/HttpResponse.php';
	
	require_once BASE_PATH.'/includes/Template.php';
    require_once BASE_PATH.'/includes/Application.php';
    require_once BASE_PATH.'/includes/ModuleLoader.php';
	require_once BASE_PATH.'/includes/Router.php';
	require_once BASE_PATH.'/includes/Module.php';
