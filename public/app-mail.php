<?php

require '../bootstrap.php';


use Http\HttpRequest as HttpRequest;
use Http\HttpResponse as HttpResponse;


$app = new Application();

$request = HttpRequest::newFromEnvironment();

//$response = $app->runHttp($request);


$response = new HttpResponse();
$response->setBody("Here are the latest OCDLA Case Reviews");



$app->sendMail($response);
