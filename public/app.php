<?php
require '../bootstrap.php';           

session_start();

$request = HTTPRequest::newFromEnvironment();
// print_r($request->getHeaders());
// exit;

$application = new Application();
$loader = new ModuleLoader();
$application->setModuleLoader($loader);
$router = new Router($application);
$application->setRouter($router);
$application->setRequest($request);



$responseBody = $router->run($application->getRequestHeader("Request-URI"));


$application->secure();
$router->sendHeaders();
print $responseBody;