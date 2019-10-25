<?php
require '../bootstrap.php';           

session_start();


//$router = new AppRouter($_SERVER["REQUEST_URI"]);
$appModules = new AppModules();
$modules = $appModules->getModules();



$router = new AppRouter();
$router->initRoutes($modules);
$router->setPath($_SERVER['REQUEST_URI']);
$router->parsePath();
$responseBody = $router->processRoute();
print ($responseBody);