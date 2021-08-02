<?php

use \Http as Http;
use \Http\HttpHeader as HttpHeader; 
use \Http\HttpResponse as HttpResponse;
use \Http\HttpRequest as HttpRequest;
use Salesforce\OAuth;
use Salesforce\OAuthException;


class Application {
    
    
    private static $DEFAULT_HTTP_METHOD = Http\HTTP_METHOD_GET;

    private static $DEFAULT_CONTENT_TYPE = Http\MIME_TEXT_HTML;

    // The Module Loader.
    private $loader;
    
    // Available routes/commands.
    private $routes = array();
    
    // Incoming request.
    private $req;
    
    // Outgoing response.
    private $resp;

    private $modules;

	
	
		
    public function __construct() {
		
        // Demonstrate that we can build an index of modules.
        // $mIndex = new ModuleDiscoveryService(path_to_modules());
        // $list = XList::fromFileSystem(path_to_modules());
        $list = new XList(XList::getDirContents(path_to_modules()));        
        // dump($list);exit;
        
        // Only include folders with the magic module.json file.
        $only = $list->filter(function($folder) {
            $file = $folder . "/module.json";
            return file_exists($file);
        });
        
        

        // Build the complete list of module definitions specified by 
        //  module.json files.
        $defs = $only->map(function($path) {
            $file = $path . "/module.json";
            $json = file_exists($file) ? file_get_contents($file) : "{}";
            $def = json_decode($json, true);
            $def["path"] = $path;
            return $def;
        });
           
        // dump($defs);

        // Build an index for modules.
        $this->modules = $defs->indexBy(function($def) {
            return $def["name"];
        });

        $coreDef = array(
            "comment"      => "The core module",
            "connectedApp" => CORE_MODULE_CONNECTED_APP_NAME,
            "name"         => "core",
            "description"  => "holds routes for core functionality",
            "files"        => array(),
            "routes"       => array(
                "file/upload" => array(
                    "callback"      => "upload",
                    "content-type"  => "application/json",
                    "path"          => "upload",
                    "module"        => "core",
                    "method"        => "get"
                ),
                "file/download/%filename" => array(
                    "callback"      => "download",
                    "content-type"  => "application/json",
                    "path"          => "download",
                    "module"        => "core",
                    "method"        => "get"
                ),
                "file/list" => array(
                    "callback"      => "list",
                    "content-type"  => "application/json",
                    "path"          => "list/files",
                    "module"        => "core",
                    "method"        => "get"
                ),
                "oauth/start" => array(
                    "callback"      => "oauthFlowStart",
                    "content-type"  => "application/json",
                    "path"          => "oauth/start",
                    "module"        => "core",
                    "method"        => "get"
                ),
                "oauth/api/request" => array(
                    "callback"      => "oauthFlowAccessToken",
                    "content-type"  => "application/json",
                    "path"          => "oauth/api/request",
                    "module"        => "core",
                    "method"        => "get"
                ),
                "login"        => array(
                    "callback"      => "login",
                    "content-type"  => "application/json",
                    "path"          => "login",
                    "module"        => "core",
                    "method"        => "get",
                    "access"        => "is_authenticated",
                    "authorization" => "webserver"
                ),
                "logout"        => array(
                    "callback"      => "userLogout",
                    "content-type"  => "application/json",
                    "path"          => "logout",
                    "module"        => "core",
                    "method"        => "get"
                ),
                "user/profile"        => array(
                    "callback"      => "userProfile",
                    "content-type"  => "text/html",
                    "path"          => "user/profile",
                    "module"        => "core",
                    "method"        => "get"
                )


            ),
						//If the path is null the module loader will not try to load the file
						//core module is loaded in autoloader.php
						"path"     => null
            
        );

        $this->modules->put("core", $coreDef);

        $this->loader = new ModuleLoader($this->modules->getArray());

        // Build an index for routes.
        $this->routes = $this->modules->map(function($def) {
            $routes = $def["routes"];
            $name = $def["name"];

            
            foreach($routes as $path => &$route) {
                $route["path"] = $path;
                $route["module"] = $name;
                $route["method"] = $route["method"] ?: self::$DEFAULT_HTTP_METHOD;
                $route["content-type"] = $route["content-type"] ?: self::$DEFAULT_CONTENT_TYPE;
            }

            return $routes;
        },false)->flatten();
    }


    
    public function runHttp($req) {

        $uri = $req->getRequestUri();
        l("Processing {$uri}.");
    
        $resp = new HttpResponse();

        session_start();

        // Will need to handle PageNotFoundExceptions here.
        list($module, $route, $params) = $this->init($uri);
        //instanciate a new translation class
        //check for lang and files and correct name
        //methods are static
        //default to en for testing
        Translate::init ($module->getRelPath(),$module->getLanguages());//path and language filenames

        // Thrown an exception if authorization is set on the route, but there is no "connectedApp" key set on the module.json file for the module.
        if((isset($route["authorization"]) && !isset($module->getInfo()["connectedApp"])) && get_class($module) != "CoreModule") {

            throw new Exception("MODULE_CONFIGURATION_ERROR: No connected app set for the module.  Check the module.json file of the module.");
        }

        //  This is the module flow not the route flow
        $connectedAppName = $module->get("connectedApp");
        //get the connected app config from the module
        //if there is a default then include it on the module
        $config = get_oauth_config($connectedAppName);

        if(!is_user_authorized($module)){

            // What if we decide to set authorization at the module level?                                                     
            $flow = isset($module->getInfo()["authorizationFlow"]) ? $module->getInfo()["authorizationFlow"] : "usernamepassword";

            //$flow = "usernamepassword";
            $httpMessage = OAuth::start($config, $flow);

            if(self::isHttpResponse($httpMessage)){

                return $httpMessage;
            } else {

                $oauthResp = $httpMessage->authorize();

                if(!$oauthResp->isSuccess()) throw new OAuthException($oauthResp->getErrorMessage());

                OAuth::setSession($config->getName(), $flow, $oauthResp->getInstanceUrl(), $oauthResp->getAccessToken());
            }
        }


        // This is the route flow not the module flow.
        if(!is_user_authorized($module, $route)){

            $resp = user_require_auth($config->getName(), $route);

            if($resp == null) throw new Exception("AUTHORIZATION_ERROR:");

            return $resp;
        }
        
        if(!user_has_access($module, $route)){

            $resp = new HttpResponse();
            $resp->setStatusCode(401);
            $resp->setBody("Access Denied!");
            return $resp;
        }

        $module->setRequest($req);

        if($route["content-type"] == Http\MIME_APPLICATION_JSON) {

            return $this->getJsonOutput($module, $route, $params);

        } else {
            
            return $this->getTextOutput($module, $route, $params);
        }
    }

        /**
     * Actually call the route's callback
     *  Retrieve the output,
     *  then decide what to do with the output
     *  depending on the context.
     */
    public function getOutput($module, $route, $params) {
    
        return call_user_func_array(array($module,$route["callback"]),$params);
    }

    public function getTextOutput($module, $route, $params){

        $resp = new HttpResponse();

        try {

            if(isset($route["theme"])) {
                \set_theme("Videos");
            }
    
            $out = $this->getOutput($module, $route, $params);
                
            if(self::isHttpResponse($out)){
    
                return $out;
            }
            
            if(null == $out) throw new Exception("Callback function returned NULL!");
            
            $handler = Handler::fromType($out, $route["content-type"]);
    
            $resp->setBody($handler->getOutput());
            $resp->addHeaders($handler->getHeaders());
    
            return $resp;

        } catch(Throwable $e) {

            $handlers = ob_list_handlers();
            while (!empty($handlers)){

                ob_end_clean();
                $handlers = ob_list_handlers();
            }

            http_response_code(500);

            if(get_class($e) == "Error") {

                throw new Error($e->getMessage(), 0, $e);  // Should figure out an error code.
            } else {

                throw new Exception($e->getMessage(), 0, $e);
            }
        }
    }


    public function getJsonOutput($module, $route, $params){

        $resp = new HttpResponse();

        try {

            if(isset($route["theme"])) {
                \set_theme("Videos");
            }

            $out = $this->getOutput($module, $route, $params);

            if(self::isHttpResponse($out)){

                return $out;
            }
            
            if(null == $out) throw new Exception("Callback function returned NULL!");
            
            $handler = Handler::fromType($out, $route["content-type"]);

            $resp->setBody($handler->getOutput());
            $resp->addHeaders($handler->getHeaders());
            
       } catch(Exception $error) {

            $handler = Handler::fromType($error, $route["content-type"]);

            $resp->setBody($handler->getOutput());
            $resp->addHeaders($handler->getHeaders());
            http_response_code(500);

        } catch(Error $error) {

            $handler = Handler::fromType($error, $route["content-type"]);

            $resp->setBody($handler->getOutput());
            $resp->addHeaders($handler->getHeaders());
            http_response_code(500);
        }

        return $resp;
    }
    
    
    public function exec($uri) {
        list($module, $route, $params) = $this->init($uri);

        return $this->getOutput($module, $route, $params);
    }
        
        
    // incoming request: maps
    public function init($uri) {

        $router = new Router();
        $path = $router->match($uri, array_keys($this->routes));

        l("Executing application...");
        l("Exec located route: {$path}.");
        l("FINISHED");

        if(false === $path) {
            http_response_code(404);
            throw new Exception("Could not locate {$uri}.");
        }

        $route = $this->routes[$path->__toString()];

        $params = $path->getParams();

        l("Will execute $path: </p><pre>".print_r($route,true)."</pre>");
        
        $moduleName = $route["module"];
        l("Module is: {$moduleName}.");

        // Check access here.
        $access = $route["access"];
        $access_args = $route["access_args"];


        l("Loading Module...");
        $loader = $this->getLoader();


        // Demonstrate that we can instantiate a module
        //  and begin using it.
        $module = $loader->loadObject($moduleName);

        $module->setCurrentRoute($route);

        set_active_module($module);
        
        $func = $route["callback"];

        l("Executing route...<br />Module: {$moduleName}<br />Callback: {$func}.");
        
        return array($module, $route, $params);
    }
    	
    	
    
    
    
    /**
     * For HTTP contexts,
     *  `run` should return an HttpResponse object that will be returned to the
     *  webserver context.
     */
    public function run($path) {

    	return $this->runHttp($path);
    } 


    //Other Methods
    public function secure() { 

        $header = $this->resp->getHeader("Content-Type");
        $cType = null;
        
        if(null != $cType){

            $cType = $header->getValue();
        }
        
        $accept = "*/*";

        if(!$this->request->isSupportedContentType("*/*")){

            throw new Exception("The content type of the requested resource '$contentType' does not match the accepted content type '$accept', which is set by the requesting entity.");
        }
    }
    
    public function send($resp) {

        $content = $resp->getBody();

        $collection = $resp->getHeaderCollection();
        foreach($collection->getHeaders() as $header){

            header($header->getName() . ": " . $header->getValue());
        }

        http_response_code($resp->getStatusCode());

        if($resp->isFile()) {

            $file = $resp->getBody();
            
            if($file->exists()){

                readfile($file->getPath());

            } else {

                $content = $file->getContent();
                
            }
        }

        print $content;
    }
    

    public function getLoader(){
        return $this->loader;
    }

    public function getRoutes(){
        return $this->routes;
    }

    //Setters
    public function setModuleLoader($loader){

        $this->loader = $loader;
    }
    
    public function setRequest($req){

        $this->req = $req;
    }
    
    public function setResponse($resp){

    	$this->resp = $resp;
    }
    
    public function setRouter($router){

        $this->router = $router;
    }

    //Getters
    public function getInstance($moduleName){

        return $this->loader->getInstance($moduleName);
    }
    
    public function getModules(){

        return $this->loader->getModules();
    }

    public static function isHttpResponse($object){

        return is_object($object) && (get_class($object) === "Http\HttpResponse" || is_subclass_of($object, "Http\HttpResponse", False));

    }


///////////////////////////   SHOULD WE REMOVE THESES????   //////////////////////////////////////////////////////////////


    // NOT BEING USED....................
    private function handleErrors() {}


    // NOT BEING USED....................
    public function doParameters($module,$route) {
        
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
    }
}