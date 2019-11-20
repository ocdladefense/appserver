<?php
require '../bootstrap.php';           

session_start();

$request = HTTPRequest::newFromEnvironment();
// print_r($request->getHeaders());
// exit;

$application = new Application();
$loader = new ModuleLoader();
$application->setModuleLoader($loader);
$application->setRequest($request);

$router = new Router($application);
$responseBody = $router->run($application->getRequestHeader("Request-URI"));

if($router->getHeaders()["Content-type"] == $application->getRequestHeader("Accept") || stringContains($application->getRequestHeader("Accept"),"*/*")){
    $router->sendHeaders();
}
else{
    throw new Exception("The content type of the requested resource '{$router->getHeaders()["Content-type"]}' does not match the accepted
     content type '{$application->getRequestHeader("Accept")}', which is set by the requesting entity.  Exception thrown");
}

print $responseBody;