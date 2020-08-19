<?php

require '../bootstrap.php';     

  
use Http\HttpRequest as HttpRequest;
use Http\HttpResponse as HttpResponse;   


ini_set("max_execution_time","18000");


$request = HttpRequest::newFromEnvironment();
$response = null;

$app = new Application();
$app->setModuleLoader(new ModuleLoader());

$router = new Router($app->getModules());
$app->setRouter($router);
$app->setRequest($request);


$response = $app->run($request->getRequestUri());

$app->setResponse($response);

$app->secure();

$app->send();