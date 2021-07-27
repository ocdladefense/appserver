<?php

error_reporting(E_ALL & ~E_NOTICE);

define("ACTIVE_THEME","default");
define("ADMIN_USER", false);

// Connected app configuration.
$oauth_config = array(
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
					"redirect_url" => "https://trust.ocdla.org/oauth/api/request",
					"callback_url" => "https://trust.ocdla.org/jobs"
				)
			)
		)
	),
	"ocdla-sandbox-2.0" => array(
		"default" => false,
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
				// Admin endpoints for webserver flow.
				"webserver-admin" => array(
					"token_url" => "https://test.salesforce.com/services/oauth2/token",
					"auth_url" => "https://test.salesforce.com/services/oauth2/authorize",	// Web server ouath flow has two oauth urls.
					"redirect_url" => "https://trust.ocdla.org/oauth/api/request",
					"callback_url" => "https://trust.ocdla.org/jobs"
				),
				// Customer endpoints for webserver flow.
				"webserver" => array(
					"token_url" => "https://ocdpartial-ocdla.cs169.force.com/services/oauth2/token",
					"auth_url" => "https://ocdpartial-ocdla.cs169.force.com/services/oauth2/authorize",	// Web server ouath flow has two oauth urls.
					"redirect_url" => "https://trust.ocdla.org/oauth/api/request",
					"callback_url" => "https://trust.ocdla.org/jobs"
				)

			)
		)
	)
		
);

// MYSQL
//  Can also use MariaDB.
define("DB_HOST", "172.31.47.173");
define("DB_NAME", "appocdla");
define("DB_USER", "appocdlaprod");
define("DB_PASS", "8a9F313a57");


// Accepted file upload tyoes.
$fileConfig = array(
	"path" 		=> path_to_uploads(),
	"fileTypes" => array("pdf", "docx", "doc", "txt", "jpg", "json")
);
