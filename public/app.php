<?php
$debug = false;

if($debug) {
var_dump($_POST);

var_dump($_FILES);

exit;

}


require '../bootstrap.php';     


use Http\HttpRequest as HttpRequest;
use Http\HttpResponse as HttpResponse;   


ini_set("max_execution_time","18000");

$app = new Application();

$request = HttpRequest::newFromEnvironment();

$response = $app->runHttp($request);

$app->send($response);