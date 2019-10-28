<?php
// Unit tests for the appModules class

//EXECUTION OF TESTS IS './vendor/bin/phpunit  tests'

declare(strict_types=1);
include "vendor/autoload.php";
require "bootstrap.php";

use PHPUnit\Framework\TestCase;

final class AppModulesTest extends TestCase
{
    private $expectedModulesArray = ['authorizeNet','salesforce','testModule'];  //Should hold the modules that are available at the time of testing.

    public function testGetModules(): void{
        $appModules = new AppModules();
        $modules = $appModules->getModules();

        $this->assertEquals($this->expectedModulesArray,$modules);
        $this->assertEquals('testModule', $appModules->getModules()[2], "The string 'testModule' is not the element at the index of 2");
        $this->assertEquals($this->expectedModulesArray, $appModules->getModules(), "The arrays are not the same");
    }
    public function testDiscoverFileSystemModules(): void{
        $appModules = new AppModules();
        $modules = $appModules->discoverFileSystemModules();

        $this->assertEquals($this->expectedModulesArray,$modules);
        $this->assertEquals('testModule', $appModules->getModules()[2], "The string 'testModule' is not the element at the index of 2");
        $this->assertEquals($this->expectedModulesArray, $appModules->getModules(), "The arrays are not the same");
    }
    public function testLoadModules(): void{
        $appModules = new AppModules();

        $appModules->LoadModules($this->expectedModulesArray);

        $this->assertTrue(function_exists("testFunctionNumberOne")); //from the testModule
        $this->assertTrue(function_exists("testFunctionNumberTwo")); //from the testModule
    }
}