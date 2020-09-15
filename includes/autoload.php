<?php

	

	if(file_exists(BASE_PATH.'/config/config.php')){		
		require_once BASE_PATH.'/config/config.php';
		require_once BASE_PATH.'/includes/theme.inc';
	}

	require_once BASE_PATH.'/includes/globals.php';

	if(file_exists(BASE_PATH.'/vendor/autoload.php')) {
		include BASE_PATH.'/vendor/autoload.php';
	}
	
	
	require_once BASE_PATH.'/includes/System/ClassName.php';
	require_once BASE_PATH.'/includes/System/XList.php';
	require_once BASE_PATH.'/includes/System/IJson.php';

	


	$files = array("FileSystem","File", "FileList", "FileHandler", "PhpFileUpload");

	foreach($files as $file) {
		require_once(BASE_PATH.'/includes/File/'.$file.".php");
	}
	
	

	require_once BASE_PATH.'/includes/Url/Url.php';
	require_once BASE_PATH.'/includes/Url/RelativeUrl.php';
	require_once BASE_PATH.'/includes/Url/AbsoluteUrl.php';
	require_once BASE_PATH.'/includes/Url/QueryString.php';


	
	$http = array("HttpConstants","CurlConfiguration","Curl","Http","HttpHeader","HttpHeaderCollection","HttpMessage","HttpRequest","HttpResponse","HttpRedirect","IHttpCache",
	"SigningKey","SigningRequest","Signature/Parameter","Signature/SignatureParameter","Signature/SignatureParameterBag");
	
	foreach($http as $file) {
		require_once(BASE_PATH.'/includes/Http/'.$file.".php");
	}



	$store = array("ShoppingCart","IPaymentProcessor","Customer","Order","CreditCard","Payment");
	
	foreach($store as $file) {
		require_once(BASE_PATH.'/includes/Store/'.$file.".php");
	}
	
	$handlers = array("Handler","ApplicationFileHandler","JsonHandler","HtmlDocumentHandler","JsonErrorHandler","HtmlStringHandler","HtmlErrorHandler");
	
	foreach($handlers as $file) {
		require_once(BASE_PATH.'/includes/Handlers/'.$file.".php");
	}

	require_once BASE_PATH.'/includes/Html/Html.php';
		
	require_once BASE_PATH.'/includes/Exception/PageNotFoundException.php';		



	require_once BASE_PATH.'/includes/Template.php';
	require_once BASE_PATH.'/includes/Theme.php';	
	require_once BASE_PATH.'/includes/IRenderable.php';


	require_once BASE_PATH.'/includes/Routing/Path.php';	
	require_once BASE_PATH.'/includes/Routing/Route.php';
	require_once BASE_PATH.'/includes/Routing/Router.php';


	require_once BASE_PATH.'/includes/Module/Module.php';
	require_once BASE_PATH.'/includes/Module/ModuleLoader.php';
	require_once BASE_PATH.'/includes/DocumentParser.php';

	require_once BASE_PATH.'/includes/System/CoreModule.php';

	require_once BASE_PATH.'/includes/Database/IDbResult.php';
	require_once BASE_PATH.'/includes/Database/DbResult.php';
	require_once BASE_PATH.'/includes/Database/MysqlDatabase.php';
	require_once BASE_PATH.'/includes/Database/DbSelectResult.php';
	require_once BASE_PATH.'/includes/Database/DbInsertResult.php';
	require_once BASE_PATH.'/includes/Database/DbUpdateResult.php';
	require_once BASE_PATH.'/includes/Database/DbDeleteResult.php';
	require_once BASE_PATH.'/includes/Database/QueryBuilder.php';

	require_once BASE_PATH.'/includes/Exception/DbException.php';

	require_once BASE_PATH.'/includes/Application.php';

