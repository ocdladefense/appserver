<?php
// Prepare an HTTP exchange.
// An HTTP exchange consists of an HTTP Request and Response lifecycle.
/* See also, https://github.com/WICG/webpackage/blob/main/explainers/authoritative-site-sharing.md

An HTTP exchange consists of an HTTP request and its response.

A publisher (like https://theestablishment.co/) writes (or has an author write) some content and owns the domain where it's published. A client (like Firefox) downloads content and uses it. An intermediate (like Fastly, the AMP cache, or old HTTP proxies) downloads content from its author (or another intermediate) and forwards it to a client (or another intermediate).

When an HTTP exchange is encoded into a resource, the resource can be fetched from a distributing URL that is different from the publishing URL of the encoded exchange. We talk about the inner exchange and its inner request and response, the outer resource it's encoded into, and sometimes the outer exchange whose response contains the outer resource.
*/
require '../bootstrap.php';

$application = !empty($_GET["mail"]) ? "mail" : "http";
$isCLI = false;

use Http\HttpRequest as HttpRequest;
use Http\HttpResponse as HttpResponse;


$app = new Application();

$request = HttpRequest::newFromEnvironment();


$response = $app->runHttp($request);
$app->send($response);



// Presentation: A presentation
// $presentation->render();
// $presentation->start();
// $presentation->end();
// $presentation->pause();
// $presentation->stop();
// $presentation->join();
// $presentation->absent();

// $performance;
// $show;
// $isRated()?
// $


