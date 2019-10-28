<?php
// Unit tests for the appRouter class.

//EXECUTION OF TESTS IS './vendor/bin/phpunit  tests'

declare(strict_types=1);
include "vendor/autoload.php";
require "bootstrap.php";

use PHPUnit\Framework\TestCase;

final class AppRouterTest extends TestCase
{
    private $validPathThatRequiresFiles = 'charge-credit-card';

    private $validPathWithArgs = "/customer-profile-id?999";
    private $contactId = "999";
    private $completeRequestedResource = "customer-profile-id?999";
    private $resourceString = "customer-profile-id";
    private $expectedCallback = "getCustomerProfileIdFromSalesforce";
    
    public function testInitRoutes(): void{
        $router = new AppRouter();
        $appModules = new AppModules();
        $allRoutes = $router->initRoutes($appModules->getModules());

        //Insure that $allRoutes array has keys from the "modRoutes" function from each module in the modules directory.
        $this->assertTrue(array_key_exists("charge-credit-card",$allRoutes)); //Only passes if authorizeNet module is in modules directory.
        $this->assertTrue(array_key_exists("authorize-user",$allRoutes)); //Only passes if salesforce module is in modules directory
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
        $this->assertEquals($this->contactId, $router->getArgs()[0], "getArgs()[0] is not equal to the given argument");
        $this->assertEquals($this->contactId, $router->getArg(0), "getArgs(0) is not equal to the given argument");
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


        $this->assertFalse(method_exists("Charge","payWith"));
        $router->requireRouteFiles($activeRoute);
        $this->assertTrue(method_exists("Charge","payWith"));
    }
    public function testSetHeaderContentType(): void{
        $appModules = new AppModules();
        $router = new AppRouter();
        $expectedContentType = "Content-type: application/json; charset=utf-8";
        $pathWithContentTypeOfJson = "get-customer-payment-profile";
        
        $router->initRoutes($appModules->getModules());
        $router->setPath($pathWithContentTypeOfJson);
        $router->parsePath();
        $activeRoute = $router->getActiveRoute();
        $router->requireRouteFiles($activeRoute);
        //$router->setHeaderContentType($activeRoute);

        print_r ($activeRoute);
        exit;
        
        $this->assertEquals($activeRoute["content-type"],$expectedContentType);
    }
    public function testCallCallBackFunction(): void{
        $appModules = new AppModules();
        $router = new AppRouter();

        //a path pointing to a function specificaly for testing.
        $testingFunctionPath = "testing-function";
        
        $router->initRoutes($appModules->getModules());
        $router->setPath($testingFunctionPath);
        $router->parsePath();
        $activeRoute = $router->getActiveRoute();
        $router->requireRouteFiles($activeRoute);
        
        $this->assertEquals($router->callCallBackFunction($activeRoute),"testing function");
    }
}


