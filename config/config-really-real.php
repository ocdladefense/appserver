<?php

define("USE_SALESFORCE_SLO_LOGOUT_ENDPOINT", true);
define("CORE_MODULE_CONNECTED_APP_NAME", "default");
define("PHP_SESSION_NAME", "OCDLA_APP_PHP_SESSION");
define("DEBUG", false);


// These are really connected applications
$oauth_config = array(
	"ocdla-sandbox-2.0" => array(
		"default" => false,
		"sandbox" => true, // Might be used to determine domain for urls
		"client_id" => "3MVG9gI0ielx8zHJY0OyNQvtaqwdWPrAdHZ5z.Bnwnvl4o7oBWjoCadvDRcIKPbL1qmwcKvY139zKMhO1R8yG",
		"client_secret" => "7AFEFEEF1D851DC3657CA16ED0B090E5360C49153927457B7EBD360F23A50B1F",
		"auth" => array(
			"saml" => array(),
			"oauth" => array(
				"usernamepassword" => array(
					"token_url" => "https://ocdla-sandbox--ocdpartial.my.salesforce.com/services/oauth2/token",
					"username" => "membernation@ocdla.com.ocdpartial",
					"password" => "asdi49ir4",
					"security_token" => "mT4ZN6OQmoF9SSZmx830AtpEM"
				),
				"webserver" => array(
					"token_url" => "https://ocdla-sandbox--ocdpartial.my.salesforce.com/services/oauth2/token",
					"auth_url" => "https://ocdla-sandbox--ocdpartial.my.salesforce.com/services/oauth2/authorize",	// Web server ouath flow has two oauth urls.
					"redirect_url" => "http://localhost/oauth/api/request",
					"callback_url" => "http://localhost/jobs"
				),
				"webserver-customer" => array(
					"token_url" => "https://ocdpartial-ocdla.cs169.force.com/services/oauth2/token",
					"auth_url" => "https://ocdpartial-ocdla.cs169.force.com/services/oauth2/authorize",	// Web server ouath flow has two oauth urls.
					"redirect_url" => "http://localhost/oauth/api/request",
					"callback_url" => "http://localhost/jobs"
				)
			)
		)
	),
	"ocdla-production-2.0" => array(
		"default" => true,
		"sandbox" => false, // Might be used to determine domain for urls
		"client_id" => "3MVG9fMtCkV6eLhf1Go62CS4_dHHdfBGBYnqJsRIWOEW916D66wfXS9Ih3BWHde2GZX1jUTIEOx13ymaB5_wd",
		"client_secret" => "742AF67F8F93EF8717A4E8AA9D8E9A0E7A56E1DF322082ED563016FB52A72BE0",
		"auth" => array(
			"saml" => array(),
			"oauth" => array(
				"usernamepassword" => array(
					"token_url" => "https://login.salesforce.com/services/oauth2/token",
					"username" => "membernation@ocdla.com",
					"password" => "asdi49ir4",
					"security_token" => "dI7EbhAadjZ7KB1UGeDoPSmh"
				),
				"webserver" => array(
					"token_url" => "https://ocdla.force.com/services/oauth2/token",
					"auth_url" => "https://ocdla.force.com/services/oauth2/authorize",	// Web server ouath flow has two oauth urls.
					"redirect_url" => "https://localhost/oauth/api/request",
					"callback_url" => "https://localhost/jobs"
				)
			)
		)
	)
);

// MYSQL
define("DB_HOST", "Localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "lod");








//OTHER STUFF
error_reporting(E_ALL & ~E_NOTICE);

define("ACTIVE_THEME","default");

//define("ADMIN_USER", true);


//File List 
$fileConfig = array(
	"path" 		=> path_to_uploads(),
	"fileTypes" => array("pdf", "docx", "doc", "txt", "jpg", "json")
);
