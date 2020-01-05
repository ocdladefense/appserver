<?php
class HTTPResponse
{
    private $body;
    
    private $headers = array();

		private $statusCode;

    public function __construct($body = null){
    	$this->body = $body;
    }


    //Setters
    public function setBody($content){
        $this->body = $content;
    }
    public function setContentType($route){
        //Add the preferred content type to the headers array
        if(isset($route["Content-Type"]) && $route["Content-Type"] == "json"){
            $this->headers["Content-Type"] = "application/json; charset=utf-8";
        }
        else{
            $this->headers["Content-Type"] = "text/html; charset=utf-8";
        }

    }
    public function setHeaders($headers){
        $this->headers = $headers;
    }
    
    public function setHeader($name,$value) {
    	$this->headers[$name] = $value;
    }
    
    public function setStatusCode($code) {
    	$this->statusCode = $code;
    }
    
    public function setErrorStatus(){
    	$this->statusCode = "HTTP/1.1 500 Internal Server Error";
    }
    
    public function setNotFoundStatus(){
    	$this->statusCode = "HTTP/1.1 400 Page Not Found";
    }

    //Getters
    public function getBody(){
        return $this->body;
    }
    
    public function getHeader($headerName){
    	if(!isset($this->headers[$headerName])) {
    		return null;
    	}
    	
			return $this->headers[$headerName];
    }
    
    
    public function getHeaders(){
        return $this->headers;
    }
    
    
    public function getPhpArray(){
        // Parsing the HTTP Response; by parsing we just mean the data has a known format and we can retrieve certain things from the Response.
			return json_decode($this->body, true);
    }

    //other methods

    //Send the value of the headers array at the key of content-type 
    public function sendHeaders(){

			foreach($this->headers as $headerName => $headerValue){
				header($headerName.": ".$headerValue);
			}
			if($this->statusCode != null){
				header($this->statusCode);
			}
    }
    public function __toString(){
        return $this->body;
    }
    public static function formatResponseBody($content, $contentType){

        if(strpos($contentType,"json")){
            if(is_array($content) || is_object($content)){
                $out = json_encode($content);
            }
            else{
                $out = json_encode(array("content" => $content));
            }  
        }
        else{
            $out = $content;
        }
        return $out;
    }
}
?>