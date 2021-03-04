<?php


	$saml_auth = "/var/simplesamlphp/lib/_autoload.php";
	if(file_exists($saml_auth)) {
		require_once($saml_auth);
	}
	require_once BASE_PATH.'/includes/globals.php';
	require_once BASE_PATH.'/includes/User.php';

	if(file_exists(BASE_PATH.'/config/config.php')){		
		require_once BASE_PATH.'/config/config.php';
		require_once BASE_PATH.'/includes/theme.inc';
	}


	
	require_once BASE_PATH.'/includes/System/ClassName.php';
	require_once BASE_PATH.'/includes/System/XList.php';
	require_once BASE_PATH.'/includes/System/IJson.php';
	require_once BASE_PATH.'/includes/System/DateRange.php';

	


	$files = array("FileSystem","File", "FileList", "FileHandler", "PhpFileUpload");

	foreach($files as $file) {
		require_once(BASE_PATH.'/includes/File/'.$file.".php");
	}
	
	

	require_once BASE_PATH.'/includes/Url/Url.php';
	require_once BASE_PATH.'/includes/Url/RelativeUrl.php';
	require_once BASE_PATH.'/includes/Url/AbsoluteUrl.php';
	require_once BASE_PATH.'/includes/Url/QueryString.php';


	
	$http = array("HttpConstants","CurlConfiguration","Curl","Http","HttpHeader","HttpHeaderCollection","HttpMessage","HttpRequest","HttpResponse","HttpRedirect","IHttpCache",
	"SigningKey","SigningRequest","Signature/Parameter","Signature/SignatureParameter","Signature/SignatureParameterBag","BodyPart");
	
	foreach($http as $file) {
		require_once(BASE_PATH.'/includes/Http/'.$file.".php");
	}



	$store = array("IPaymentProcessor","Customer","Order","CreditCard","Payment");
	
	foreach($store as $file) {
		require_once(BASE_PATH.'/includes/Store/'.$file.".php");
	}
	
	$handlers = array("Handler","ApplicationFileHandler","HttpResponseHandler","JsonHandler","HtmlDocumentHandler","JsonErrorHandler","HtmlStringHandler","HtmlErrorHandler");
	
	foreach($handlers as $file) {
		require_once(BASE_PATH.'/includes/Handlers/'.$file.".php");
	}

	require_once BASE_PATH.'/includes/Html/Html.php';
		
	require_once BASE_PATH.'/includes/Exception/PageNotFoundException.php';
	require_once BASE_PATH.'/includes/Exception/SalesforceAccessException.php';
	require_once BASE_PATH.'/includes/Exception/SalesforceAuthException.php';		



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
	require_once BASE_PATH.'/includes/Database/mysql/Database.php';
	require_once BASE_PATH.'/includes/Database/salesforce/Database.php';
	require_once BASE_PATH.'/includes/Database/DbSelectResult.php';
	require_once BASE_PATH.'/includes/Database/DbInsertResult.php';
	require_once BASE_PATH.'/includes/Database/DbUpdateResult.php';
	require_once BASE_PATH.'/includes/Database/DbDeleteResult.php';
	require_once BASE_PATH.'/includes/Database/QueryBuilder.php';
	require_once BASE_PATH.'/includes/Database/SObject.php';
	require_once BASE_PATH.'/includes/Database/SObjectList.php';




	require_once BASE_PATH.'/includes/Exception/DbException.php';

	require_once BASE_PATH.'/includes/Application.php';

	require_once BASE_PATH.'/includes/Salesforce/Salesforce.php';
	require_once BASE_PATH.'/includes/Salesforce/RestApiResult.php';	

	require_once BASE_PATH.'/includes/Salesforce/OAuthRequest.php';
	require_once BASE_PATH.'/includes/Salesforce/OAuthResponse.php';
	require_once BASE_PATH.'/includes/Salesforce/RestApiRequest.php';
	require_once BASE_PATH.'/includes/Salesforce/RestApiResponse.php';

	
	require_once BASE_PATH.'/includes/Store/Product.php';
	require_once BASE_PATH. '/includes/Database/QueryStringParser.php';
	require_once BASE_PATH. '/includes/Exception/QueryException.php';

	//to be modules
	require_once BASE_PATH.'/includes/Store/Salesforce/ShoppingCart.php'; 
	require_once BASE_PATH.'/includes/Store/Salesforce/PaymentProcessor.php'; 
	
	require_once BASE_PATH.'/includes/Store/Square/ShoppingCart.php';
	require_once BASE_PATH.'/includes/Store/Square/PaymentProcessor.php'; 



	if(file_exists(BASE_PATH.'/vendor/autoload.php')) {
		include BASE_PATH.'/vendor/autoload.php';
	}
