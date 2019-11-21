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

//httpresponse 


///Router->run should retunr an httpResponse instance
$responseBody = $router->run($application->getRequestHeader("Request-URI"));


$application->secure();

//should be in the httpResponse class
$router->sendHeaders();

//print out according to client accept type

//if the content type is json return json_encode(rewponsebody) or an array
print $responseBody;

//use httpResponse class to package up the data and know how to format it.