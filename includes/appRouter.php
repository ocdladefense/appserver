<?php
class AppRouter
{
    private $requestedPath = "";
    private $basePath = "";
    private $resourceString = "";
    private $pathToRequestedResource = "";
    private $arguments = array();
    private $allRoutes = array();


    public function __construct($path)
    {
        $this->requestedPath = $path;
        $this->initializeRoutes();
        $this->parsePath();
    }

    public function parsePath()
    {
        if(strpos($this->requestedPath,"/") == 0)
        {
            $this->requestedPath = substr($this->requestedPath,1);
        }
        //isolate the resource string from the requested path
        $parts = explode("?", $this->requestedPath);
        $this->resourceString = $parts[0];
        $parts = explode("/", $this->resourceString);
        //remove the base path
        $basePath = array_shift($parts);
        //match the requestedPath to a path of a resource
        $this->pathToRequestedResource = array_shift($parts);
        //subtract the matching path you are left with the args
        $this->arguments = $parts;
    }

    public function getArg($index)
    {
        return $this->arguments[$index];
    }

    public function getArgs()
    {
        return $this->arguments;
    }

    public function processRoute()
    {
        //allRoutes is all possible routes "collected" from the names of the files in modules minus the "Mod"
        if(!array_key_exists($this->pathToRequestedResource,$this->allRoutes))
        {
            throw new exception($this->pathToRequestedResource." could not be found");
        }
        $route = $this->allRoutes[$this->pathToRequestedResource];

        //loop through files to include the files in the route
        $filesIncluded = array();
        foreach($route["files"] as $file)
        {
            $file = "../modules/{$route['module']}/".$file;
            require($file);
            array_push($filesIncluded,$file);
        }
        if($route["content-type"] == "json")
        {
            header("Content-type: application/json; charset=utf-8");
        }
        if($route["method"] == "post")
        {
            $entityBody = file_get_contents('php://input');
            return call_user_func_array($route["callback"],array($entityBody));
            
        }
        else
        {
            return call_user_func_array($route["callback"],$this->getArgs());
        }
    }

    public function initializeRoutes()
    {
        //loop through the files in the modules folder in the directory
        //for each file, isolate the module name and add it to the "$modules" array
        //use the "include" function to run the script which will make the functions available
        $previous = getcwd();
        chdir("../modules");
        $modules = array();

        $files = scandir(".");
        foreach($files as $file)  
        {
            if(!is_file($file)) continue;
            $moduleName = explode("Mod", $file)[0];
            $modules[] = $moduleName;
            include($file);
        }
        chdir($previous);
        //loop through the "$modules" array
        //for each module name in the "$modules" array, concatinate the module name with ".ModRoutes" and assign that value to the "$routes" variable
        //use the "array_merge" function to merge the "$allRoutes" array and the "$routes" variable (as an array) and assign that value to the "$allRoutes" variable
        $this->allRoutes = array();
        foreach($modules as $mod)
        {
            $functionName = $mod . "ModRoutes";
            $routes = call_user_func($functionName);
            foreach($routes as &$route)
            {
                $route["module"] = $mod;
                $route["method"] = !empty($route["method"])?$route["method"]:"get";
                $route["content-type"] = !empty($route["content-type"])?$route["content-type"]:"html";
            }
            $this->allRoutes = array_merge($routes, $this->allRoutes);
        }
    }
}
?>