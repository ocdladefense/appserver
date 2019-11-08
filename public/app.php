<?php
require '../bootstrap.php';           

session_start();

$appModules = new AppModules();



$router = new AppRouter();
$responseBody = $router->runRouter($appModules->getModules(),$_SERVER['REQUEST_URI']);
$router->sendHeaders();
print_r ($responseBody);