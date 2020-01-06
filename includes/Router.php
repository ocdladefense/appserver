<?php


use \Http as Http;


class Router
{
    private static $DEFAULT_HTTP_METHOD = Http\HTTP_METHOD_GET;
    
    private static $DEFAULT_CONTENT_TYPE = Http\MIME_TEXT_HTML;
    
    private $application;
    
    private $completeRequestedPath = "";
    
    private $resourceString = "";
    
    private $activeRoute;
    
    private $activeModule;
    
    private $filesIncluded = array();
    
    private $additionalModules = array();
    
    private $arguments = array();
    
    private $allRoutes = array();
    
    private $headers = array();
    
    private $url;
    
    
    
    /**
     * New instances need an array of modules from which to gather routes from.
     */
    public function __construct($mods = array()){
      $this->modules = $mods;
    }


    public function run($path){
        $this->initRoutes($this->modules);
        
        $this->url = new Url($path);
        
				$this->completeRequestedPath = $path;
			
				$this->resourceString = $this->url->getResourceString();

				$this->arguments = $this->url->getArguments();

        $this->activeRoute = $this->getActiveRoute();
        
        $this->activeModule = ModuleLoader::getInstance($this->activeRoute["module"]);
        
        $this->requireRouteFiles($this->activeRoute);

				return $this->doCallback($this->activeRoute);
    }


    //Initialize and return all available routes from all available modules.  Set the routes http method and content type to the default
    //if it is not already defined by the module.
    public function initRoutes(){

        foreach($this->modules as $mod){
            $module = ModuleLoader::getInstance($mod);
            $routes = $module->getRoutes();
            
            foreach($routes as &$route){
                $route["module"] = $mod;
                $route["method"] = $route["method"] ?: self::$DEFAULT_HTTP_METHOD;
                $route["Content-Type"] = $route["Content-Type"] ?: self::$DEFAULT_CONTENT_TYPE;
            }
            
						$this->allRoutes = array_merge($this->allRoutes,$routes);
        }
        
        return $this->allRoutes;
    }



    //Return the route at the index of the requested resource.
    public function getActiveRoute(){

        if(!array_key_exists($this->resourceString,$this->allRoutes)){
            throw new PageNotFoundException($this->resourceString." could not be found");
        }
        
        return $this->allRoutes[$this->resourceString];
    }






    //require all of the necessary file in the route at the key of 'files'
    public function requireRouteFiles($route){
        if(!isset($route["files"]))
            return;
            
        foreach($route["files"] as $file){
            $this->requireModuleFile($file);
            array_push($this->filesIncluded,$file);
        }
    }
    
    
    
    public function requireModuleFile($file){
        $path = getPathToModules()."/{$this->activeRoute['module']}/src/".$file;
				require_once($path);
    }




    public function doCallback($route){
        if($route["method"] == "post"){
            //should be set to request->getBody();
            $entityBody = file_get_contents('php://input');
            return call_user_func_array($route["callback"],array($entityBody));   
        }
        else{
            return call_user_func_array($route["callback"],$this->getArgs());
        }
    }
    
    
    
    //*****************************Getters*********************************************//
    public function getCompleteRequestedPath(){
        return $this->completeRequestedPath;
    }
    public function getResourceString(){
        return $this->resourceString;
    }
    
    
    public function getArg($index){
        return $this->arguments[$index];
    }
    public function getArgs(){
        return $this->arguments;
    }
    public function getFilesIncluded(){
        return $this->filesIncluded;
    }
}