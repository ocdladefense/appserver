<?php

# System directories.
define("CACHE_DIR",BASE_PATH."/cache");
define("TMP_DIR",BASE_PATH."/tmp");

# Extra options.
define("USE_SALESFORCE_SLO_LOGOUT_ENDPOINT", false);
define("CORE_MODULE_CONNECTED_APP_NAME", "default");

# Whether to display stack traces for exceptions.
define("DEBUG", true);

# Whether to check module / route access.
define("CHECK_ACCESS", true);

# Masquerade as the admin user.
define("ADMIN_USER", false);

# Session options; all are optional and should default to PHP.ini values if not set.
define("SESSION_DIR", "/var/lib/php/sessions-dev");
define("SESSION_NAME","ocdla_dev_id");
define("SESSION_COOKIE_SECURE", true);
define("SESSION_COOKIE_DOMAIN", "appdev.ocdla.org");

# Authorize.net module configurations.
define("AUTHORIZE_DOT_NET_MERCHANT_ID","example-value");
define("AUTHORIZE_DOT_NET_TRANSACTION_KEY","example-value");
define("AUTHORIZE_DOT_NET_USE_PRODUCTION_ENDPOINT", false);

# Localize URL settings.
define("APP_URL", "https://appdev.ocdla.org");
define("STORE_URL","https://ocdpartial-ocdla.cs198.force.com");
define("ORG_URL","https://test.ocdla.org");
define("LOD_URL", "https://lodtest.ocdla.org");


//OTHER STUFF
error_reporting(E_ALL & ~E_NOTICE);
define("ACTIVE_THEME","default");



// These are really connected applications
$oauth_config = array(
	"ocdla-app" => array(
		"default" => true,
		"sandbox" => true, // Might be used to determine domain for urls
		"client_id" => "example-client-id",
		"client_secret" => "example-client-secret",
		"auth" => array(
			"saml" => array(),
			"oauth" => array(
				"usernamepassword" => array(
					"token_url" => "https://test.salesforce.com/services/oauth2/token",
					"username" => "example@example.com",
					"password" => "example123",
					"security_token" => "exampleabc",
					"cache" => "application"
				),
				"webserver" => array(
					"token_url" => "https://ocdpartial-ocdla.cs198.force.com/services/oauth2/token",
					"auth_url" => "https://ocdpartial-ocdla.cs198.force.com/services/oauth2/authorize",
					"redirect_url" => "https://appdev.ocdla.org/oauth/api/request",
					"callback_url" => "https://appdev.ocdla.org/jobs",
					"cache" => "session"
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

