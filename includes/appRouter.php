<?php
class AppRouter
{
    private $completeRequestedPath = "";
    private $resourceString = "";
    private $pathToRequestedResource = "";
    
    private $filesIncluded = array();
    private $arguments = array();
    private $allRoutes = array();
    private $modules = array();


    public function __construct($path){
        $this->completeRequestedPath = $path;
        $this->initializeRoutes();
        $this->parsePath();   
    }

    public function parsePath(){
        if(strpos($this->completeRequestedPath,"/") == 0){
            $this->completeRequestedPath = substr($this->completeRequestedPath,1);
        }
        //isolate the resource string from the requested path
        $parts = explode("?", $this->completeRequestedPath);
        $this->resourceString = $parts[0];
        $parts = explode("/", $this->resourceString);
        //remove the base path
        array_shift($parts);
        //match the completeRequestedPath to a path of a resource
        $this->pathToRequestedResource = array_shift($parts);
        //subtract the matching path you are left with the args
        $this->arguments = $parts;
    }
    public function processRoute(){
        $route = $this->getActiveRoute($this->modules);
        $this->requireRouteFiles($route);
        $this->setHeaderContentType($route);
        return $this->callCallbackFunction($route);
    }
    public function initializeRoutes(){
        $this->modules = $this->getModules();
        $this->loadModules($this->modules);
        $this->allRoutes = getAllRoutesForModule($this->modules);
    }
    public function getModules(){
        $previous = getcwd();
        chdir("../modules");
        $modules = array();

        $files = scandir(".");

        foreach($files as $dir)  {
            if(!is_dir($dir) || $dir == ".." || $dir == ".")
            continue;
            $modules[] = $dir;
        }
        chdir($previous);
        return $modules;
    }
    public function loadModules($modules){
        $previous = getcwd();
        chdir("../modules");

        $files = scandir(".");

        foreach($modules as $mod)  {
            require($mod."/module.php");
        }
        chdir($previous);
    }
    public function getActiveRoute($modules){

        if(!array_key_exists($this->pathToRequestedResource,$this->allRoutes)){
            throw new exception($this->pathToRequestedResource." could not be found");
        }
        return $this->allRoutes[$this->pathToRequestedResource];
    }
    public function getAllRoutesForModule($modules){
        foreach($this->modules as $mod){
            $functionName = $mod . "ModRoutes";
            $routes = call_user_func($functionName);
            foreach($routes as &$route){
                $route["module"] = $mod;
                $route["method"] = !empty($route["method"])?$route["method"]:"get";
                $route["content-type"] = !empty($route["content-type"])?$route["content-type"]:"html";
            }
            return array_merge($routes, $this->allRoutes);
        }
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
    public function getArg($index){
        return $this->arguments[$index];
    }
    public function getArgs(){
        return $this->arguments;
    }
    



    //Functions that return state data for testing purposes
    public function getcompleteRequestedPath(){
        return $this->completeRequestedPath;
    }
    public function getPaths(){

        echo "\n"."\n".
        "completeRequestedPath ====".$this->completeRequestedPath."*********************************************"."\n"."\n".
        "resourceString ====".$this->resourceString."*******************************************"."\n"."\n".
        "pathToRequestedResource ====".$this->pathToRequestedResource."*************************"."\n"."\n";
    }
    public function getRoutes(){
        print_r($this->allRoutes);
    }
    public function getArguments(){
        print_r ($this->arguments);
    }
    public function getFilesIncluded(){
        print_r($this->filesIncluded);
    }
}