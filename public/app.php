<?php

require '../bootstrap.php';           

session_start();

$request = HTTPRequest::newFromEnvironment();
//var_dump($request);exit;

$app = new Application();
$app->setModuleLoader(new ModuleLoader());

$router = new Router($app->getModules());
$app->setRouter($router);
$app->setRequest($request);

try {

	$out = $app->doRoute($request->getRequestUri());
	
	if(gettype($out) === "string") {
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
	
} catch(Exception $e) {

	$resp = new HttpResponse();
	$resp->setErrorStatus();
	$resp->setBody($e->getMessage());
	
}


$app->setResponse($resp);

$app->secure();

$app->send();