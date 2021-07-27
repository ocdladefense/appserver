<?php
// MAIN CONFIGURATIONS.




// Force the current user to be a super-user.
// Works with globals.php/user_has_access().
// define("ADMIN_USER", true);

/**
 * Connect to multiple endpoints, but
 *  specify one as the "default."
 *
 * Example in a module:
 * $api = $this->loadForceApi("ocdla-sandbox");
 *
 * to connect to the default use the existing syntax:
 * $api = $this->loadForceApi();
 */
$oauth_config = array(

	"highscope-sandbox-2.0" => array(
		"default" => false,
		"sandbox" => true, // Might be used to determine domain for urls
		"client_id" => "3MVG9M43irr9JAuw0AXX2Tv0rm2UVDmpqZEhTKfXLL3i_n6iDfnPEcomIPBw3qDKrIqJQExR4iQRHFLqIOG85",
		"client_secret" => "9011FD5BF2DB22599F5FD2F5C293A7AC4DCDE8A3CF36BB1FB09E414F597AA6DD",
		"auth" => array(
			"saml" => array(),
			"oauth" => array(
				"usernamepassword" => array(
					"token_url" => "https://test.salesforce.com/services/oauth2/token",
					"username" => "jbernal@highscope.org.ltdglobal",
					"password" => "brjcis12",
					"security_token" => "oDMvMlASM8R6H7Uf5o0FjLxG"
				),
				"webserver" => array(
					"token_url" => "https://ltdglobal-customer.cs197.force.com/services/oauth2/token",
					"auth_url" => "https://ltdglobal-customer.cs197.force.com/services/oauth2/authorize",	// Web server ouath flow has two oauth urls.
					"redirect_url" => "https://app.highscope.org/oauth/api/request",
					"callback_url" => "https://app.highscope.org/my-account"
				)
			)
		)
	),
	"ocdla-jobs" => array(

			"default" => true,

			"sandbox" => true, // Might be used to determine domain for urls

			"client_id" => "3MVG9gI0ielx8zHLKXlEe15aGYjrfRJ2j60D4kIpoTDqx2YSaK2xqoA3wU77thTRImxT5RSq_obv6EOQaZBm2",

			"client_secret" => "3B61242366DCD4812DAA4C63A5FDF9C76F619528547B87A950A1584CEAB825E1",

			"auth" => array(

					"saml" => array(),

					"oauth" => array(

							"usernamepassword" => array(

									"token_url" => "https://test.salesforce.com/services/oauth2/token",

									"username" => "membernation@ocdla.com.ocdpartial",

									"password" => "asdi49ir4",

									"security_token" => "mT4ZN6OQmoF9SSZmx830AtpEM"

							),

							"webserver" => array(

									"token_url" => "https://test.salesforce.com/services/oauth2/token",

									"auth_url" => "https://test.salesforce.com/services/oauth2/authorize",  // Web server ouath flow has two oauth urls.

									"redirect_url" => "https://trust.ocdla.org/oauth/api/request",

									"callback_url" => "http://trust.ocdla.org/jobs"

							)

					)

			)

	)
);




// MySQL Database Connection.
//  Can also use MariaDB.
define("DB_HOST", "172.31.47.173");
define("DB_NAME", "appocdla");
define("DB_USER", "appocdlaprod");
define("DB_PASS", "8a9F313a57");



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

define("ADMIN_USER",true);


// Will need OCDLA to establish an account for this.
// Use OCDLA's API key.
define("CLOUD_CONVERT_SANDBOX_API_KEY","foobar");


//File List 
$fileConfig = array(
	"path" 		=> path_to_uploads(),
	"fileTypes" => array("pdf", "docx", "doc", "txt", "jpg", "json"),
	"userId"    => "user123",//$postData->userId,
	"appId"	    => "app123"//$postData->appId
);
