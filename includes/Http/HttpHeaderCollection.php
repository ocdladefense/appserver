<?php



namespace Http;


class HttpHeaderCollection {


    const SEPERATOR = ": ";

		protected $headers = array();
		
		
		protected $stripOwsFromHeaders = array();
	
	

    public function __construct($headers = null){
        $this->headers = null == $headers ? array() : $headers;
    }

		
	public function addHeader($header) {
		$this->headers[] = $header;
	}

	public function addHeaders(array $headers) {
		$this->headers = array_merge($this->headers,$headers);
	}
		
		
    public function getHeaders() {
    	return $this->headers;
    }

    
	/**
	 * Return the header with the specified name.
	 *  If more than one header with this name
	 *  exists, then return the last one.
	 *
	 *  http spec supports multiple headers with the same name,
	 *  however, multiple pseudo-headers of the same name are prohibited.
	 */
	public function getHeader( $name ) {

		$filter = function($header) use ($name) {
			return $name == $header->getName();			
		}; 
		
		$tmp = array_filter($this->headers, $filter);

		if(null == $tmp || count($tmp) < 1) return null;
		
		$arrange = array_values($tmp);
		

		return $arrange[count($arrange)-1];
	}
    
    
    
    
	public function setStripOwsFromHeaders($names = array()) {
		$this->stripOwsFromHeaders = $names;
	}

    
	public function getHeadersAsArray() {
			$strip = $this->stripOwsFromHeaders;
			
			return array_map(function($header) use($strip) {

				if(in_array($header->getName(),$strip)) {
					return $header->getName() . ":".$header->getValue();
				} else {
					return $header->getName() . ": ".$header->getValue();
				}
			}, $this->headers);
	}
  
    

	public static function fromArray(array $headers) {
		$tmp = array();
		foreach($headers as $key => $value) {
			return new HttpHeader($key,$value);
		}
		
		return $tmp;
	}
}