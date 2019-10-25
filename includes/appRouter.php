<?php
class AppRouter
{
    private static $DEFAULT_HTTP_METHOD = "get";
    private static $DEFAULT_CONTENT_TYPE = "html";
    
    

    private $completeRequestedPath = "";
    private $resourceString = "";
    private $pathToRequestedResource = "";
    
    private $filesIncluded = array();
    private $arguments = array();
    private $allRoutes = array();
    

    public function __construct(){}

    public function setPath($path){
        $this->completeRequestedPath = $path;
    }

    public function parsePath($path = null){
        if(!$path == null)
            $this->completeRequestedPath = $path;
        //Remove prevailing slash
        if(strpos($this->completeRequestedPath,"/") == 0){
            $this->completeRequestedPath = substr($this->completeRequestedPath,1);
        }
        //isolate the resource string from the completeRequestedPath
        $parts = explode("?", $this->completeRequestedPath);
        $this->resourceString = $parts[0];
        //isolate the arguments from the completeRequestedPath
        $this->arguments = explode("/",$parts[1]);

        $parts = explode("/", $this->resourceString);

        //match the completeRequestedPath to a path of a resource
        $this->pathToRequestedResource = array_shift($parts);
    }
    public function processRoute(){
        $route = $this->getActiveRoute($this->modules);
        $this->requireRouteFiles($route);
        $this->setHeaderContentType($route);
        return $this->callCallbackFunction($route);
    }
    


    
    
    
    public function getActiveRoute($modules){

        if(!array_key_exists($this->pathToRequestedResource,$this->allRoutes)){
            throw new exception($this->pathToRequestedResource." could not be found");
        }
        return $this->allRoutes[$this->pathToRequestedResource];
    }

    public function requireRouteFiles($route){
        foreach($route["files"] as $file){
            $file = "../modules/{$route['module']}/".$file;
            require($file);
            array_push($this->filesIncluded,$file);
        }
    }
    public function setHeaderContentType($route){
        if($route["content-type"] == "json"){
            header("Content-type: application/json; charset=utf-8");
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
    public function getPathToRequestedResource(){
        return $this->pathToRequestedResource;
    }
    public function getArg($index){
        return $this->arguments[$index];
    }
    public function getArgs(){
        return $this->arguments;
    }
    public function initRoutes(){
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
}