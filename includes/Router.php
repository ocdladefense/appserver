<?php


use \Http as Http;


class Router
{
    private static $DEFAULT_HTTP_METHOD = Http\HTTP_METHOD_GET;
    
    private static $DEFAULT_CONTENT_TYPE = Http\MIME_TEXT_HTML;
    
    private $completeRequestedPath = "";
    
    private $resourceString = "";
    
    //An array of all the routes in all the modules using this format
    // $route["module"]
    // $route["method"]
    // $route["content-type"]
    // $route["callback"]
    // $route["files"]
    private $allRoutes = array();
    
    private $headers = array();
    
    private $url;
    
    
    
    /**
     * New instances need an array of modules from which to gather routes from.
     */
    public function __construct($mods = array()){
      $this->modules = $mods;
    }


    public function match($path){
    
		$this->completeRequestedPath = $path;
					
        $this->initRoutes($this->modules);

        $url = new Url($path);

		$this->resourceString = $url->getResourceString();

        //pass the selected route and any named parameters to the Route constructor.
		return new Route($this->getFoundRoute());
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
                $route["content-type"] = $route["content-type"] ?: self::$DEFAULT_CONTENT_TYPE;
            }
            
						$this->allRoutes = array_merge($this->allRoutes,$routes);
        }
        
        return $this->allRoutes;
    }



    //Return the route at the index of the requested resource.

    public function getFoundRoute(){

        if(!array_key_exists($this->resourceString,$this->allRoutes)){
            throw new PageNotFoundException($this->resourceString." could not be found");
        }
        
        return $this->allRoutes[$this->resourceString];
    }

    
    //*****************************Getters*********************************************//
    public function getCompleteRequestedPath(){
        return $this->completeRequestedPath;
    }
    
    
    public function getResourceString(){
        return $this->resourceString;
    }

}