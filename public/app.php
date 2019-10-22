<?php
require '../bootstrap.php';           

session_start();


//$router = new AppRouter($_SERVER["REQUEST_URI"]);
$router = new AppRouter();
$router->recievePath($_SERVER['REQUEST_URI']);
$responseBody = $router->processRoute();

print ($responseBody);