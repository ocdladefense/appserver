<?php

use Http as Http;
use Http\HttpHeader as HttpHeader; 
use Http\AcceptHeader as AcceptHeader; 
use Http\HttpResponse as HttpResponse;
use Http\HttpRequest as HttpRequest;
use Http\HttpHeaderCollection;

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

    // List of installed modules, if any.
    // Core module will be in here by default.
    private $modules;


    public static $count = 0;


    
	
	
		
    public function __construct() {
		
        global $coreDef;

        // Demonstrate that we can build an index of modules.
        // $mIndex = new ModuleDiscoveryService(path_to_modules());
        // $list = XList::fromFileSystem(path_to_modules());

        // You need a modules directory, or "XList" will throw an Exception.
        if(!file_exists(path_to_modules())) mkdir(path_to_modules());

        $list = new XList(XList::getDirContents(path_to_modules()));

        $installPaths = $this->getComposerInstallPathsByType("appserver-module");
        
        $list->addItems($installPaths);

        // Only include folders with the magic module.json file.
        $only = $list->filter(function($folder) {
            $file = $folder . "/module.json";
            return file_exists($file);
        });

        // Make sure that there is only one instance of a given module installed.
        $this->validateModuleList($only->getArray());

        // Build the complete list of module definitions specified by 
        //  module.json files.
        $defs = $only->map(function($path) {
            $file = $path . "/module.json";
            $json = file_exists($file) ? file_get_contents($file) : "{}";
            $def = json_decode($json, true);
            $def["path"] = $path;
            return $def;
        });
           

        // Build an index for modules.
        $this->modules = $defs->indexBy(function($def) {
            return $def["name"];
        });





        $this->modules->put("core", CoreModule::getRoutes());


        // $this->loader = new ModuleLoader($this->modules->getArray());
        Module::catalog($this->modules->getArray());


        // Build an index for routes.
        $this->routes = $this->modules->map(function($def) {
            $routes = $def["routes"];
            $name = $def["name"];

            
            foreach($routes as $path => &$route) {
                $route["path"] = $path;
                $route["module"] = $name;
                $route["method"] = isset($route["method"]) ? $route["method"] : self::$DEFAULT_HTTP_METHOD;
                $route["content-type"] = $route["content-type"] ?: self::$DEFAULT_CONTENT_TYPE;
            }

            return $routes;
        },false)->flatten();
    }




    


    // Prepare to return an HttpResponse object.
    public function runHttp($req) {

        $uri = $req->getRequestUri();

        // Might be altered depending on wheather the appserver is installed in a subdirectory.
        $scriptUri = $this->getScriptUri($uri);

        session_start();


        // $loader = $this->getLoader(); @jbernal Module is now static
        $router = new Router();

        // if path is not found match returns false.
        $path = $router->match($scriptUri, array_keys($this->routes));
        
        
        if($path == false) { // No matching route was found.

            $module = Module::loadInstance("core");
            $route = $this->routes["system/404/%msg"];

            $params = array("We couldn't find ".$scriptUri);
            // var_dump($params);exit;

        } else { // We found a route

            $params = $path->getParams();

            $route = $this->routes[$path->__toString()];

            $moduleName = $route["module"];

            $module = Module::loadInstance($moduleName);
        }



        // ... But access is denied to the route.
        // Return a 403.
        // @TODO: setup appropriate response for different handlers?
        if(CHECK_ACCESS === true && !user_has_access($module,$route))
        {
            $module = Module::loadInstance("core");
            $route = $this->routes["system/403"];
        }
        

        $module->setRequest($req);
        $module->setCurrentRoute($route);
        set_active_module($module);


        Presentation\Component::setRequest($req);

        
        // Instantiate a new translation class.
        // Check for lang and files and correct name.
        // Methods are static
        // Default to en for testing
        Translate::init($module->getRelPath(),$module->getLanguages());//path and language filenames

        

        
        $value = $module->getInfo()["connectedApp"];
        $config = get_oauth_config($value);
        $name = $config->getName();

        // If the module requires APIs, bootstrap those.
        // Currently, use the module's "connectedApp" key to determine which API to use.
        if(null != $name && !api_is_bootstrapped($name)){

            $this->writeCredentialsToCache($module);
        }



        $out = $this->getOutput($req, $module, $route, $params);
        
        session_write_close();
        return $out;
    }





    /**
     * Actually call the route's callback
     *  Retrieve the output, then decide what to do with the output
     *  depending on the context.
     * 
     * @param module The module that is implementing the current route.
     * @param route The route to be executed.
     * @param params Any parameters to be passed in via the URL.
     */
    public function getOutput($req, $module, $route, $params = array()) {
        global $theme;
        
        $params = $params != null ? $params : array();

        $resp = new HttpResponse();

		// Init the theme, first.
		$className = ucfirst(strtolower(get_theme())) . "Theme";
		require_once(get_theme_path() . "/" . $className . ".php");
		
		$theme = new $className();

        // REMEMBER! TRY CATCH BLOCKS WON'T DISPLAY WARNINGS.
        try {

            $out = call_user_func_array(array($module, $route["callback"]), $params);
            // if(null == $out) throw new Exception("Callback function returned NULL!");

        } catch(Throwable $e) {

            
            if(get_class($e) == "Http\HttpClientException") {

                if(function_exists("opcache_invalidate")) {
                    $result = opcache_invalidate(CACHE_DIR . "/access_token", true);
                    $result = opcache_invalidate(CACHE_DIR . "/instance_url", true);
                }

                cache_delete("access_token");
                cache_delete("instance_url");

                return $this->runHttp($req);
            }



            $out = get_class($e) == "Error" ? new Error($e->getMessage(), 0, $e) : new Exception($e->getMessage(), 0, $e);
            
            $resp->setStatusCode(500);

            // Unhandled Exceptions/Errors will often be returned as
            // text (i.e., error message / stack trace) by the PHP runtime or
            // XDebug.
            // We should only let these messages propagate up to the main app.php
            // if the reqested content-type is "text/html."
            if(defined("DEBUG") && DEBUG === true) {

                throw $out;

            } else {

                $handlers = ob_list_handlers();

                while(!empty($handlers)) {

                    ob_end_clean();
                    $handlers = ob_list_handlers();
                }
            }
        } // end catch.





        if(self::isHttpResponse($out) && null == $out->getHeader("X-Theme")) {
            return $out;
        }

        
        // Content-Negotiation.
        $handler = Handler::getRegisteredHandler($req, $route, $out);


        

        if(isset($route["theme"])) {
            \set_theme("Videos");
        }


        $fn = function() {
            $links = array();
            foreach($this->modules->getArray() as $moduleDef) {
                if(!empty($moduleDef["links"])) $links = array_merge($links, $moduleDef["links"]);
            }
            return $links;
        };
        Theme::addLinks($fn());


        /* Detailed Information on Accept Header
        https://httpwg.org/specs/rfc7231.html#header.accept
        
        All HTTP Status Code Definitions
        https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
        
        Details description of 406 Not Acceptable
        https://httpwg.org/specs/rfc7231.html#status.406
        */
        $explicit = !empty($route["content-type"]) ? array($route["content-type"]) : null;
        $accept = $req->getHeader("Accept");
        
        $BYPASS_ACCEPT_HEADER = true;

        if($BYPASS_ACCEPT_HEADER || empty($accept->getValue())) {
            $accept = new AcceptHeader("*/*");
        }

        // var_dump($req);
        $contentType = Handler::getPreferredRepresentationMimeType($accept->getByWeight(), (!empty($explicit) ? $explicit : $handler->getRepresentations()));


        
        // Resolve the Content-Type header against
        // the Accept header.  We talk about this 
        // as "could the available content-types satisfy the request?"
        // If the answer is no we need to return the appropriate
        // status code, per the Spec.
        if(false && !$req->canBeSatisfiedBy($available)) {
            $resp->setStatusCode(406, "Not Acceptable");
            return $resp;
        };





        

        // Set headers on the HTTP Response.
        $resp->addHeader(new HttpHeader("Content-Type",$contentType));
        $resp->addHeaders($handler->getHeaders($contentType));
        

        // Set the body of the HTTP Response that will be returned to the client.
        $resp->setBody($handler->getOutput($contentType));

        // For HTTP Responses, check to see if the resp has already dictated
        // what the HTTP status code should be.
        if(method_exists($handler,"getStatusCode")) {
            $resp->setStatusCode($handler->getStatusCode());
        }
        // var_dump($explicit,$accept,$contentType);exit;

        // Remove the PHP error stack if we don't wish to deliver
        // verbose debugging info.
        // Assuming the handler supports ->removeStack();
        // $handler->removeStack();


        return $resp;
    }


    public function writeCredentialsToCache($module) {

        $value = $module->getInfo()["connectedApp"];
        $config = get_oauth_config($value);
        $flow = "usernamepassword";

        $httpMessage = OAuth::start($config, $flow);

        $oauthResp = $httpMessage->authorize();

        if(!$oauthResp->isSuccess()) throw new OAuthException($oauthResp->getErrorMessage());

        // CoreModule::setSession($config->getName(), $flow, $oauthResp->getInstanceUrl(), $oauthResp->getAccessToken());
        cache_set("instance_url", $oauthResp->getInstanceUrl());
        cache_set("access_token", $oauthResp->getAccessToken());
    }



    
    public function exec($uri) {
        list($module, $route, $params) = $this->init($uri);

        return $this->getOutput($module, $route, $params);
    }
        
        



    	
    	
    
    
    
    /**
     * For HTTP contexts,
     *  `run` should return an HttpResponse object that will be returned to the
     *  webserver context.
     */
    public function run($path) {

    	return $this->runHttp($path);
    } 


    // Other Methods
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
    




    public function send($message) {
        if(!is_object($message)) die('Woops');

        // $class = get_class($message);

        
        $this->sendHttp($message);
        /* 
        // Return a subrequest, if we want.
        // is_subclass_of($object, "Http\HttpRequest", false)
        if(is_object($next) && get_class($next) == "Http\HttpRequest") {
            // print "foodbar";exit;
            $resp = $this->runHttp($next);
            // var_dump($resp);
            $this->send($resp);
        }
        else if(null != $next) $this->send($next);
        */
    }


    
    public function sendHttp($resp) {

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

        return null;
    }












// Should only be executed if the route needs it.
    // This function is deprecated and is no longer needed.
    // @DEPRECATED
    private function doAuthorization($module,$route) {

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

            $_SESSION["login_redirect"] = $_SERVER["HTTP_REFERER"];
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
            $resp->setStatusCode(403);
            $resp->setBody("Access Denied!");
            return $resp;
        }
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

        return is_object($object) && (get_class($object) === "Http\HttpResponse" || is_subclass_of($object, "Http\HttpResponse", false));
    }






    public function getComposerInstallPathsByType($type){

        // Get installed composer appserver module packages
        $installedModulePackages = Composer\InstalledVersions::getInstalledPackagesByType($type);

        // Get the install path for each module package
        $installPaths = array();

        // For some reason, the "InstalledVersions" class method "getInstalledPackagesByType" returns duplicates.
        // The issue is in the "getInstalled" method which is called by "getInstalledPackagesByType".
        // That is why i need to use the "filterDups" boolean.
        $filterDups = true;
        foreach($installedModulePackages as $package){
            
            $path = Composer\InstalledVersions::getInstallPath($package);
            if(true === $filterDups && !in_array($path, $installPaths)){
                
                $installPaths[] = $path;

            } elseif($filterDups === false){

                $installPaths[] = $path;
            }
        }

        return $installPaths;

    }

    public function validateModuleList($pathsToModules){

        $moduleNames = array();
        foreach($pathsToModules as $path){

            $pathParts = explode("/", $path);
            $semiFilteredName = $pathParts[count($pathParts) -1];
            $semiFilteredNameParts = explode("\\", $semiFilteredName);
            $moduleName = $semiFilteredNameParts[count($semiFilteredNameParts) -1];

            if(in_array($moduleName, $moduleNames)){

                throw new Exception("MODULE_DUPLICATE_ERROR: You have two instances of the $moduleName module installed.");
            }

            $moduleNames[] = $moduleName;
        }
    }
    
    public function isInstallDirValid($uri, $installDir){

        return strpos($uri, $installDir) === 0;
    }


    
    public function calculateScriptUri($uri, $installDir){

        $uriLength = strlen($uri);
        $installDirLength = strlen($installDir);
        $offset = $installDirLength - $uriLength;

        return substr($uri, $offset);
    }



    public function getScriptUri($uri){

        $installDir = defined("APPSERVER_INSTALL_DIRECTORY") && !empty(APPSERVER_INSTALL_DIRECTORY) ? trim(APPSERVER_INSTALL_DIRECTORY) : null;

        $installDirIsSet = $installDir != null;

        if($installDirIsSet){

            // Making sure that the setting for the constant is valid
            $installDirIsValid = $this->isInstallDirValid($uri, $installDir);
            
            if(!$installDirIsValid){
                
                throw new Exception("CONFIGURATION_ERROR: the 'APPSEVER_INSTALL_DIRECTORY' setting is invalid in config.php");

            }

            return $this->calculateScriptUri($uri, $installDir);

        } else {

            return $uri;
        }

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
  

