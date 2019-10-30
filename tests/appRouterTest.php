<?php
// Unit tests for the appRouter class.
//Tests require that the test-module is in the modules directory: https://github.com/Trevor-Uehlin/test-module

//EXECUTION OF TESTS IS './vendor/bin/phpunit  tests'

declare(strict_types=1);
include "vendor/autoload.php";
require "bootstrap.php";

use PHPUnit\Framework\TestCase;

final class AppRouterTest extends TestCase
{
    private $validPathThatRequiresFiles = 'test-function-one';

    private $validPathWithArgs = "/test-function-two?parameter";
    private $parameter = "parameter";
    private $completeRequestedResource = "test-function-two?parameter";
    private $resourceString = "test-function-two";
    private $expectedCallback = "testFunctionNumberTwo";
    
    public function testInitRoutes(): void{
        $router = new AppRouter();
        $appModules = new AppModules();
        $allRoutes = $router->initRoutes($appModules->getModules());

        //Insure that $allRoutes array has keys from the "modRoutes" function from each module in the modules directory.
        $this->assertTrue(array_key_exists("test-function-one",$allRoutes)); //Only passes if the testModule is in modules directory.
    }
    public function testSetPath(): void{
        $router = new AppRouter();
        $router->setPath($this->validPathWithArgs);
        $this->assertEquals($router->getCompleteRequestedPath(),$this->validPathWithArgs);
    }
    
    public function testParsePathResults(): void{
        $router = new AppRouter();
        $router->setPath($this->validPathWithArgs);
        $router->parsePath();

        $this->assertEquals($this->completeRequestedResource, $router->getCompleteRequestedPath(), "Paths are not Equal");
        $this->assertEquals($this->resourceString, $router->getResourceString(), "Paths are not Equal");
        $this->assertEquals($this->parameter, $router->getArgs()[0], "getArgs()[0] is not equal to the given argument");
        $this->assertEquals($this->parameter, $router->getArg(0), "getArgs(0) is not equal to the given argument");
    }

    public function testGetActiveRoute(): void{
        $router = new AppRouter();
        $appModules = new AppModules();
        $router->initRoutes($appModules->getModules());
        $router->setPath($this->validPathWithArgs);
        $router->parsePath();
        $activeRoute = $router->getActiveRoute();

        $this->assertEquals($this->expectedCallback,$activeRoute["callback"]);
    }

    public function testRequireRouteFiles(): void{
        $appModules = new AppModules();
        $router = new AppRouter();
        
        $router->initRoutes($appModules->getModules());
        $router->setPath($this->validPathThatRequiresFiles);
        $router->parsePath();
        $activeRoute = $router->getActiveRoute();


        $this->assertFalse(method_exists("TestClass","testMethodNumberOne"));
        $router->requireRouteFiles($activeRoute);
        $this->assertTrue(method_exists("TestClass","testMethodNumberOne"));
    }
    public function testSetHeaderContentType(): void{
        $appModules = new AppModules();
        $router = new AppRouter();
        $expectedContentType = "application/json; charset=utf-8";
        
        $router->initRoutes($appModules->getModules());
        $router->setPath($this->validPathWithArgs);
        $router->parsePath();
        $activeRoute = $router->getActiveRoute();
        $router->requireRouteFiles($activeRoute);
        $router->setHeaderContentType($activeRoute);
        $headers = $router->getHeaders();

        $this->assertEquals($headers["Content-type"],$expectedContentType);
    }
    public function testCallCallBackFunction(): void{
        $appModules = new AppModules();
        $router = new AppRouter();
        
        $router->initRoutes($appModules->getModules());
        $router->setPath($this->validPathWithArgs);
        $router->parsePath();
        $activeRoute = $router->getActiveRoute();
        $router->requireRouteFiles($activeRoute);
        
        $this->assertEquals($router->callCallBackFunction($activeRoute),"Hello from the second test function in the module.php file of the test module");
    }
}


