<?php


namespace Http;

class Parameter {


    const KEY_VALUE_SEPERATOR = "=";
    
    const VALUES_ENCLOSED_IN = '"';
    
    
    // The name of the parameter.
    // Never use this value directly.
    // Instead, always use ->getName() so that sub-classes
    // Can override the functionality.
    protected $name;
    
    // The value of the parameter.
    // Never use this value directly.
    // Instead, always use ->getValue() so that sub-classes
    // Can override the functionality.
    protected $value;



    public function __construct($name,$value){
        $this->name = $name;
        $this->value = $value."";
    }

    public function getName(){
       return $this->name;
    }

    public function getValue(){
        return $this->value;
    }

    //Signature bag calls it
    public function __toString(){
        return ($this->getName() . self::KEY_VALUE_SEPERATOR
        . self::VALUES_ENCLOSED_IN . $this->getValue() . self::VALUES_ENCLOSED_IN);
    }

}