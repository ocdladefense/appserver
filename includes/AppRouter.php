<?php
class AppRouter
{
    private static $DEFAULT_HTTP_METHOD = "get";
    private static $DEFAULT_CONTENT_TYPE = "html";
    
    private $completeRequestedPath = "";
    private $resourceString = "";
    
    private $filesIncluded = array();
    private $additionalModules = array();
    private $arguments = array();
    private $allRoutes = array();
    private $headers = array();
    

    public function __construct(){}

    public function runRouter($modules, $path){
        $this->modules = $modules;
        $this->initRoutes($this->modules);
        $this->setPath($path);
        $this->parsePath();
        return $this->processRoute();
    }

    //Initialize and return all available routes from all available modules.  Set the routes http method and content type to the default
    //if it is not already defined by the module.
    public function initRoutes($modules){
        $this->modules = $modules;

        foreach($this->modules as $mod){
            $routeFunction = $mod . "ModRoutes";
            $routes = call_user_func($routeFunction);
            foreach($routes as &$route){
                $route["module"] = $mod;
                $route["method"] = !empty($route["method"])?$route["method"]:self::$DEFAULT_HTTP_METHOD;
                $route["content-type"] = !empty($route["content-type"])?$route["content-type"]:self::$DEFAULT_CONTENT_TYPE;
            }
                $this->allRoutes = array_merge($this->allRoutes,$routes);
        }
        return $this->allRoutes;
    }

    //Set the complete requested path to the given path
    public function setPath($path){
        $this->completeRequestedPath = $path;
    }

    //Break the complete requested path into parts that can be consumed by the router.
    public function parsePath(){      
        //Remove prevailing slash if there is one
        if(strpos($this->completeRequestedPath,"/") === 0){
            $this->completeRequestedPath = substr($this->completeRequestedPath,1);
        }
        //isolate the resource string from the completeRequestedPath
        $parts = explode("?", $this->completeRequestedPath);
        $this->resourceString = $parts[0];
        //isolate the arguments from the completeRequestedPath
        if(array_key_exists(1,$parts))
            $this->arguments = explode("/",$parts[1]);
    }

    //Call all functions required to process the route
    public function processRoute(){
        $route = $this->getActiveRoute();
        $this->requireRouteFiles($route);
        $this->requireRouteModules($route);
        $this->setHeaderContentType($route);
        return $this->callCallbackFunction($route);
    }
    
    //Return the route at the index of the requested resource.
    public function getActiveRoute(){
        if(!array_key_exists($this->resourceString,$this->allRoutes)){
            throw new exception($this->resourceString." could not be found");
        }
        return $this->allRoutes[$this->resourceString];
    }

    //require all of the necessary file in the route at the key of 'files'
    public function requireRouteFiles($route){
        foreach($route["files"] as $file){
            $file = getPathToModules()."/{$route['module']}/src/".$file;
            require_once($file);
            array_push($this->filesIncluded,$file);
        }
    }
    
    //if the route requires the use of methods from another module, require the module
    public function requireRouteModules($route){
        foreach($route["modules"] as $additionaModule){
            var_dump($route["modules"]);

            $installedModules = scandir(getPathToModules());
            
            if(!in_array($additionaModule,$installedModules)){
                throw new Exception("$additionaModule is required for the given route, but it is not installed in the modules directory");
            }
            
            $additionalModuleFile = getPathToModules()."/$additionaModule/module.php";
            require_once($additionaModule);
            array_push($this->additionalModules,$additionaModule);
            print_r ($this->additionalModules);
            var_dump($installedModules);
            exit;
        }
    } 

    //Add the preferred content type to the headers array
    public function setHeaderContentType($route){
        if($route["content-type"] == "json"){
            $this->headers["Content-type"] = "application/json; charset=utf-8";
        }
    }
    //Send the value of the headers array at the key of content-type 
    public function sendHeaders(){
        foreach($this->headers as $headerName => $headerValue){
            header($headerName.": ".$headerValue);
        }
    }
    public function callCallbackFunction($route){
        if($route["method"] == "post"){
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
    public function getHeaders(){
        return $this->headers;
    }
}