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
	

	
	$http = array("HttpConstants","CurlConfiguration","Curl","Http","HttpHeader","HttpHeaderCollection","HttpMessage","HttpRequest","HttpResponse","HttpRedirect","IHttpCache",
		"SigningKey","SigningRequest","Signature/Parameter","Signature/SignatureParameter","Signature/SignatureParameterBag");
		
		foreach($http as $file) {
			require_once(BASE_PATH.'/includes/Http/'.$file.".php");
		}
	

	require_once BASE_PATH.'/includes/Html/Html.php';
		
	require_once BASE_PATH.'/includes/Exception/PageNotFoundException.php';		
	
	require_once BASE_PATH.'/includes/Template.php';
	require_once BASE_PATH.'/includes/Application.php';
	require_once BASE_PATH.'/includes/ModuleLoader.php';
	require_once BASE_PATH.'/includes/Route.php';
	require_once BASE_PATH.'/includes/Router.php';
	require_once BASE_PATH.'/includes/Module.php';
	require_once BASE_PATH.'/includes/DocumentParser.php';

	require_once BASE_PATH.'/includes/Database/IDbResult.php';
	require_once BASE_PATH.'/includes/Database/DbResult.php';
	require_once BASE_PATH.'/includes/Database/MysqlDatabase.php';
	require_once BASE_PATH.'/includes/Database/DbSelectResult.php';
	require_once BASE_PATH.'/includes/Database/DbInsertResult.php';
	require_once BASE_PATH.'/includes/Database/QueryBuilder.php';

	require_once BASE_PATH.'/includes/Exception/DbException.php';


