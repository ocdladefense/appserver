<?php

namespace Presentation;


// widget("sample"); 







class Component {


    // Content that is renderable as
    // part of the presentation layer.
    protected $renderable;


    protected $params;


    public function __construct($name = "", $params = array()) {
        
        // get widget settings;
        $this->template = $name;
        $this->renderable = "<h2>HELLO WORLD!</h2>";
        $this->params = $params;
    }



    public function __toString() {


    }

    public function getStyles() {
        return array(
            "active" => true,
            "href" => "/content/themes/default/components/drawer/css/drawer.css?bust=001"
        );
    }

    public function getScripts() {
        return array(
            "src" => "/content/themes/default/components/drawer/js/sidebar.js"
        );
    }

      


    public function toHtml($params = null) {
        $params = empty($params) ? $this->params : $params;

        load_template($this->template, $params);
    }

}