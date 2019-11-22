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

$request->setHeader("Accept","*/*");

$application = new Application();
$loader = new ModuleLoader();
$application->setModuleLoader($loader);
$router = new Router($application);
$application->setRouter($router);
$application->setRequest($request);

$response = $router->run($application->getRequestHeader("Request-URI"));

$application->secure();
$application->send($response);