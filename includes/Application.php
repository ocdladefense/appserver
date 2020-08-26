<?php

use \Http as Http;
use \Http\HttpHeader as HttpHeader; 
use \Http\HttpResponse as HttpResponse;
use \Http\HttpRequest as HttpRequest;


class Application {
    
    private $moduleLoader;
    
    private $fileSystemService;
    
    private $request;
    
    private $resp;

	private $activeRoute; 
		
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
    // Invalid method definition
    public function getRequestHeader($headerName){
        return $this->request->getHeader($headerName);
    }
    

    
    public function run($path) {
        $this->activeRoute = $this->router->match($path);


        //Aciive module is an instance of the module class
        $this->activeModule = ModuleLoader::getInstance($this->activeRoute->getModule());

        $this->activeModule->setRequest($this->request);
        
        $this->activeModule->loadFiles();

        $this->requireRouteFiles($this->activeRoute);

        $resp = new HttpResponse();


        session_start();
        
        try{

            $out = $this->doCallback($this->activeModule,$this->activeRoute);

            //Set up the response body.

            if(gettype($out) != "string" && get_class($out) == "File"){

                $resp = new HttpResponse($out);

                return $resp;
            }
            
            if($this->activeRoute->getContentType() == Http\MIME_APPLICATION_JSON){

            $resp->setBody($this->getAsJson($out));

            } else {
                $resp->setBody($out);
            }

        } catch(PageNotFoundException $e) {

            $resp->setNotFoundStatus();
            $resp->setBody($e->getMessage());
            
        } catch(Exception $e) {

            if($this->activeRoute->getContentType() == Http\MIME_APPLICATION_JSON){

                $error = new StdClass();
                $error->error = $e->getMessage();
                $body = json_encode($error);

            } else {

                $body = $e->getMessage();
            }

            $resp->setErrorStatus();
            $resp->setBody($body);
        }



        //Just adding a header
        if(strpos($this->activeRoute->getContentType(),"json") !== false){
            $contentType = Http\MIME_APPLICATION_JSON;
        } else {
            $contentType = Http\MIME_TEXT_HTML;
        }

        $header = new HttpHeader("Content-Type",$contentType);
        
        $resp->addHeader($header);

        return $resp;
    }

    public function getAsJson($out){
        
        if(gettype($out) == "object" && in_array( "Http\IJson",class_implements($out))){
            return $out->toJson();
        } else {
            return json_encode($out);
        }
    }
    
    
    
    
    //require all of the necessary file in the route at the key of 'files'
    public function requireRouteFiles($route){
        if(null == $route->getFiles())
            return;
            
        foreach($route->getFiles() as $file){
            $this->requireModuleFile($file);
        }
    }


    
    public function requireModuleFile($file){
        $path = getPathToModules()."/{$this->activeRoute->getModule()}/src/".$file;
				require_once($path);
    }




    public function doCallback($module,$route){

        $expectedRouteParams = $route->getParameters();
        $urlNamedParameters = $this->request->getUrlNamedParameters();
        $args = $this->request->getArguments();
        $namedParamKeys = array_keys($urlNamedParameters);
        $params = array();


        //if the parameter is defined by name then use the value for that name otherwise use the value at the current index
        //Determine which kind of paramter to give preference to.
        if(!empty($urlNamedParameters) && empty($args)){
            for($i = 0; $i < count($expectedRouteParams); $i++){
                if(in_array($namedParamKeys[$i],$expectedRouteParams)){
                    $params[] = $urlNamedParameters[$namedParamKeys[$i]];
                }
                if(count($params) == 0){
                    $params = $args;
                }
            }
        } else {
            $params = $args;
        }

        return call_user_func_array(array($module,$route->getCallback()),$params);
    }
    

    //Other Methods
    public function secure(){ 
        $header = $this->resp->getHeader("Content-Type");
        $cType = null;
        
        
        if(null != $cType) {
            $cType = $header->getValue();
        }
        
        $accept = "*/*";

        if(!$this->request->isSupportedContentType("*/*")){
            throw new Exception("The content type of the requested resource '$contentType' does not match the accepted content type '$accept', which is set by the requesting entity.");
        }
    }
    
    public function send() {

        $content = $this->resp->getBody();

        
        $collection = $this->resp->getHeaderCollection();
        foreach($collection->getHeaders() as $header) {
            header($header->getName() . ": " . $header->getValue());
        }

        http_response_code($this->resp->getStatusCode());

        if($this->resp->isFile()){

            $file = $this->resp->getFile();
            if($file->exists()){

                readfile($file->getPath());

            } else {

                $content = $file->getContent();
                
            }
        }

        print $content;
    }
}