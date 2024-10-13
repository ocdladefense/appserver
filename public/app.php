<?php
require '../bootstrap.php';

$isCLI = false;

use Http\HttpRequest as HttpRequest;
use Http\HttpResponse as HttpResponse;


$app = new Application();

$req = HttpRequest::newFromEnvironment();
$resp = null;

try {
    $resp = $app->runHttp($req);
} catch (Exception $e) {
    $resp = new HttpResponse($e->getMessage());
    $resp->setStatusCode(500);
}
$app->send($resp);
