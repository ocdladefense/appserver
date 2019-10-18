<?php
require '../bootstrap.php';

function isFinding(){
    return "testing page is found";
}

function isRequestedPath(){
    $testPath = "/foobar";
    $router = new AppRouter($testPath);
    $requestedPath = $router->getRequestedPath();

    if($requestedPath == $testPath)
        return "true, path is ".$requestedPath;
        return "false, path is ".$requestedPath;
}

echo isFinding();
echo isRequestedPath();
