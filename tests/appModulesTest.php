<?php
// Unit tests for the appModules class

//EXECUTION OF TESTS IS './vendor/bin/phpunit  tests'

declare(strict_types=1);
include "vendor/autoload.php";
require "bootstrap.php";

use PHPUnit\Framework\TestCase;

final class AppModulesTest extends TestCase
{
    private $expectedModulesArray = ['authorizeNet','salesforce'];  //Should hold the modules that are available at the time of testing.

    public function testGetModules(): void{
        $appModules = new AppModules();
        $modules = $appModules->getModules();

        $this->assertEquals($this->expectedModulesArray,$modules);
        $this->assertEquals('authorizeNet', $appModules->getModules()[0], "The string 'authorizeNet' is not the element at the index of 0");
        $this->assertEquals('salesforce', $appModules->getModules()[1], "The string 'salesforce' is not the element at the index of 1");
        $this->assertEquals($this->expectedModulesArray, $appModules->getModules(), "The arrays are not the same");
    }
    public function testDiscoverFileSystemModules(): void{
        $appModules = new AppModules();
        $modules = $appModules->discoverFileSystemModules();

        $this->assertEquals($this->expectedModulesArray,$modules);
        $this->assertEquals('authorizeNet', $appModules->discoverFileSystemModules()[0], "The string 'authorizeNet' is not the element at the index of 0");
        $this->assertEquals('salesforce', $appModules->discoverFileSystemModules()[1], "The string 'salesforce' is not the element at the index of 1");
        $this->assertEquals($this->expectedModulesArray, $appModules->discoverFileSystemModules(), "The arrays are not the same");
    }
    public function testLoadModules(): void{
        $appModules = new AppModules();

        $appModules->LoadModules($this->expectedModulesArray);

        $this->assertTrue(function_exists("chargeCard")); //from authorizeNet module
        $this->assertTrue(function_exists("authorize"));  //from salesforce module
    }
}