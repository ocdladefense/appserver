<?php

namespace Presentation;



class Component {


    // By default let components be active.
    // They can set themselves to inactive.
    protected $active = true;


    protected static $request;
    
    // Unique name of this component.
    // Used when invoking component().
    protected $name;


    // Content that is renderable as
    // part of the presentation layer.
    protected $renderable;


    // Variables that will be extracted into the
    // component's template file when rendering.
    protected $params = array();


    // Set to true to throw errors 
    // when the component can't be loaded.
    private static $debug = false;
    /**
     * 
     * @params $name The name of this component. Note, this usually refers to the class name.
     * 
     * @param $id A unique id identifying this component on the page.
     * 
     * @param $params Variable to be used when rendering the component.  See toHtml().
     */
    public function __construct($name, $id = null, array $params = array()) {

        $id = $params["id"];
        
        // get widget settings;
        $this->name = $name;
        $this->instance = empty($id) ? ($name . "-component") : $id;
        $this->template = $name;
        $this->params = $params;
    }


    public function active() {
        return $this->active;
    }


	public function getInput($name = null) {

		$req = $this->getRequest();
		$body = $req->getBody();

        // Start with GET; don't let GET variables overwrite
        // data sent up in the body of the request.
        $tmp = !empty($_GET) ? $_GET : array();
        $body = (array)$body;

        foreach($body as $key => $value) {
            $tmp[$key] = $value;
        }

        if(!empty($name) && empty($tmp[$key])) {

            return null;

        } else if(!empty($name) && !empty($tmp[$key])) {

            return $tmp[$name];

        } else if(empty($name) && !empty($tmp)) {

            return (object)$tmp;

        } else {

            return new \stdClass();
        }
	}


    public static function setRequest(\Http\HttpRequest $req) {
        self::$request = $req;
    }

    public function getRequest() {
        return self::$request;
    }

    /**
     * Instantiate a component using it's class name.
     */
    public static function fromName($name, $id = null, $params = array()) {
        $class = ucfirst($name);
        if(!class_exists($class)) {
            throw new ComponentException("PARSE_ERROR: $class cannot be resolved into a valid class name.");
        }

        return func_num_args() > 2 ? new $class($name, $id, $params) : new $class($name, $params);
    }



    public function getStyles() {
        return $this->styles;
    }

    public function getScripts() {
        return $this->scripts;
    }

      


    public function toHtml($params = array()) {

        $params = empty($params) ? $this->params : $params;
        // $props = get_object_vars($this);
        

        $reflection = new \ReflectionClass($this);
        $props = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
    
        // Add public members of this Component instance
        // to the scope so they can be consumed by templates;
        // especially, without `this` keyword.
        foreach($props as $obj) {
            $name = $obj->name;
            $params[$name] = $this->{$name};
        }
        $directory = dirname($reflection->getFileName());

        $path = $directory . "/templates/" . $this->template . ".tpl.php";
        
        if(self::$debug === true && !is_readable($path)) {
            throw new \ComponentException("PATH_RESOLUTION_ERROR: The file does not exist or is not readable: {$path}.");
        }

        extract($params);

        $found = include($path);
    
        if(!$found) return false;
    }

}



class ComponentException extends \Exception {}