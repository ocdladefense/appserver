<?php
class HTTPResponse
{
    private $body;
    private $headers = array();
    private $content;

    public function __construct(){}


    //Setters
    public function setBody($content){
        $this->body = $content;
    }
    public function setContentType($route){
        //Add the preferred content type to the headers array
        if($route["content-type"] == "json"){
            $this->headers["Content-type"] = "application/json; charset=utf-8";
        }
        if($route["content-type"] == "text"){
            $this->headers["Content-type"] = "text/html; charset=utf-8";
        }
    }
    public function setHeaders($headers){
        $this->headers = $headers;
    }

    //Getters
    public function getBody(){
        return $this->body;
    }
    public function getHeader($headerName){
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
    }
    public function __toString(){
        return $this->body;
    }
    public static function formatResponseBody($content, $contentType){

        if(strpos($contentType,"json")){
            if(is_array($content)){
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