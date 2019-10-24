<?php
// Unit tests for our API Integration.

//EXECUTION OF TESTS IS './vendor/bin/phpunit  tests'

declare(strict_types=1);
include "vendor/autoload.php";
require "bootstrap.php";

use PHPUnit\Framework\TestCase;

final class AppserverTest extends TestCase
{
    private $validPathWithArgs = "/customer-profile-id?999";
    private $contactId = "999";
    private $completeRequestedResource = "customer-profile-id?999";
    private $resourceString = "customer-profile-id";
    private $pathToRequestedResource = null;
    private $expectedModulesArray = ['authorizeNet','salesforce'];  //Should hold the modules that are available at the time of testing.
    private $allRoutes = array();
    
    public function testParsePathResults(): void{
        $router = new AppRouter();
        $router->parsePath($this->validPathWithArgs);

        $this->assertEquals($this->completeRequestedResource, $router->getCompleteRequestedPath(), "Paths are not Equal");
        $this->assertEquals($this->resourceString, $router->getResourceString(), "Paths are not Equal");
        $this->assertEquals($this->resourceString, $router->getPathToRequestedResource(), "Paths are not Equal");
        $this->assertEquals($this->contactId, $router->getArgs()[0], "getArgs()[0] is not equal to the given argument");
        $this->assertEquals($this->contactId, $router->getArg(0), "getArgs(0) is not equal to the given argument");
    } 
    public function testGetModules(): void{
        $router = new AppRouter();
        $expectedModulesArray = ['authorizeNet','salesforce'];

        $this->assertEquals('authorizeNet', $router->getModules()[0], "The string 'authorizeNet' is not the element at the index of 0");
        $this->assertEquals('salesforce', $router->getModules()[1], "The string 'salesforce' is not the element at the index of 1");
        $this->assertEquals($expectedModulesArray, $router->getModules(), "The arrays are not the same");
    }
    public function testLoadModules(): void{
        $router = new AppRouter();
        

        //require "modules/authorizeNet/module.php";

        $router->LoadModules($this->expectedModulesArray);
        $this->assertTrue(function_exists("chargeCard"));
    }
    public function testAllRoutesHasAllAvalilableRoutes(): void{
        $router = new AppRouter();
        //$router->initializeRoutes();
        $this->allRoutes = $router->setRouteDefaults($this->expectedModulesArray);
    }


}


