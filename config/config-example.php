<?php

//Salesforce
define("SALESFORCE_USERNAME", "");
define("SALESFORCE_PASSWORD", "");
define("CLIENT_ID", "");
define("CLIENT_SECRET", "");
define("REDIRECT_URI", "");
define("LOGIN_URI", "");

//Authorize.net

define("AUTHORIZENET_SANDBOX_URL","");
define("AUTHORIZENET_MERCHANT_LOGIN_ID", "");
define("AUTHORIZENET_MERCHANT_TRANSACTION_KEY", "");

define("AUTHORIZENET_ACCOUNT_ID","");
define("AUTHORIZENET_TEST_API_ENDPOINT","");
define("AUTHORIZENET_PRODUCTION_API_ENDPOINT","");
define("AUTHORIZENET_ORG_ID","");
define("AUTHORIZENET_USERNAME","");

//DATABASE 
define("DB_HOST", "");
define("DB_USER", "");
define("DB_PASS", "");
define("DB_NAME", "");

//cybersource.com
define("CYBERSOURCE_SANDBOX_URL","https://developer.cybersource.com/api-reference-assets/index.html");
define("CYBERSOURCE_MERCHANT_ID","");
define("CYBERSOURCE_ORG_ID","");
define("CYBERSOURCE_USERNAME","");
define("CYBERSOURCE_ACCOUNT_ID","");
define("CYBERSOURCE_TEST_API_ENDPOINT","");
define("CYBERSOURCE_PRODUCTION_API_ENDPOINT","");

// Used by SignatureKey
// put your own values here...
$keyStore = array(
	"keyId" =>
	"sharedSecret"
);


//The shared secret key and the id that holds the reference to the shared secret key on cybersource.com
define("CYBERSOURCE_SHARED_SECRET_KEY_ID","");
define("CYBERSOURCE_SHARED_SECRET_KEY","");

//The access key and secret key for the "visualForce checkout" transaction key, for the "website checkout" profile
define("CYBERSOURCE_TRANSACTION_KEY_ACCESS_KEY","");
define("CYBERSOURCE_TRANSACTION_KEY_SECRET_KEY","");

//OTHER STUFF
error_reporting(E_ALL & ~E_NOTICE);

define("ACTIVE_THEME","default");

?>