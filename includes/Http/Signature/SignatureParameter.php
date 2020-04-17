<?php


namespace Http;

class SignatureParameter extends Parameter {


		

    public function __construct($name, $value) {
    	$this->name = $name;
    	$this->value = $value;
    }
    

    /**
     * All of the custom code for signing our values goes here:
     */
		public function getValue() {
			return $this->value;
		}


}