<?php


	$saml_auth = "/var/simplesamlphp/lib/_autoload.php";
	if(file_exists($saml_auth)) {
		require_once($saml_auth);
	}

	require_once BASE_PATH.'/includes/user.inc';
	require_once BASE_PATH.'/includes/globals.php';
	require_once BASE_PATH.'/includes/User.php';


	if(file_exists(BASE_PATH.'/config/config.php')){		
		require_once BASE_PATH.'/config/config.php';
		require_once BASE_PATH.'/includes/theme.inc';
	}


	require_once(BASE_PATH.'/includes/Module/Module.php');

	$systemFiles = array("ClassName","XList", "IJson", "DateRange", "CoreModule","Session");

	foreach($systemFiles as $file) {
		require_once(BASE_PATH.'/includes/System/'.$file.".php");
	}



	// Require all of the files in the includes/File directory.
	$files = array("FileSystem","File", "FileList", "FileHandler", "PhpFileUpload");

	foreach($files as $file) {
		require_once(BASE_PATH.'/includes/File/'.$file.".php");
	}
	
	

	require_once BASE_PATH.'/includes/Url/Url.php';
	require_once BASE_PATH.'/includes/Url/RelativeUrl.php';
	require_once BASE_PATH.'/includes/Url/AbsoluteUrl.php';
	require_once BASE_PATH.'/includes/Url/QueryString.php';



	$store = array("IPaymentProcessor","Customer","Order","CreditCard","Payment");
	
	foreach($store as $file) {
		require_once(BASE_PATH.'/includes/Store/'.$file.".php");
	}
	

	$handlers = array("Handler","HtmlEmailHandler","ApplicationFileHandler","HttpResponseHandler","JsonHandler","HtmlDocumentHandler","JsonErrorHandler","HtmlStringHandler","HtmlErrorHandler", "XmlHandler");

	
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
	require_once BASE_PATH.'/includes/Translate.php';

	require_once BASE_PATH.'/includes/Routing/Path.php';	
	require_once BASE_PATH.'/includes/Routing/Route.php';
	require_once BASE_PATH.'/includes/Routing/Router.php';

	require_once BASE_PATH.'/includes/Module/Module.php';
	require_once BASE_PATH.'/includes/Module/ModuleLoader.php';

	require_once BASE_PATH.'/includes/System/CoreModule.php';

	require_once BASE_PATH. "/includes/MailMessage.php";





	
	// Step 1.) Move this over to the scraper module; rename it from car-scraper to something more appropriate (e.g., "web-scraper"), since it does more than just scrape cars.
	// require_once BASE_PATH.'/includes/DocumentParser.php';


	// Step 2.) This should be converted to library code.
	// require_once BASE_PATH.'/includes/Database/DbException.php';
	// require_once BASE_PATH. '/includes/Database/QueryException.php';
	// require_once BASE_PATH.'/includes/Database/IDbResult.php';
	
	// require_once BASE_PATH.'/includes/Database/mysql/DbResult.php';
	// require_once BASE_PATH.'/includes/Database/mysql/DbSelectResult.php';
	// require_once BASE_PATH.'/includes/Database/mysql/DbInsertResult.php';
	// require_once BASE_PATH.'/includes/Database/mysql/DbUpdateResult.php';
	// require_once BASE_PATH.'/includes/Database/mysql/DbDeleteResult.php';
	// require_once BASE_PATH.'/includes/Database/mysql/QueryBuilder.php';
	// require_once BASE_PATH. '/includes/Database/mysql/QueryStringParser.php';	
	// require_once BASE_PATH.'/includes/Database/mysql/Database.php';

	// require_once BASE_PATH.'/includes/Database/salesforce/Database.php';
	// require_once BASE_PATH.'/includes/Database/salesforce/SObject.php';
	// require_once BASE_PATH.'/includes/Database/salesforce/SObjectList.php';



	// Step 2.) This needs to be moved to the Database library.












	require_once BASE_PATH.'/includes/Application.php';
	

	// To be modules
	require_once BASE_PATH.'/includes/Store/Product.php';
	require_once BASE_PATH.'/includes/Store/Salesforce/ShoppingCart.php'; 
	require_once BASE_PATH.'/includes/Store/Salesforce/PaymentProcessor.php'; 
	
	require_once BASE_PATH.'/includes/Store/Square/ShoppingCart.php';
	require_once BASE_PATH.'/includes/Store/Square/PaymentProcessor.php'; 

	if(defined("COMPOSER_VENDOR_PATH")) {
		require COMPOSER_VENDOR_PATH.'/vendor/autoload.php';

	} else if(file_exists(BASE_PATH.'/vendor/autoload.php')) {

		include BASE_PATH.'/vendor/autoload.php';
	}
	
	
// end of file.
