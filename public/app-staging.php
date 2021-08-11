<?php

require '../bootstrap.php';
require_once '../config/config-staging.php';


use Http\HttpRequest as HttpRequest;
use Http\HttpResponse as HttpResponse;   


$app = new Application();

$request = HttpRequest::newFromEnvironment();

$response = $app->runHttp($request);


//session_gc();

$app->send($response);