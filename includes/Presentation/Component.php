<?php

namespace Presentation;



class Component {

    // Unique name of this component.
    // Used when invoking component().
    protected $name;


    // Content that is renderable as
    // part of the presentation layer.
    protected $renderable;


    // Variables that will be extracted into the
    // component's template file when rendering.
    protected $params = array();



    /**
     * 
     * @params $name The name of this component. Note, this usually refers to the class name.
     * 
     * @param $id A unique id identifying this component on the page.
     * 
     * @param $params Variable to be used when rendering the component.  See toHtml().
     */
    public function __construct($name, $id = "", $params = array()) {
        
        // get widget settings;
        $this->name = $name;
        $this->instance = $id;
        $this->template = $name;
        $this->params = $params;
    }



    /**
     * Instantiate a component using it's class name.
     */
    public static function fromName($name,$id,$params) {
        $class = ucfirst($name)."Component";
        if(!class_exists($class)) {
            throw new \Exception("PARSE_ERROR: $class cannot be resolved into a valid class name.");
        }
        return new $class($name,$id,$params);
    }



    public function getStyles() {
        return $this->styles;
    }

    public function getScripts() {
        return $this->scripts;
    }

      


    public function toHtml($params = null) {
        $params = empty($params) ? $this->params : $params;

        load_template($this->template, $params);
    }

}