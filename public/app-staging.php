<?php


require '../bootstrap.php';

$application = !empty($_GET["mail"]) ? "mail" : "http";
$isCLI = false;

use Http\HttpRequest as HttpRequest;
use Http\HttpResponse as HttpResponse;


$app = new Application();

$request = HttpRequest::newFromEnvironment();


if($application == "http")
{

    $response = $app->runHttp($request);
    $app->send($response);
} 
else if($application == "mail")
{

    $response = $app->runHttp($request);
    $app->sendMail($response);
}
else
{
    throw new Exception("No application set in app.php");
}