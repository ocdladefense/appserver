<?php
require '../bootstrap.php';           

session_start();


//$cient = new Client();
//$cient->machineName;
//#client->deviceName;
#client->macAddress;
#client->IpAddress;
#client->features;
//$client2 = new TerminalClient;
//client2->hasFeatureScreen = false;
$request = HTTPRequest::newFromEnvironment();

// $request->setHeader("Accept","*/*");

$application = new Application();

$application->setModuleLoader(new ModuleLoader());
$router = new Router($application);



$application->setRouter($router);

$application->setRequest($request);



try {
	$resp = $router->run($application->getRequestHeader("Request-URI"));
} catch(PageNotFoundException $e) {
	$resp = new HTTPResponse();
	$resp->setNotFoundStatus();
	$resp->setBody($e->getMessage());
} catch(Exception $e) {
	$resp = new HTTPResponse();
	$resp->setErrorStatus();
	$resp->setBody($e->getMessage());
}


$application->setResponse($resp);


$application->secure();



$application->send();