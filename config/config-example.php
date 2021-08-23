<?php
// MAIN CONFIGURATIONS.

// Force the current user to be a super-user.
// Works with globals.php/user_has_access().
define("USE_SALESFORCE_SLO_LOGOUT_ENDPOINT", true);

define("DEBUG", false);

//Optionally set the path to the root vendor directory if the appserver is not the root directory
//define("COMPOSER_VENDOR_PATH", "/foob");

//Optionally set the path the appserver if the appserver is in a subdirectory
define("APPSERVER_INSTALL_DIRECTORY", "  ");


// These are really connected applications
$oauth_config = array(

	"connected-app-name" => array(
		"default" => false,
		"sandbox" => true,
		"token_url" => "https://login.salesforce.com/services/oauth2/token",
		"client_id" => "your client id",
		"client_secret" => "your client secret",
		"username" => "your username",
		"password" => "our password",
		"security_token" => "your security token"
	),
	"trevors-dev-hub" => array(
		"default" => true,
		"sandbox" => true, // Might be used to determine domain for urls
		"client_id" => "",
		"client_secret" => "",
		"auth" => array(
			"saml" => array(),
			"oauth" => array(
				"usernamepassword" => array(
					"token_url" => "https://login.salesforce.com/services/oauth2/token",
					"username" => "",
					"password" => "",
					"security_token" => ""
				),
				"webserver" => array(
					"token_url" => "https://login.trevoruehlin-developer-edition.na85.force.com/services/oauth2/token",
					"auth_url" => "https://trevoruehlin-developer-edition.na85.force.com/services/oauth2/authorize",	// Web server ouath flow has two oauth urls.
					"redirect_url" => "http://localhost/oauth/api/request",
					"callback_url" => "http://localhost/test/1",
				)
			)
		)
	)
);

// MySQL Database Connection.
//  Can also use MariaDB.
define("DB_HOST", "127.0.0.1");
define("DB_USER", "");
define("DB_PASS", "");
define("DB_NAME", "");


// Error reporting.
// Comment to use values from php.ini.
error_reporting(E_ALL & ~E_NOTICE);


// Default theme.
// Which theme to use to render text/html callbacks.
define("ACTIVE_THEME","default");


// Key store.
/* $keyStore = array(
	"keyId" => "sharedSecret"
);
*/
$keyStore = array(
	"97db7d6d-219e-47b5-a2b6-54c0906131b1" => "+6tgPmw+I9JDkvx4dk3NA+m4gH96z2CVF6KGMvMmz/I="
);






// MODULE CONFIGURATIONS.


//Salesforce
define("SALESFORCE_USERNAME", "");
define("SALESFORCE_PASSWORD", "");
define("SALESFORCE_CLIENT_ID", "");
define("SALESFORCE_CLIENT_SECRET", "");
define("SALESFORCE_REDIRECT_URI", "");
define("SALESFORCE_LOGIN_URL", "");
define("SALESFORCE_SECURITY_TOKEN", "");
define("ORG_WSDL", array(
	"myOrg"	=>	"../config/wsdl/example.wsdl"
));

// Authorize.net
define("AUTHORIZENET_SANDBOX_URL","");
define("AUTHORIZENET_MERCHANT_LOGIN_ID", "");
define("AUTHORIZENET_MERCHANT_TRANSACTION_KEY", "");
define("AUTHORIZENET_ACCOUNT_ID","");
define("AUTHORIZENET_TEST_API_ENDPOINT","");
define("AUTHORIZENET_PRODUCTION_API_ENDPOINT","");
define("AUTHORIZENET_ORG_ID","");
define("AUTHORIZENET_USERNAME","");



// CyberSource
// define("CYBERSOURCE_SANDBOX_URL","https://developer.cybersource.com/api-reference-assets/index.html");
// define("CYBERSOURCE_MERCHANT_ID","");
// define("CYBERSOURCE_ORG_ID","");
// define("CYBERSOURCE_USERNAME","");
// define("CYBERSOURCE_ACCOUNT_ID","");
// define("CYBERSOURCE_TEST_API_ENDPOINT","");
// define("CYBERSOURCE_PRODUCTION_API_ENDPOINT","");


// Square
define("SQUARE_API_KEY","");
define("SQUARE_API_VERSION","2020-06-25");
define("SQUARE_APPLICATION_ID","sandbox-sq");



//The shared secret key and the id that holds the reference to the shared secret key on cybersource.com
// define("CYBERSOURCE_SHARED_SECRET_KEY_ID","");
// define("CYBERSOURCE_SHARED_SECRET_KEY","");

//The access key and secret key for the "visualForce checkout" transaction key, for the "website checkout" profile
// define("CYBERSOURCE_TRANSACTION_KEY_ACCESS_KEY","");
// define("CYBERSOURCE_TRANSACTION_KEY_SECRET_KEY","");






//Trevors cybersource keys ===========================================================================================
//cybersource.com
define("CYBERSOURCE_SANDBOX_URL","https://developer.cybersource.com/api-reference-assets/index.html");
define("CYBERSOURCE_MERCHANT_ID","trevor_uehlin");
define("CYBERSOURCE_ORG_ID","trevor_uehlin");
define("CYBERSOURCE_USERNAME","trevor_uehlin");
define("CYBERSOURCE_ACCOUNT_ID","trevor_uehlin_acct");
define("CYBERSOURCE_TEST_API_ENDPOINT","https://apitest.cybersource.com");
define("CYBERSOURCE_PRODUCTION_API_ENDPOINT","api.cybersource.com");
define("CYBERSOURCE_TEST_HOST","apitest.cybersource.com");
define("CYBERSOURCE_PRODUCTION_HOST","api.cybersource.com");




//The shared secret key and the id that holds the reference to the shared secret key on cybersource.com
define("CYBERSOURCE_SHARED_SECRET_KEY_ID","97db7d6d-219e-47b5-a2b6-54c0906131b1");
define("CYBERSOURCE_SHARED_SECRET_KEY","+6tgPmw+I9JDkvx4dk3NA+m4gH96z2CVF6KGMvMmz/I=");

//The cybersource signature algorithm used to ????
define("CYBERSOURCE_SIGNATURE_ALGORITHM","HmacSHA256");

//The access key and secret key for the "visualForce checkout" transaction key, for the "website checkout" profile
define("CYBERSOURCE_TRANSACTION_KEY_ACCESS_KEY","85339ed8a0543feeadeda49f0eeeefbc");
define("CYBERSOURCE_TRANSACTION_KEY_SECRET_KEY","573ad151cf204f48a29fcc72f35b62be09b38baca24d4ea89906d920dde1cd1233327b34ed21418286f7a62f646760e6bd428ec8b8af481e886ae2a8fccbed590a20bf6bd3f648f081cf9f047709f61ff236d8b13b9e4a6c9cea6a50ab43399bd1a633a447014300a7b91b3c584e181ef30e053c39e846bf9597f074350c0cf8");




//File List 
$fileConfig = array(
	"path" 		=> path_to_uploads(),
	"fileTypes" => array("pdf", "docx", "doc", "txt", "jpg", "json"),
	"userId"    => "user123",//$postData->userId,
	"appId"	    => "app123"//$postData->appId
);

