<?php
require '../bootstrap.php';


//echo isRequestedPath();
//echo displayAllPaths();
//displayAllFilesIncluded();
//displayAllArguments();
//displayAllRoutes();
testValidPath();


function testValidPath(){
    $contactId = "999";
    $validPath = "/customer-profile-id?{$contactId}";
    $router = new AppRouter($validPath);

    $modules = $router->getModules();

    if(!is_dir("../modules/salesforce")){
        throw new Exception("Not a directory");
    }

    if(!in_array("salesforce",$modules) && !in_array("authorizeNet",$modules)){
        throw new Exception("should have loaded salesforce module");
    }

    $arguments = $router->getArgs();

    print_r ($arguments);

    if(!in_array("999",$arguments)){
        throw new Exception("The array should contain a parameter of 999");
    }


    
}
function isRequestedPath(){
    $testPath = "part1/part2/part3/part4";
    $router = new AppRouter($testPath);
    $requestedPath = $router->getRequestedPath();

    if($requestedPath == $testPath)
        return "true, path is ".$requestedPath;
        return "false, path is ".$requestedPath;
}
function displayAllPaths(){
    $testPath = "part1/part2/part3/part4?SOME-PARAMTER-STRING";
    $router = new AppRouter($testPath);
    return $router->listAllPaths();
}
function displayAllRoutes(){
    $testPath = "part1/part2/part3/part4?SOME-PARAMTER-STRING";
    $router = new AppRouter($testPath);
    return $router->listAllRoutes();
}
function displayAllArguments(){
    $testPath = "part1/part2/part3/part4?SOME-PARAMTER-STRING";
    $router = new AppRouter($testPath);
    return $router->listAllArguments();
}
function displayAllFilesIncluded(){
    $testPath = "part1/part2/part3/part4?SOME-PARAMTER-STRING";
    $router = new AppRouter($testPath);
    return $router->listAllFilesIncluded();
}


