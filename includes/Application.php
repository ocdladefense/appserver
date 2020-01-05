<?php


class Application {
    
    private $moduleLoader;
    
    private $fileSystemService;
    
    private $request;
    
    private $resp;

    public function __construct(){}

    //Setters
    public function setModuleLoader($loader){
        $this->moduleLoader = $loader;
    }
    public function setRequest($request){
        $this->request = $request;
    }
    
    public function setResponse($resp) {
    	$this->resp = $resp;
    }
    public function setRouter($router){
        $this->router = $router;
    }

    //Getters
    public function getInstance($moduleName){
        return $this->moduleLoader->getInstance($moduleName);
    }
    public function getModules(){
        return $this->moduleLoader->getModules();
    }
    public function getRequestHeader($headerName){
        return $this->request->getHeader($headerName);
    }

    //Other Methods
    public function secure(){ 
        $routeContentType = $this->resp->getHeader("Content-Type");
        $requestAcceptType = $this->request->getHeader("Accept");

        if(!$this->request->isSupportedContentType($routeContentType)){
            throw new Exception("The content type of the requested resource '$routeContentType' does not match the accepted content type '$requestAcceptType', which is set by the requesting entity.");
        }
    }
    
    public function send(){
        $this->resp->sendHeaders();
        $content = $this->resp->getBody();
        
        print $content;
    }
}
