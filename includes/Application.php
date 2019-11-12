<?php


class Application {
    
    private $moduleLoader;
    private $fileSystemService;

    public function __construct(){}

    public function setModuleLoader($loader){
        $this->moduleLoader = $loader;
    }
    
    public function getInstance($moduleName){
        return $this->moduleLoader->getInstance($moduleName);
    }

    public function getModules(){
        return $this->moduleLoader->getModules();
    }
}
