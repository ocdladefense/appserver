<?php
require '../bootstrap.php';           

session_start();


$router = new AppRouter($_SERVER["REQUEST_URI"]);
$responseBody = $router->processRoute();

print ($responseBody);