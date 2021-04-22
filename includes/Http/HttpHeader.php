<?php



namespace Http;


class HttpHeader {


    const SEPERATOR = ": ";
    
    // For complex headers like Content-Type: text/html; encoding=utf-8. 
    const PARAMS_MARKER = ";";
    
    
    private $name;
    
    
    private $value;


		/**
		 * Attempt to evaluate whether this header has the given value.
		 */
		public function equals($value, $strict = false) {
			$parts = explode(self::PARAMS_MARKER, $this->value);
			$base = $parts[0];
			
			return !$strict ? strToLower($base) == strToLower($value) : $base === $value;
		}
		

    public function __construct($name,$value){
        $this->name = $name;
        $this->value = $value;
    }
    
    
    public function getName() {
    	return $this->name;
    }
    
    
    public function getValue() {
    	return $this->value;
    }
    
    public function getParameters(){

        $kvp = array();
        
        $params = explode(";", $this->value);

        foreach($params as $p){

            $temp1 = trim($p);

            $temp2 = explode("=", $temp1);

            if(count($temp2) < 2) continue;

            $key = $temp2[0];
            $value = trim($temp2[1], '"');

            $kvp[$key] = $value;

        }

        return $kvp;
    }
    	
    public static function fromArray($headers) {
        $tmp = array();
        foreach($headers as $key => $value) {
            $tmp[] = new HttpHeader($key,$value);
        }
        return $tmp;
    }
    
    
    public function __toString() {
    	return $this->name . ": " . $this->value;
    }
}