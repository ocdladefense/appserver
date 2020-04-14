<?php

require '../bootstrap.php';     
     

session_start();
ini_set("max_execution_time","18000");
// set_time_limit(0);

$request = \Http\HttpRequest::newFromEnvironment();

$app = new Application();
$app->setModuleLoader(new ModuleLoader());

$router = new Router($app->getModules());
$app->setRouter($router);
$app->setRequest($request);

try {

	$out = $app->run($request->getRequestUri());

	if(gettype($out) == "object" && get_class($out) == "HttpResponse") {
		$resp = $out;
	} else if(gettype($out) === "string" || gettype($out) === "array" || gettype($out) === "object") {
		$resp = $app->getAsHttpResponse($out);
	} else if(get_class($out) == "HttpRedirect") {
		$app->setResponse($out);
		// $app->secure();
		$app->send();
	}
	
} catch(PageNotFoundException $e) {

	$resp = new HttpResponse();
	$resp->setNotFoundStatus();
	$resp->setBody($e->getMessage());
	
} /* catch(Exception $e) {

	$resp = new HttpResponse();
	$resp->setErrorStatus();
	$resp->setBody($e->getMessage());
	
}
*/


$app->setResponse($resp);

$app->secure();

$app->send();