<?php
require_once '../config/config.php';
require_once '../includes/HTTPRequest.php';
require_once '../includes/HTTPResponse.php';
require '../vendor/autoload.php';   
require_once '../includes/appRouter.php';             

session_start();


$router = new AppRouter($_SERVER["REQUEST_URI"]);
$responseBody = $router->processRoute();

print ($responseBody);