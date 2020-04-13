<?php

use \Http as Http;


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
    
    public function getRequestHeader($headerName){
        return $this->request->getHeader($headerName);
    }
    

    
    public function run($path) {
    	$this->activeRoute = $this->router->match($path);
    	
      $this->activeModule = ModuleLoader::getInstance($this->activeRoute->getModule());
      
      $this->activeModule->loadFiles();

      $this->requireRouteFiles($this->activeRoute);

			return $this->doCallback($this->activeModule,$this->activeRoute);
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
    		if(method_exists($module,$route->getCallback())) {
    			return call_user_func_array(array($module,$route->getCallback()),$route->getArgs());
    		}
    		
        if($route->getMethod() == "post") {
            //should be set to request->getBody();

            //check the content type of the request if json decode it and pass json to the callback
            $entityBody = file_get_contents('php://input');
            return call_user_func_array($route->getCallback(),array($entityBody));   
        } else {
            return call_user_func_array($route->getCallback(),$route->getArgs());
        }
    }
    
    
    
    
    public function getAsHttpResponse($data) {
			$resp = new HttpResponse();
			
			// Set up the HttpResponse object
			// Should be in Application or another class.
			// $resp->setHeaders($this->activeRoute->headers);
			
			//Add the preferred content type to the headers array
			if(strpos($this->activeRoute->getContentType(),"json") !== false)
			{
					$contentType = Http\MIME_APPLICATION_JSON;
			}
			else
			{
				$contentType = Http\MIME_TEXT_HTML;
			}

			
			$resp->setContentType($contentType);
			
			$out = Http\formatResponseBody($data, $contentType);
			
			$resp->setBody($out);
			
			
			return $resp;
    }

    //Other Methods
    public function secure(){ 
        $routeContentType = $this->resp->getHeader("Content-Type");
        $requestAcceptType = $this->request->getHeader("Accept");

        if(!$this->request->isSupportedContentType($routeContentType)){
            throw new Exception("The content type of the requested resource '$routeContentType' does not match the accepted content type '$requestAcceptType', which is set by the requesting entity.");
        }
    }
    
    public function send() {
    
        $content = $this->resp->getBody();
        
        foreach($this->resp->getHeaders() as $name => $value) {
        	header($name . ": " . $value);
        }
        
        print $content;
    }
}
