<?php



namespace Http;


class HttpHeader {


    const SEPERATOR = ": ";
    
    
    private $name;
    
    
    private $value;




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
    
    
    
    /**
     * Convert an array of HttpHeader objects
     *  to a PHP keyed array.
     */
		public static function toArray(array $headers) {
			return array_map(function($header) {
				return $header->getName() . ": ".$header->getValue();
			},$headers);
		}
		
		
		public static function fromArray(array $headers) {
			$tmp = array();
			foreach($headers as $key => $value) {
				return new HttpHeader($key,$value);
			}
			
			return $tmp;
		}
}