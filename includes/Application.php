<?php

use \Http as Http;
use \Http\HttpHeader as HttpHeader; 
use \Http\HttpResponse as HttpResponse;
use \Http\HttpRequest as HttpRequest;


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

	
	
		
    public function __construct(){
		
				// Demonstrate that we can build an index of modules.
				// $mIndex = new ModuleDiscoveryService(path_to_modules());
				$list = XList::fromFileSystem(path_to_modules());
				
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
				$modules = $defs->indexBy(function($def) {
					return $def["name"];
				});
				$this->loader = new ModuleLoader($modules->getArray());
				// dump($modules);

				
				// Build an index for routes.
				$this->routes = $modules->map(function($def) {
					$routes = $def["routes"];
					$name = $def["name"];
					// dump($routes);
					
					foreach($routes as $path => &$route) {
						$route["path"] = $path;
						$route["module"] = $name;
						$route["method"] = $route["method"] ?: self::$DEFAULT_HTTP_METHOD;
						$route["content-type"] = $route["content-type"] ?: self::$DEFAULT_CONTENT_TYPE;
					}

					return $routes;
				},false)->flatten();


				// dump($this->routes);
    }


    
		public function runHttp($req) {

			$uri = $req->getRequestUri();
    	l("Processing {$uri}.");
    	
    	$resp = new HttpResponse();
			

      session_start();
      

      try {
      
				list($module, $route, $params) = $this->init($uri);
      	
      	$module->setRequest($req);
      	
				$out = $this->getOutput($module, $route, $params);
				
				$handler = Handler::fromType($out, $route["content-type"]);
      	// $handler->get($out);
				// var_dump($handler);
				$resp->setBody($handler->getOutput());
				$resp->addHeaders($handler->getHeaders());
				
			} catch(Exception $e) {
				throw $e;
			}

			
			return $resp;
		}
		
		
		
		public function exec($uri) {
			list($module, $route, $params) = $this->init($uri);
			
			return $this->getOutput($module, $route, $params);
		}
		
		
		
    public function init($uri) {

			$router = new Router();
			$path = $router->match($uri, array_keys($this->routes));

			l("Executing application...");
			l("Exec located route: {$path}.");
			l("FINISHED");
			if(false === $path) {
				throw new Exception("Could not locate {$uri}.");
			}

			
			$route = $this->routes[$path];
			
			l("Will execute $path: </p><pre>".print_r($route,true)."</pre>");
			
			$moduleName = $route["module"];
			l("Module is: {$moduleName}.");


			l("Loading Module...");
			$loader = $this->getLoader();

 
			// Demonstrate that we can instantiate a module
			//  and begin using it.
			$object = $loader->loadObject($moduleName);
    	$func = $route["callback"];

			l("Executing route...<br />Module: {$moduleName}<br />Callback: {$func}.");

    	// dump($object);
    	// Load the routes files, if any.
			// $target->loadFiles();
    	
    	$params = array(
    		"request" => $req,
    		"service1" => null,
    		"service2" => null,
    		"named" => array("foo"=> $bar,"baz" => $pow)
    	);
    	
    	return array($object, $route, $params);
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
    
    
    
    
    /**
     * For HTTP contexts,
     *  `run` should return an HttpResponse object that will be returned to the
     *  webserver context.
     */
    public function run($path) {
    	return $this->runHttp($path);
    	// $this->runCli($cmd, $flags);
    } 



		private function handleErrors() {
		
			/* 
      } catch(PageNotFoundException $e) {
        $resp->setNotFoundStatus();
        $resp->setBody($e->getMessage());
        
      } catch(Exception $e) {

        if($contentType == Http\MIME_APPLICATION_JSON){
            $error = new StdClass();
            $error->error = $e->getMessage();
            $body = json_encode($error);
        } else {
            $body = $e->getMessage();
        }

        $resp->setErrorStatus();
        $resp->setBody($body);
      }
      */
		
		}
    


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



    //Other Methods
    public function secure() { 
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
    
    public function send($resp) {

        $content = $resp->getBody();

        
        $collection = $resp->getHeaderCollection();
        foreach($collection->getHeaders() as $header) {
            header($header->getName() . ": " . $header->getValue());
        }

        http_response_code($resp->getStatusCode());

        if($resp->isFile()){

            $file = $resp->getFile();
            if($file->exists()){

                readfile($file->getPath());

            } else {

                $content = $file->getContent();
                
            }
        }

        print $content;
    }
    
    
    


		public function getLoader() {
			return $this->loader;
		}

		public function getRoutes() {
			return $this->routes;
		}

    //Setters
    public function setModuleLoader($loader){
        $this->loader = $loader;
    }
    
    public function setRequest($req){
        $this->req = $req;
    }
    
    public function setResponse($resp) {
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
    


}