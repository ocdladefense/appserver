<?php


class Application {
    
    private $moduleLoader;
    private $fileSystemService;
    private $request;
    private $headers;

    public function __construct(){}

    //Setters
    public function setModuleLoader($loader){
        $this->moduleLoader = $loader;
    }
    public function setRequest($request){
        $this->request = $request;
        $this->headers = $request->getHeaders();
    }

    //Getters
    public function getInstance($moduleName){
        return $this->moduleLoader->getInstance($moduleName);
    }
    public function getModules(){
        return $this->moduleLoader->getModules();
    }
    public function getRequestHeader($headerName){
        $this->headers = $this->request->getHeaders();
        return $this->headers[$headerName];
    }
}
