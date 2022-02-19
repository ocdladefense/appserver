<?php


require '../bootstrap.php';

$application = !empty($_GET["mail"]) ? "mail" : "http";
$isCLI = false;

use Http\HttpRequest as HttpRequest;
use Http\HttpResponse as HttpResponse;


$app = new Application();

$request = HttpRequest::newFromEnvironment();


$response = $app->runHttp($request);
$app->send($response);
