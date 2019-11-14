<?php
require '../bootstrap.php';           

session_start();
$application = new Application();
$loader = new ModuleLoader();
$application->setModuleLoader($loader);
//$application->setFileSystem(new FileSystem());



$router = new Router($application);
$responseBody = $router->run($_SERVER['REQUEST_URI']);
$router->sendHeaders();
print_r ($responseBody);