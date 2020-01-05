<?php

require '../bootstrap.php';           

session_start();

$request = HTTPRequest::newFromEnvironment();

$app = new Application();

$app->setModuleLoader(new ModuleLoader());
$router = new Router($app);

$app->setRouter($router);

$app->setRequest($request);



try {
	$resp = $router->run($app->getRequestHeader("Request-URI"));
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