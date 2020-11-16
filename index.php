<?php

	$dir = __DIR__;
	
	if(file_exists('vendor/autoload.php')) {
		include 'vendor/autoload.php';
	}
	
use Http\HttpHeader;	
use Http\HttpRequest;
use Http\Http;

$loginType = "admin"; //Other option is community login.

// curl https://login.salesforce.com/services/Soap/u/50.0 -H "Content-Type: text/xml; charset=UTF-8" -H "SOAPAction: login" -d @login-community.txt | xmllint --format -
	$url = "https://login.salesforce.com/services/Soap/u/50.0";
	$request = new HttpRequest($url);

	

	$request->addHeader(new HttpHeader("Content-Type", "text/xml; charset=UTF-8"));
	$request->addHeader(new HttpHeader("SOAPAction", "login"));
	$pathToLogin = $loginType == "admin" ? $dir."/config/soap-login-admin-user.xml" : $dir."/config/soap-login-community-user.xml";
	$load = file_get_contents($pathToLogin);
	$request->setBody($load);
	
	
	
	$module = new SalesforceModule();
	
	$out = $module->testReport();
	
	print $out;
?>
<!doctype html>
<html>
	<head>
		<title>OCDLA WebApp</title>
		<meta charset="utf-8" />
		<link href="/content/assets/css/styles.css" type="text/css" rel="stylesheet" />
	</head>
	
	<body>
		<header>
		
		</header>
	
		<main>
		
			<img id="main-logo" alt="OCDLA Logo" src="content/images/logo.png" />
		
		</main>
	
		<footer>
		
		</footer>
	</body>

</html>