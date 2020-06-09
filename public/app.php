<?php

require '../bootstrap.php';     

//session_start();
  
use Http\HttpRequest as HttpRequest;
use Http\HttpResponse as HttpResponse;   


ini_set("max_execution_time","18000");
// set_time_limit(0);

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




// if(gettype($out) == "object" && get_class($out) == "HttpResponse") {
// 	$resp = $out;
// } else if(gettype($out) === "string" || gettype($out) === "array" || gettype($out) === "object") {
// 	$resp = $app->getAsHttpResponse($out);

// } else if(get_class($out) == "HttpRedirect") {
// 	$app->setResponse($out);
// 	// $app->secure();
// 	$app->send();
// }



